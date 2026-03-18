import mongoengine as me
from datetime import datetime
import bcrypt


class User(me.Document):
    ROLES = ('admin', 'instructor', 'department_head', 'student')

    username = me.StringField(required=True, unique=True, max_length=150)
    email = me.EmailField(required=True, unique=True)
    password_hash = me.StringField(required=True)
    first_name = me.StringField(max_length=100, default='')
    last_name = me.StringField(max_length=100, default='')
    role = me.StringField(choices=ROLES, default='student')
    department = me.StringField(default='')
    faculty = me.StringField(default='')
    phone = me.StringField(default='')
    profile_image = me.StringField(default='')
    is_active = me.BooleanField(default=True)
    date_joined = me.DateTimeField(default=datetime.utcnow)
    last_login = me.DateTimeField(null=True)

    # Student-specific
    student_id = me.StringField(default='')
    year_of_study = me.IntField(null=True)

    # Staff-specific
    employee_id = me.StringField(default='')

    meta = {'collection': 'users', 'indexes': ['username', 'email']}

    def set_password(self, raw_password: str):
        self.password_hash = bcrypt.hashpw(
            raw_password.encode(), bcrypt.gensalt()
        ).decode()

    def check_password(self, raw_password: str) -> bool:
        return bcrypt.checkpw(raw_password.encode(), self.password_hash.encode())

    @property
    def full_name(self):
        return f'{self.first_name} {self.last_name}'.strip()

    @property
    def id_str(self):
        return str(self.id)

    def __str__(self):
        return f'{self.username} ({self.role})'


class PasswordResetToken(me.Document):
    user = me.ReferenceField(User, required=True)
    token = me.StringField(required=True, unique=True)
    created_at = me.DateTimeField(default=datetime.utcnow)
    is_used = me.BooleanField(default=False)

    meta = {'collection': 'password_reset_tokens'}


class AuditLog(me.Document):
    action = me.StringField(required=True)
    user_id = me.StringField(default='')
    user_type = me.StringField(default='')
    table_name = me.StringField(default='')
    record_id = me.StringField(default='')
    ip_address = me.StringField(default='')
    old_value = me.StringField(default='')
    new_value = me.StringField(default='')
    metadata = me.StringField(default='')
    created_at = me.DateTimeField(default=datetime.utcnow)

    meta = {'collection': 'audit_logs', 'ordering': ['-created_at']}

    def to_dict(self):
        return {
            'id': str(self.id),
            'action': self.action,
            'user_id': self.user_id,
            'user_type': self.user_type,
            'table_name': self.table_name,
            'record_id': self.record_id,
            'ip_address': self.ip_address,
            'created_at': self.created_at.isoformat(),
        }
