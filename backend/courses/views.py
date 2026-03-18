from rest_framework.views import APIView
from rest_framework.response import Response
from rest_framework import permissions
from .models import Faculty, Department, Course, CourseAssignment, StudentEnrollment
from users.permissions import IsAdmin, IsAdminOrDeptHead


class FacultyView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def get(self, request):
        return Response([f.to_dict() for f in Faculty.objects.all()])

    def post(self, request):
        if request.user.role != 'admin':
            return Response({'error': 'Forbidden'}, status=403)
        f = Faculty(name=request.data.get('name', ''), description=request.data.get('description', ''))
        f.save()
        return Response(f.to_dict(), status=201)


class FacultyDetailView(APIView):
    permission_classes = [IsAdmin]

    def _get(self, pk):
        try:
            return Faculty.objects.get(id=pk)
        except Exception:
            return None

    def patch(self, request, pk):
        f = self._get(pk)
        if not f:
            return Response({'error': 'Not found'}, status=404)
        for field in ('name', 'description'):
            if field in request.data:
                setattr(f, field, request.data[field])
        f.save()
        return Response(f.to_dict())

    def delete(self, request, pk):
        f = self._get(pk)
        if not f:
            return Response({'error': 'Not found'}, status=404)
        f.delete()
        return Response(status=204)


class DepartmentView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def get(self, request):
        return Response([d.to_dict() for d in Department.objects.all()])

    def post(self, request):
        if request.user.role != 'admin':
            return Response({'error': 'Forbidden'}, status=403)
        try:
            faculty = Faculty.objects.get(id=request.data['faculty'])
        except Exception:
            return Response({'error': 'Faculty not found'}, status=400)
        d = Department(
            name=request.data.get('name', ''),
            faculty=faculty,
            description=request.data.get('description', ''),
        )
        d.save()
        return Response(d.to_dict(), status=201)


class DepartmentDetailView(APIView):
    permission_classes = [IsAdmin]

    def _get(self, pk):
        try:
            return Department.objects.get(id=pk)
        except Exception:
            return None

    def patch(self, request, pk):
        d = self._get(pk)
        if not d:
            return Response({'error': 'Not found'}, status=404)
        for field in ('name', 'description', 'head_id', 'head_name'):
            if field in request.data:
                setattr(d, field, request.data[field])
        d.save()
        return Response(d.to_dict())

    def delete(self, request, pk):
        d = self._get(pk)
        if not d:
            return Response({'error': 'Not found'}, status=404)
        d.delete()
        return Response(status=204)


class CourseView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def get(self, request):
        qs = Course.objects.all()
        dept = request.query_params.get('department')
        if dept:
            qs = qs.filter(department=dept)
        return Response([c.to_dict() for c in qs])

    def post(self, request):
        if request.user.role not in ('admin', 'department_head'):
            return Response({'error': 'Forbidden'}, status=403)
        dept_id = request.data.get('department_id') or request.data.get('department')
        try:
            dept = Department.objects.get(id=dept_id)
        except Exception:
            return Response({'error': 'Department not found'}, status=400)
        c = Course(
            code=request.data.get('code', ''),
            name=request.data.get('name', ''),
            department=dept,
            description=request.data.get('description', ''),
            credit_hours=request.data.get('credit_hours', 3),
        )
        c.save()
        return Response(c.to_dict(), status=201)


class CourseDetailView(APIView):
    permission_classes = [IsAdmin]

    def _get(self, pk):
        try:
            return Course.objects.get(id=pk)
        except Exception:
            return None

    def get(self, request, pk):
        c = self._get(pk)
        if not c:
            return Response({'error': 'Not found'}, status=404)
        return Response(c.to_dict())

    def patch(self, request, pk):
        c = self._get(pk)
        if not c:
            return Response({'error': 'Not found'}, status=404)
        for field in ('code', 'name', 'description', 'credit_hours', 'is_active'):
            if field in request.data:
                setattr(c, field, request.data[field])
        c.save()
        return Response(c.to_dict())

    def delete(self, request, pk):
        c = self._get(pk)
        if not c:
            return Response({'error': 'Not found'}, status=404)
        c.delete()
        return Response(status=204)


class MyCoursesView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def get(self, request):
        user = request.user
        uid = str(user.id)
        if user.role == 'instructor':
            assignments = CourseAssignment.objects.filter(instructor_id=uid)
            course_ids = [a.course_id for a in assignments]
            courses = Course.objects.filter(id__in=course_ids)
            return Response([c.to_dict() for c in courses])
        elif user.role == 'student':
            enrollments = StudentEnrollment.objects.filter(student_id=uid)
            course_ids = [e.course_id for e in enrollments]
            courses = Course.objects.filter(id__in=course_ids)
            return Response([c.to_dict() for c in courses])
        return Response([])


class EnrollmentView(APIView):
    permission_classes = [IsAdminOrDeptHead]

    def get(self, request):
        qs = StudentEnrollment.objects.all()
        if request.query_params.get('student'):
            qs = qs.filter(student_id=request.query_params['student'])
        if request.query_params.get('course'):
            qs = qs.filter(course_id=request.query_params['course'])
        return Response([e.to_dict() for e in qs])

    def post(self, request):
        e = StudentEnrollment(
            student_id=request.data.get('student_id', ''),
            student_name=request.data.get('student_name', ''),
            course_id=request.data.get('course_id', ''),
            course_name=request.data.get('course_name', ''),
        )
        e.save()
        return Response(e.to_dict(), status=201)


class AssignInstructorView(APIView):
    permission_classes = [IsAdminOrDeptHead]

    def post(self, request):
        course_id = request.data.get('course_id', '') or request.data.get('course', '')
        instructor_id = request.data.get('instructor_id', '') or request.data.get('instructor', '')
        course_name = ''
        instructor_name = ''
        try:
            course = Course.objects.get(id=course_id)
            course_name = course.name
        except Exception:
            pass
        from users.models import User
        try:
            instructor = User.objects.get(id=instructor_id)
            instructor_name = instructor.full_name
        except Exception:
            pass

        # Remove existing assignment for this instructor+course if any
        CourseAssignment.objects(course_id=course_id, instructor_id=instructor_id).delete()

        a = CourseAssignment(
            course_id=course_id,
            course_name=course_name,
            instructor_id=instructor_id,
            instructor_name=instructor_name,
            academic_year=request.data.get('academic_year', ''),
            semester=request.data.get('semester', ''),
        )
        a.save()
        return Response(a.to_dict(), status=201)


class InstructorCoursesView(APIView):
    """Returns all course assignments (for admin/dept-head overview)."""
    permission_classes = [IsAdminOrDeptHead]

    def get(self, request):
        qs = CourseAssignment.objects.all()
        if request.query_params.get('instructor'):
            qs = qs.filter(instructor_id=request.query_params['instructor'])
        if request.query_params.get('course'):
            qs = qs.filter(course_id=request.query_params['course'])
        return Response([a.to_dict() for a in qs])
