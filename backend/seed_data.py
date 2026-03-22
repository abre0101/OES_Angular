"""
Seed script — populates the database with realistic DMU sample data.
Run with: python seed_data.py
"""
import os
import django
os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'oes.settings')
django.setup()

from datetime import datetime, timedelta
from users.models import User
from courses.models import Faculty, Department, Course, CourseAssignment, StudentEnrollment
from exams.models import Question, Exam, ExamResult, Schedule, Topic, Notification

# ── helpers ──────────────────────────────────────────────────────────────────
def clean(*models):
    for m in models:
        m.objects.all().delete()

def dt(days_offset=0, hour=9):
    return datetime.utcnow().replace(hour=hour, minute=0, second=0, microsecond=0) \
           + timedelta(days=days_offset)

# ── wipe existing seed data ───────────────────────────────────────────────────
print("Cleaning existing data...")
clean(Notification, Schedule, ExamResult, Exam, Question, Topic,
      StudentEnrollment, CourseAssignment, Course, Department, Faculty, User)

# ═══════════════════════════════════════════════════════════════════════════════
# 1. USERS
# ═══════════════════════════════════════════════════════════════════════════════
print("Creating users...")

def make_user(data):
    pw = data.pop('password')
    u = User(**data, is_active=True)
    u.set_password(pw)
    u.save()
    return u

admin = make_user({'username':'admin','email':'admin@dmu.edu.et','password':'admin123',
    'first_name':'Abebe','last_name':'Girma','role':'admin'})

# Department Heads
dh_cs = make_user({'username':'depthead1','email':'depthead1@dmu.edu.et','password':'depthead123',
    'first_name':'Tigist','last_name':'Haile','role':'department_head',
    'employee_id':'EMP002','department':'Computer Science','faculty':'Engineering'})

dh_math = make_user({'username':'depthead2','email':'depthead2@dmu.edu.et','password':'depthead123',
    'first_name':'Mulugeta','last_name':'Worku','role':'department_head',
    'employee_id':'EMP003','department':'Mathematics','faculty':'Natural Sciences'})

# Instructors
inst1 = make_user({'username':'instructor1','email':'instructor1@dmu.edu.et','password':'instructor123',
    'first_name':'Tesfaye','last_name':'Bekele','role':'instructor',
    'employee_id':'EMP010','department':'Computer Science','faculty':'Engineering'})

inst2 = make_user({'username':'instructor2','email':'instructor2@dmu.edu.et','password':'instructor123',
    'first_name':'Hiwot','last_name':'Alemu','role':'instructor',
    'employee_id':'EMP011','department':'Computer Science','faculty':'Engineering'})

inst3 = make_user({'username':'instructor3','email':'instructor3@dmu.edu.et','password':'instructor123',
    'first_name':'Dawit','last_name':'Mengistu','role':'instructor',
    'employee_id':'EMP012','department':'Mathematics','faculty':'Natural Sciences'})

# Students
students_data = [
    ('student1','Selam','Tadesse','STU001',2),
    ('student2','Biruk','Assefa','STU002',2),
    ('student3','Meron','Kebede','STU003',3),
    ('student4','Yonas','Girma','STU004',1),
    ('student5','Hana','Tesfaye','STU005',3),
    ('student6','Abel','Hailu','STU006',2),
    ('student7','Sara','Bekele','STU007',1),
    ('student8','Naol','Demeke','STU008',4),
]
students = []
for i, (uname, fn, ln, sid, yr) in enumerate(students_data, 1):
    s = make_user({'username': uname, 'email': f'{uname}@dmu.edu.et', 'password': 'student123',
        'first_name': fn, 'last_name': ln, 'role': 'student',
        'student_id': sid, 'year_of_study': yr, 'department': 'Computer Science',
        'faculty': 'Engineering'})
    students.append(s)

math_students = []
for i in range(1, 4):
    s = make_user({'username': f'mathstudent{i}', 'email': f'mathstudent{i}@dmu.edu.et',
        'password': 'student123', 'first_name': f'Math{i}', 'last_name': 'Student',
        'role': 'student', 'student_id': f'MST00{i}', 'year_of_study': 2,
        'department': 'Mathematics', 'faculty': 'Natural Sciences'})
    math_students.append(s)

print(f"  Users: {User.objects.count()}")

# ═══════════════════════════════════════════════════════════════════════════════
# 2. FACULTIES & DEPARTMENTS
# ═══════════════════════════════════════════════════════════════════════════════
print("Creating faculties & departments...")

fac_eng = Faculty(name='Faculty of Engineering', description='Engineering and Technology').save()
fac_sci = Faculty(name='Faculty of Natural Sciences', description='Sciences and Mathematics').save()
fac_bus = Faculty(name='Faculty of Business', description='Business and Economics').save()

dept_cs = Department(name='Computer Science', faculty=fac_eng,
    head_id=str(dh_cs.id), head_name=dh_cs.full_name,
    description='Undergraduate and postgraduate CS programs').save()

dept_it = Department(name='Information Technology', faculty=fac_eng,
    description='IT and networking programs').save()

dept_math = Department(name='Mathematics', faculty=fac_sci,
    head_id=str(dh_math.id), head_name=dh_math.full_name,
    description='Pure and applied mathematics').save()

dept_phys = Department(name='Physics', faculty=fac_sci,
    description='Physics and applied physics').save()

# ═══════════════════════════════════════════════════════════════════════════════
# 3. COURSES
# ═══════════════════════════════════════════════════════════════════════════════
print("Creating courses...")

cs_courses_data = [
    ('CS101', 'Introduction to Programming', 3),
    ('CS201', 'Data Structures and Algorithms', 3),
    ('CS301', 'Database Systems', 3),
    ('CS302', 'Operating Systems', 3),
    ('CS401', 'Software Engineering', 3),
    ('CS402', 'Computer Networks', 3),
]
math_courses_data = [
    ('MATH101', 'Calculus I', 4),
    ('MATH201', 'Linear Algebra', 3),
    ('MATH301', 'Probability and Statistics', 3),
]

cs_courses = [Course(code=c, name=n, department=dept_cs, credit_hours=cr,
    description=f'{n} course for CS students', is_active=True).save()
    for c, n, cr in cs_courses_data]

math_courses = [Course(code=c, name=n, department=dept_math, credit_hours=cr,
    description=f'{n} course', is_active=True).save()
    for c, n, cr in math_courses_data]

# ═══════════════════════════════════════════════════════════════════════════════
# 4. COURSE ASSIGNMENTS
# ═══════════════════════════════════════════════════════════════════════════════
print("Creating course assignments...")

for course in cs_courses[:3]:
    CourseAssignment(course_id=str(course.id), course_name=course.name,
        instructor_id=str(inst1.id), instructor_name=inst1.full_name,
        academic_year='2024/2025', semester='Semester 1').save()

for course in cs_courses[3:]:
    CourseAssignment(course_id=str(course.id), course_name=course.name,
        instructor_id=str(inst2.id), instructor_name=inst2.full_name,
        academic_year='2024/2025', semester='Semester 1').save()

for course in math_courses:
    CourseAssignment(course_id=str(course.id), course_name=course.name,
        instructor_id=str(inst3.id), instructor_name=inst3.full_name,
        academic_year='2024/2025', semester='Semester 1').save()

# ═══════════════════════════════════════════════════════════════════════════════
# 5. STUDENT ENROLLMENTS
# ═══════════════════════════════════════════════════════════════════════════════
print("Creating enrollments...")

for student in students:
    for course in cs_courses:
        StudentEnrollment(student_id=str(student.id), student_name=student.full_name,
            course_id=str(course.id), course_name=course.name).save()

for student in math_students:
    for course in math_courses:
        StudentEnrollment(student_id=str(student.id), student_name=student.full_name,
            course_id=str(course.id), course_name=course.name).save()

# ═══════════════════════════════════════════════════════════════════════════════
# 6. TOPICS
# ═══════════════════════════════════════════════════════════════════════════════
print("Creating topics...")

prog_course = cs_courses[0]
ds_course   = cs_courses[1]
db_course   = cs_courses[2]

topics_data = [
    (prog_course, ['Variables & Data Types', 'Control Flow', 'Functions', 'Arrays & Lists']),
    (ds_course,   ['Arrays & Linked Lists', 'Stacks & Queues', 'Trees & Graphs', 'Sorting Algorithms']),
    (db_course,   ['Relational Model', 'SQL Basics', 'Normalization', 'Transactions']),
]
all_topics = {}
for course, tnames in topics_data:
    all_topics[str(course.id)] = []
    for tname in tnames:
        t = Topic(name=tname, course_id=str(course.id), course_name=course.name,
            created_by_id=str(inst1.id)).save()
        all_topics[str(course.id)].append(t)

# ═══════════════════════════════════════════════════════════════════════════════
# 7. QUESTIONS
# ═══════════════════════════════════════════════════════════════════════════════
print("Creating questions...")

def make_q(course, topic, text, a, b, c, d, correct, pts=1.0, practice=False, explanation=''):
    return Question(
        course_id=str(course.id), course_name=course.name,
        topic=topic, text=text,
        question_type='mcq',
        option_a=a, option_b=b, option_c=c, option_d=d,
        correct_answer=correct, points=pts,
        explanation=explanation, is_practice=practice,
        created_by_id=str(inst1.id), created_by_name=inst1.full_name
    ).save()

def make_tf(course, topic, text, correct, pts=1.0, practice=False):
    return Question(
        course_id=str(course.id), course_name=course.name,
        topic=topic, text=text,
        question_type='true_false',
        option_a='True', option_b='False',
        correct_answer=correct, points=pts,
        is_practice=practice,
        created_by_id=str(inst1.id), created_by_name=inst1.full_name
    ).save()

# ── CS101 Questions ──
prog_qs = [
    make_q(prog_course,'Variables & Data Types',
        'Which of the following is NOT a primitive data type in most programming languages?',
        'int','float','string','array','D',1.0,False,'Arrays are composite/reference types.'),
    make_q(prog_course,'Variables & Data Types',
        'What is the output of: x = 5; y = 2; print(x // y)?',
        '2.5','2','3','2.0','B',1.0,False,'// is integer (floor) division.'),
    make_q(prog_course,'Control Flow',
        'Which loop is guaranteed to execute at least once?',
        'for loop','while loop','do-while loop','foreach loop','C',1.0),
    make_q(prog_course,'Control Flow',
        'What does the "break" statement do inside a loop?',
        'Skips current iteration','Exits the loop entirely','Restarts the loop','None of the above','B',1.0),
    make_q(prog_course,'Functions',
        'What is a function that calls itself called?',
        'Iteration','Recursion','Overloading','Polymorphism','B',1.0,False,'Recursion is self-referential function calls.'),
    make_q(prog_course,'Functions',
        'Which keyword is used to return a value from a function?',
        'send','output','return','yield','C',1.0),
    make_q(prog_course,'Arrays & Lists',
        'What is the index of the first element in a zero-indexed array?',
        '-1','0','1','Depends on language','B',1.0),
    make_q(prog_course,'Arrays & Lists',
        'What is the time complexity of accessing an element by index in an array?',
        'O(n)','O(log n)','O(1)','O(n²)','C',1.0),
    make_tf(prog_course,'Variables & Data Types',
        'In Python, variables must be declared with a type before use.','False',1.0),
    make_tf(prog_course,'Control Flow',
        'A "while" loop can iterate zero times if the condition is false initially.','True',1.0),
]

# ── CS201 Questions ──
ds_qs = [
    make_q(ds_course,'Arrays & Linked Lists',
        'What is the time complexity of inserting at the beginning of a singly linked list?',
        'O(n)','O(1)','O(log n)','O(n²)','B',1.0),
    make_q(ds_course,'Arrays & Linked Lists',
        'Which data structure uses LIFO (Last In First Out) order?',
        'Queue','Stack','Linked List','Tree','B',1.0),
    make_q(ds_course,'Stacks & Queues',
        'Which operation removes an element from the front of a queue?',
        'push','pop','enqueue','dequeue','D',1.0),
    make_q(ds_course,'Stacks & Queues',
        'What is the time complexity of push and pop operations on a stack?',
        'O(n)','O(log n)','O(1)','O(n log n)','C',1.0),
    make_q(ds_course,'Trees & Graphs',
        'In a binary search tree, where are smaller values stored relative to a node?',
        'Right subtree','Left subtree','Root','Random','B',1.0),
    make_q(ds_course,'Trees & Graphs',
        'What traversal visits nodes in Left-Root-Right order?',
        'Pre-order','Post-order','In-order','Level-order','C',1.0),
    make_q(ds_course,'Sorting Algorithms',
        'What is the average time complexity of QuickSort?',
        'O(n)','O(n²)','O(n log n)','O(log n)','C',1.0),
    make_q(ds_course,'Sorting Algorithms',
        'Which sorting algorithm is stable and has O(n log n) worst-case complexity?',
        'QuickSort','HeapSort','MergeSort','BubbleSort','C',1.0),
    make_tf(ds_course,'Arrays & Linked Lists',
        'A doubly linked list allows traversal in both directions.','True',1.0),
    make_tf(ds_course,'Sorting Algorithms',
        'BubbleSort has O(n log n) time complexity in the worst case.','False',1.0),
]

# ── CS301 Questions ──
db_qs = [
    make_q(db_course,'Relational Model',
        'What is a primary key?',
        'A key that can be NULL','A unique identifier for each record',
        'A foreign reference','A composite attribute','B',1.0),
    make_q(db_course,'Relational Model',
        'What does a foreign key do?',
        'Uniquely identifies a row','Links two tables together',
        'Encrypts data','Indexes a column','B',1.0),
    make_q(db_course,'SQL Basics',
        'Which SQL clause filters rows after grouping?',
        'WHERE','HAVING','GROUP BY','ORDER BY','B',1.0),
    make_q(db_course,'SQL Basics',
        'Which SQL command retrieves data from a table?',
        'INSERT','UPDATE','SELECT','DELETE','C',1.0),
    make_q(db_course,'Normalization',
        'Which normal form eliminates partial dependencies?',
        '1NF','2NF','3NF','BCNF','B',1.0),
    make_q(db_course,'Normalization',
        'A table is in 3NF if it is in 2NF and has no:',
        'Partial dependencies','Transitive dependencies',
        'Multi-valued attributes','Repeating groups','B',1.0),
    make_q(db_course,'Transactions',
        'Which ACID property ensures a transaction is fully completed or not at all?',
        'Consistency','Isolation','Durability','Atomicity','D',1.0),
    make_q(db_course,'Transactions',
        'What does the SQL ROLLBACK command do?',
        'Saves changes permanently','Undoes uncommitted changes',
        'Deletes a table','Creates a savepoint','B',1.0),
    make_tf(db_course,'SQL Basics',
        'The SQL WHERE clause can be used with SELECT, UPDATE, and DELETE statements.','True',1.0),
    make_tf(db_course,'Normalization',
        'A table in 1NF can have repeating groups of columns.','False',1.0),
]

# Practice questions
for course, qs_list in [(prog_course, prog_qs[:5]), (ds_course, ds_qs[:5]), (db_course, db_qs[:5])]:
    for q in qs_list:
        q.is_practice = True
        q.save()

print(f"  Questions: {Question.objects.count()}")

# ═══════════════════════════════════════════════════════════════════════════════
# 8. EXAMS
# ═══════════════════════════════════════════════════════════════════════════════
print("Creating exams...")

def make_exam(title, course, instructor, question_list, status, duration=60,
              start_offset=1, end_offset=2):
    total = sum(q.points for q in question_list)
    e = Exam(
        title=title, course_id=str(course.id), course_name=course.name,
        created_by_id=str(instructor.id), created_by_name=instructor.full_name,
        status=status, duration_minutes=duration,
        total_marks=total, pass_marks=round(total * 0.5, 1),
        instructions='Read all questions carefully. No cheating. Submit before time runs out.',
        start_time=dt(start_offset, 9), end_time=dt(end_offset, 17),
        question_ids=[str(q.id) for q in question_list],
    )
    if status in ('pending', 'approved', 'active', 'completed'):
        e.submitted_at = dt(-1)
    if status in ('approved', 'active', 'completed'):
        e.reviewed_by_id = str(dh_cs.id)
        e.reviewed_by_name = dh_cs.full_name
        e.reviewed_at = dt(0)
        e.review_comment = 'Approved. Questions are well-structured.'
    e.save()
    return e

exam1 = make_exam('CS101 Midterm Exam', prog_course, inst1, prog_qs, 'active', 60, 1, 3)
exam2 = make_exam('CS201 Midterm Exam', ds_course, inst1, ds_qs, 'active', 90, 2, 4)
exam3 = make_exam('CS301 Midterm Exam', db_course, inst1, db_qs, 'approved', 75, 5, 7)
exam4 = make_exam('CS101 Final Exam', prog_course, inst2, prog_qs[5:], 'pending', 120, 10, 12)
exam5 = make_exam('CS201 Quiz 1', ds_course, inst1, ds_qs[:5], 'completed', 30, -10, -9)
exam6 = make_exam('CS301 Quiz 1', db_course, inst2, db_qs[:5], 'draft', 30, 15, 16)

print(f"  Exams: {Exam.objects.count()}")

# ═══════════════════════════════════════════════════════════════════════════════
# 9. EXAM RESULTS
# ═══════════════════════════════════════════════════════════════════════════════
print("Creating exam results...")

import random
random.seed(42)

def make_result(exam, student, questions, score_pct):
    answers = []
    score = 0
    for q in questions:
        options = ['A', 'B', 'C', 'D']
        if random.random() < score_pct:
            chosen = q.correct_answer
            score += q.points
        else:
            wrong = [o for o in options if o != q.correct_answer]
            chosen = random.choice(wrong)
        answers.append({'question_id': str(q.id), 'selected': chosen,
                        'correct': q.correct_answer, 'is_correct': chosen == q.correct_answer})
    total = exam.total_marks
    pct = round((score / total) * 100, 1) if total else 0
    ExamResult(
        exam_id=str(exam.id), exam_title=exam.title,
        student_id=str(student.id), student_name=student.full_name,
        score=score, total_marks=total, percentage=pct,
        passed=pct >= 50, answers=answers,
        started_at=dt(-10, 9), submitted_at=dt(-10, 10)
    ).save()

score_profiles = [0.9, 0.75, 0.85, 0.6, 0.95, 0.55, 0.7, 0.8]
for student, pct in zip(students, score_profiles):
    make_result(exam5, student, ds_qs[:5], pct)

for student, pct in zip(students[:4], [0.8, 0.65, 0.9, 0.7]):
    make_result(exam1, student, prog_qs, pct)

print(f"  Results: {ExamResult.objects.count()}")

# ═══════════════════════════════════════════════════════════════════════════════
# 10. SCHEDULES
# ═══════════════════════════════════════════════════════════════════════════════
print("Creating schedules...")

schedules_data = [
    (exam1, inst1, 'Lab 101', 1),
    (exam2, inst1, 'Hall A', 2),
    (exam3, inst2, 'Lab 102', 5),
    (exam4, inst2, 'Main Hall', 10),
]
for exam, inst, loc, offset in schedules_data:
    Schedule(
        exam_id=str(exam.id), exam_title=exam.title,
        course_id=exam.course_id, course_name=exam.course_name,
        instructor_id=str(inst.id), instructor_name=inst.full_name,
        start_time=dt(offset, 9), end_time=dt(offset, 11),
        location=loc
    ).save()

# ═══════════════════════════════════════════════════════════════════════════════
# 11. NOTIFICATIONS
# ═══════════════════════════════════════════════════════════════════════════════
print("Creating notifications...")

for student in students:
    Notification(user_id=str(student.id), user_type='student',
        title='New Exam Available', message=f'{exam1.title} is now available. Good luck!',
        notification_type='exam_scheduled', link='/student/exams').save()
    Notification(user_id=str(student.id), user_type='student',
        title='Results Published', message=f'Your results for {exam5.title} are ready.',
        notification_type='results_ready', is_read=False, link='/student/results').save()

Notification(user_id=str(inst1.id), user_type='instructor',
    title='Exam Approved', message=f'{exam1.title} has been approved by the department head.',
    notification_type='general').save()

Notification(user_id=str(dh_cs.id), user_type='department_head',
    title='Exam Pending Review', message=f'{exam4.title} is awaiting your approval.',
    notification_type='general').save()

# ═══════════════════════════════════════════════════════════════════════════════
print("\n✅ Seed complete!")
print(f"   Users:        {User.objects.count()}")
print(f"   Faculties:    {Faculty.objects.count()}")
print(f"   Departments:  {Department.objects.count()}")
print(f"   Courses:      {Course.objects.count()}")
print(f"   Assignments:  {CourseAssignment.objects.count()}")
print(f"   Enrollments:  {StudentEnrollment.objects.count()}")
print(f"   Topics:       {Topic.objects.count()}")
print(f"   Questions:    {Question.objects.count()}")
print(f"   Exams:        {Exam.objects.count()}")
print(f"   Results:      {ExamResult.objects.count()}")
print(f"   Schedules:    {Schedule.objects.count()}")
print(f"   Notifications:{Notification.objects.count()}")
print("\nCredentials (all roles):")
print("  admin        / admin123")
print("  depthead1    / depthead123   (or depthead1@dmu.edu.et)")
print("  depthead2    / depthead123")
print("  instructor1  / instructor123")
print("  instructor2  / instructor123")
print("  instructor3  / instructor123")
print("  student1–8   / student123")
print("  mathstudent1–3 / student123")
