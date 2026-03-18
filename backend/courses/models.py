import mongoengine as me
from datetime import datetime


class Faculty(me.Document):
    name = me.StringField(required=True, unique=True, max_length=200)
    description = me.StringField(default='')
    created_at = me.DateTimeField(default=datetime.utcnow)

    meta = {'collection': 'faculties'}

    def to_dict(self):
        return {'id': str(self.id), 'name': self.name, 'description': self.description,
                'created_at': self.created_at.isoformat()}


class Department(me.Document):
    name = me.StringField(required=True, max_length=200)
    faculty = me.ReferenceField(Faculty, required=True)
    head_id = me.StringField(default='')   # stores User id string
    head_name = me.StringField(default='')
    description = me.StringField(default='')
    created_at = me.DateTimeField(default=datetime.utcnow)

    meta = {'collection': 'departments'}

    def to_dict(self):
        return {
            'id': str(self.id), 'name': self.name,
            'faculty': str(self.faculty.id) if self.faculty else None,
            'faculty_name': self.faculty.name if self.faculty else '',
            'head_id': self.head_id, 'head_name': self.head_name,
            'description': self.description,
            'created_at': self.created_at.isoformat(),
        }


class Course(me.Document):
    code = me.StringField(required=True, unique=True, max_length=20)
    name = me.StringField(required=True, max_length=200)
    department = me.ReferenceField(Department, required=True)
    description = me.StringField(default='')
    credit_hours = me.IntField(default=3)
    is_active = me.BooleanField(default=True)
    created_at = me.DateTimeField(default=datetime.utcnow)

    meta = {'collection': 'courses'}

    def to_dict(self):
        return {
            'id': str(self.id), 'code': self.code, 'name': self.name,
            'department': str(self.department.id) if self.department else None,
            'department_name': self.department.name if self.department else '',
            'description': self.description, 'credit_hours': self.credit_hours,
            'is_active': self.is_active, 'created_at': self.created_at.isoformat(),
        }


class CourseAssignment(me.Document):
    """Instructor assigned to a course."""
    course_id = me.StringField(required=True)
    course_name = me.StringField(default='')
    instructor_id = me.StringField(required=True)
    instructor_name = me.StringField(default='')
    academic_year = me.StringField(default='')
    semester = me.StringField(default='')
    assigned_at = me.DateTimeField(default=datetime.utcnow)

    meta = {'collection': 'course_assignments'}

    def to_dict(self):
        return {
            'id': str(self.id), 'course_id': self.course_id,
            'course_name': self.course_name, 'instructor_id': self.instructor_id,
            'instructor_name': self.instructor_name, 'academic_year': self.academic_year,
            'semester': self.semester,
        }


class StudentEnrollment(me.Document):
    student_id = me.StringField(required=True)
    student_name = me.StringField(default='')
    course_id = me.StringField(required=True)
    course_name = me.StringField(default='')
    enrolled_at = me.DateTimeField(default=datetime.utcnow)

    meta = {'collection': 'student_enrollments'}

    def to_dict(self):
        return {
            'id': str(self.id), 'student_id': self.student_id,
            'student_name': self.student_name, 'course_id': self.course_id,
            'course_name': self.course_name,
            'enrolled_at': self.enrolled_at.isoformat(),
        }
