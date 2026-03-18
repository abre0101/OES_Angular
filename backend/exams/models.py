import mongoengine as me
from datetime import datetime


class Question(me.Document):
    TYPES = ('mcq', 'true_false', 'short_answer')

    course_id = me.StringField(required=True)
    course_name = me.StringField(default='')
    topic = me.StringField(default='')
    text = me.StringField(required=True)
    question_type = me.StringField(choices=TYPES, default='mcq')
    option_a = me.StringField(default='')
    option_b = me.StringField(default='')
    option_c = me.StringField(default='')
    option_d = me.StringField(default='')
    correct_answer = me.StringField(required=True)
    points = me.FloatField(default=1.0)
    explanation = me.StringField(default='')
    is_practice = me.BooleanField(default=False)
    created_by_id = me.StringField(default='')
    created_by_name = me.StringField(default='')
    created_at = me.DateTimeField(default=datetime.utcnow)
    updated_at = me.DateTimeField(default=datetime.utcnow)

    meta = {'collection': 'questions'}

    def to_dict(self, hide_answer=False):
        d = {
            'id': str(self.id), 'course_id': self.course_id,
            'course_name': self.course_name, 'topic': self.topic,
            'question_text': self.text, 'text': self.text,
            'question_type': self.question_type,
            'option_a': self.option_a, 'option_b': self.option_b,
            'option_c': self.option_c, 'option_d': self.option_d,
            'points': self.points, 'explanation': self.explanation,
            'is_practice': self.is_practice,
            'created_by_id': self.created_by_id,
            'created_by_name': self.created_by_name,
            'created_at': self.created_at.isoformat(),
        }
        if not hide_answer:
            d['correct_answer'] = self.correct_answer
        return d


class Exam(me.Document):
    STATUSES = ('draft', 'pending', 'approved', 'rejected', 'active', 'completed')

    title = me.StringField(required=True)
    course_id = me.StringField(required=True)
    course_name = me.StringField(default='')
    created_by_id = me.StringField(required=True)
    created_by_name = me.StringField(default='')
    status = me.StringField(choices=STATUSES, default='draft')
    duration_minutes = me.IntField(default=60)
    total_marks = me.FloatField(default=0)
    pass_marks = me.FloatField(default=0)
    instructions = me.StringField(default='')
    start_time = me.DateTimeField(null=True)
    end_time = me.DateTimeField(null=True)
    question_ids = me.ListField(me.StringField(), default=list)
    submitted_at = me.DateTimeField(null=True)
    reviewed_by_id = me.StringField(default='')
    reviewed_by_name = me.StringField(default='')
    reviewed_at = me.DateTimeField(null=True)
    review_comment = me.StringField(default='')
    created_at = me.DateTimeField(default=datetime.utcnow)
    updated_at = me.DateTimeField(default=datetime.utcnow)

    meta = {'collection': 'exams'}

    def to_dict(self):
        return {
            'id': str(self.id), 'title': self.title,
            'course_id': self.course_id, 'course_name': self.course_name,
            'created_by_id': self.created_by_id, 'created_by_name': self.created_by_name,
            'status': self.status, 'duration_minutes': self.duration_minutes,
            'total_marks': self.total_marks, 'pass_marks': self.pass_marks,
            'instructions': self.instructions,
            'start_time': self.start_time.isoformat() if self.start_time else None,
            'end_time': self.end_time.isoformat() if self.end_time else None,
            'question_count': len(self.question_ids),
            'submitted_at': self.submitted_at.isoformat() if self.submitted_at else None,
            'reviewed_by_name': self.reviewed_by_name,
            'reviewed_at': self.reviewed_at.isoformat() if self.reviewed_at else None,
            'review_comment': self.review_comment,
            'created_at': self.created_at.isoformat(),
        }


class ExamResult(me.Document):
    exam_id = me.StringField(required=True)
    exam_title = me.StringField(default='')
    student_id = me.StringField(required=True)
    student_name = me.StringField(default='')
    score = me.FloatField(default=0)
    total_marks = me.FloatField(default=0)
    percentage = me.FloatField(default=0)
    passed = me.BooleanField(default=False)
    answers = me.ListField(me.DictField(), default=list)
    started_at = me.DateTimeField(default=datetime.utcnow)
    submitted_at = me.DateTimeField(null=True)

    meta = {'collection': 'exam_results'}

    def to_dict(self):
        return {
            'id': str(self.id), 'exam_id': self.exam_id, 'exam_title': self.exam_title,
            'student_id': self.student_id, 'student_name': self.student_name,
            'score': self.score, 'total_marks': self.total_marks,
            'percentage': self.percentage, 'passed': self.passed,
            'answers': self.answers,
            'started_at': self.started_at.isoformat(),
            'submitted_at': self.submitted_at.isoformat() if self.submitted_at else None,
        }


class TechnicalIssue(me.Document):
    STATUSES = ('open', 'in_progress', 'resolved')

    title = me.StringField(required=True)
    description = me.StringField(required=True)
    status = me.StringField(choices=STATUSES, default='open')
    reported_by_id = me.StringField(required=True)
    reported_by_name = me.StringField(default='')
    exam_id = me.StringField(default='')
    created_at = me.DateTimeField(default=datetime.utcnow)
    resolved_at = me.DateTimeField(null=True)

    meta = {'collection': 'technical_issues'}

    def to_dict(self):
        return {
            'id': str(self.id), 'title': self.title, 'description': self.description,
            'status': self.status, 'reported_by_id': self.reported_by_id,
            'reported_by_name': self.reported_by_name, 'exam_id': self.exam_id,
            'created_at': self.created_at.isoformat(),
        }


class Schedule(me.Document):
    exam_id = me.StringField(required=True)
    exam_title = me.StringField(default='')
    course_id = me.StringField(default='')
    course_name = me.StringField(default='')
    instructor_id = me.StringField(required=True)
    instructor_name = me.StringField(default='')
    start_time = me.DateTimeField(null=True)
    end_time = me.DateTimeField(null=True)
    location = me.StringField(default='')
    created_at = me.DateTimeField(default=datetime.utcnow)

    meta = {'collection': 'schedules'}

    def to_dict(self):
        return {
            'id': str(self.id), 'exam_id': self.exam_id, 'exam_title': self.exam_title,
            'course_id': self.course_id, 'course_name': self.course_name,
            'instructor_id': self.instructor_id, 'instructor_name': self.instructor_name,
            'start_time': self.start_time.isoformat() if self.start_time else None,
            'end_time': self.end_time.isoformat() if self.end_time else None,
            'location': self.location, 'created_at': self.created_at.isoformat(),
        }


class Topic(me.Document):
    name = me.StringField(required=True)
    course_id = me.StringField(required=True)
    course_name = me.StringField(default='')
    description = me.StringField(default='')
    created_by_id = me.StringField(default='')
    created_at = me.DateTimeField(default=datetime.utcnow)

    meta = {'collection': 'topics'}

    def to_dict(self):
        return {
            'id': str(self.id), 'name': self.name, 'course_id': self.course_id,
            'course_name': self.course_name, 'description': self.description,
            'created_at': self.created_at.isoformat(),
        }


class Notification(me.Document):
    TYPES = ('exam_scheduled', 'results_ready', 'issue_resolved', 'system_alert', 'general')

    user_id = me.StringField(required=True)
    user_type = me.StringField(default='')
    title = me.StringField(required=True)
    message = me.StringField(required=True)
    notification_type = me.StringField(choices=TYPES, default='general')
    is_read = me.BooleanField(default=False)
    link = me.StringField(default='')
    created_at = me.DateTimeField(default=datetime.utcnow)

    meta = {'collection': 'notifications'}

    def to_dict(self):
        return {
            'id': str(self.id), 'user_id': self.user_id, 'user_type': self.user_type,
            'title': self.title, 'message': self.message,
            'notification_type': self.notification_type, 'is_read': self.is_read,
            'link': self.link, 'created_at': self.created_at.isoformat(),
        }
