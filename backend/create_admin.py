"""Run with: py create_admin.py"""
import os
import django
os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'oes.settings')
django.setup()

from users.models import User

users = [
    {
        'username': 'admin',
        'email': 'admin@dmu.edu.et',
        'password': 'admin123',
        'first_name': 'System',
        'last_name': 'Admin',
        'role': 'admin',
    },
    {
        'username': 'instructor1',
        'email': 'instructor1@dmu.edu.et',
        'password': 'instructor123',
        'first_name': 'John',
        'last_name': 'Smith',
        'role': 'instructor',
        'employee_id': 'EMP001',
        'department': 'Computer Science',
    },
    {
        'username': 'depthead1',
        'email': 'depthead1@dmu.edu.et',
        'password': 'depthead123',
        'first_name': 'Jane',
        'last_name': 'Doe',
        'role': 'department_head',
        'employee_id': 'EMP002',
        'department': 'Computer Science',
    },
    {
        'username': 'student1',
        'email': 'student1@dmu.edu.et',
        'password': 'student123',
        'first_name': 'Alice',
        'last_name': 'Johnson',
        'role': 'student',
        'student_id': 'STU001',
        'year_of_study': 2,
        'department': 'Computer Science',
    },
]

for data in users:
    username = data['username']
    existing = User.objects(username=username).first()
    if existing:
        existing.delete()
        print(f'Deleted existing user "{username}".')
    password = data.pop('password')
    user = User(**data, is_active=True)
    user.set_password(password)
    user.save()
    print(f'Created [{user.role}] username: {username} / password: {password}')
