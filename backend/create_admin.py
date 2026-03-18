"""Run with: py create_admin.py"""
import os
import django
os.environ.setdefault('DJANGO_SETTINGS_MODULE', 'oes.settings')
django.setup()

from users.models import User

username = 'admin'
if User.objects(username=username).first():
    print(f'User "{username}" already exists.')
else:
    u = User(
        username=username,
        email='admin@dmu.edu.et',
        first_name='System',
        last_name='Admin',
        role='admin',
        is_active=True,
    )
    u.set_password('admin123')
    u.save()
    print(f'Admin user created. Username: {username} / Password: admin123')
