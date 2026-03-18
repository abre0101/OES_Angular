from django.contrib import admin
from django.contrib.auth.admin import UserAdmin as BaseUserAdmin
from .models import User

@admin.register(User)
class UserAdmin(BaseUserAdmin):
    list_display = ('username', 'email', 'role', 'department', 'is_active')
    list_filter = ('role', 'is_active')
    fieldsets = BaseUserAdmin.fieldsets + (
        ('OES Info', {'fields': ('role', 'department', 'faculty', 'phone', 'student_id', 'employee_id')}),
    )
    search_fields = ('username', 'email', 'first_name', 'last_name')
