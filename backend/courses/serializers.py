from rest_framework import serializers
from .models import Faculty, Department, Course, CourseAssignment, StudentEnrollment
from users.serializers import UserSerializer


class FacultySerializer(serializers.ModelSerializer):
    class Meta:
        model = Faculty
        fields = '__all__'


class DepartmentSerializer(serializers.ModelSerializer):
    faculty_name = serializers.ReadOnlyField(source='faculty.name')
    head_name = serializers.ReadOnlyField(source='head.full_name')

    class Meta:
        model = Department
        fields = '__all__'


class CourseSerializer(serializers.ModelSerializer):
    department_name = serializers.ReadOnlyField(source='department.name')

    class Meta:
        model = Course
        fields = '__all__'


class CourseAssignmentSerializer(serializers.ModelSerializer):
    course_name = serializers.ReadOnlyField(source='course.name')
    instructor_name = serializers.ReadOnlyField(source='instructor.full_name')

    class Meta:
        model = CourseAssignment
        fields = '__all__'


class StudentEnrollmentSerializer(serializers.ModelSerializer):
    course_name = serializers.ReadOnlyField(source='course.name')
    student_name = serializers.ReadOnlyField(source='student.full_name')

    class Meta:
        model = StudentEnrollment
        fields = '__all__'
