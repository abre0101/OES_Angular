from rest_framework import serializers
from .models import User


def user_to_dict(user: User) -> dict:
    return {
        'id': str(user.id),
        'username': user.username,
        'email': user.email,
        'first_name': user.first_name,
        'last_name': user.last_name,
        'full_name': user.full_name,
        'role': user.role,
        'department': user.department,
        'faculty': user.faculty,
        'phone': user.phone,
        'profile_image': user.profile_image,
        'student_id': user.student_id,
        'year_of_study': user.year_of_study,
        'employee_id': user.employee_id,
        'is_active': user.is_active,
        'date_joined': user.date_joined.isoformat() if user.date_joined else None,
    }


class UserCreateSerializer(serializers.Serializer):
    username = serializers.CharField(max_length=150)
    email = serializers.EmailField()
    password = serializers.CharField(min_length=6, write_only=True)
    first_name = serializers.CharField(default='')
    last_name = serializers.CharField(default='')
    role = serializers.ChoiceField(choices=User.ROLES, default='student')
    department = serializers.CharField(default='', allow_blank=True)
    faculty = serializers.CharField(default='', allow_blank=True)
    phone = serializers.CharField(default='', allow_blank=True)
    student_id = serializers.CharField(default='', allow_blank=True)
    year_of_study = serializers.IntegerField(required=False, allow_null=True)
    employee_id = serializers.CharField(default='', allow_blank=True)

    def validate_username(self, value):
        if User.objects(username=value).first():
            raise serializers.ValidationError('Username already exists.')
        return value

    def validate_email(self, value):
        if User.objects(email=value).first():
            raise serializers.ValidationError('Email already exists.')
        return value

    def create(self, validated_data):
        password = validated_data.pop('password')
        user = User(**validated_data)
        user.set_password(password)
        user.save()
        return user


class UserUpdateSerializer(serializers.Serializer):
    first_name = serializers.CharField(required=False)
    last_name = serializers.CharField(required=False)
    email = serializers.EmailField(required=False)
    department = serializers.CharField(required=False, allow_blank=True)
    faculty = serializers.CharField(required=False, allow_blank=True)
    phone = serializers.CharField(required=False, allow_blank=True)
    student_id = serializers.CharField(required=False, allow_blank=True)
    year_of_study = serializers.IntegerField(required=False, allow_null=True)
    employee_id = serializers.CharField(required=False, allow_blank=True)
    is_active = serializers.BooleanField(required=False)


class ChangePasswordSerializer(serializers.Serializer):
    old_password = serializers.CharField()
    new_password = serializers.CharField(min_length=6)


class LoginSerializer(serializers.Serializer):
    username = serializers.CharField()
    password = serializers.CharField()
