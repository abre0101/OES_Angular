from rest_framework.views import APIView
from rest_framework.response import Response
from rest_framework import status, permissions
from .models import User
from .serializers import (
    UserCreateSerializer, UserUpdateSerializer,
    ChangePasswordSerializer, LoginSerializer, user_to_dict
)
from .auth import generate_tokens, decode_token
from .permissions import IsAdmin, IsAdminOrSelf
import jwt


class LoginView(APIView):
    permission_classes = [permissions.AllowAny]

    def post(self, request):
        s = LoginSerializer(data=request.data)
        if not s.is_valid():
            return Response(s.errors, status=400)

        user = User.objects(username=s.validated_data['username']).first()
        if not user or not user.check_password(s.validated_data['password']):
            return Response({'error': 'Invalid username or password'}, status=401)
        if not user.is_active:
            return Response({'error': 'Account is inactive'}, status=403)

        tokens = generate_tokens(user)
        return Response({**tokens, 'user': user_to_dict(user)})


class TokenRefreshView(APIView):
    permission_classes = [permissions.AllowAny]

    def post(self, request):
        refresh = request.data.get('refresh')
        if not refresh:
            return Response({'error': 'Refresh token required'}, status=400)
        try:
            payload = decode_token(refresh)
            if payload.get('type') != 'refresh':
                raise jwt.InvalidTokenError()
            user = User.objects.get(id=payload['user_id'])
            tokens = generate_tokens(user)
            return Response({'access': tokens['access']})
        except Exception:
            return Response({'error': 'Invalid or expired refresh token'}, status=401)


class RegisterView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def post(self, request):
        if request.user.role not in ('admin', 'department_head'):
            return Response({'error': 'Forbidden'}, status=403)
        s = UserCreateSerializer(data=request.data)
        if s.is_valid():
            user = s.save()
            return Response(user_to_dict(user), status=201)
        return Response(s.errors, status=400)


class UserListView(APIView):
    permission_classes = [IsAdmin]

    def get(self, request):
        role = request.query_params.get('role')
        qs = User.objects.all()
        if role:
            qs = qs.filter(role=role)
        return Response([user_to_dict(u) for u in qs])


class UserDetailView(APIView):
    permission_classes = [IsAdminOrSelf]

    def _get_user(self, pk):
        try:
            return User.objects.get(id=pk)
        except Exception:
            return None

    def get(self, request, pk):
        user = self._get_user(pk)
        if not user:
            return Response({'error': 'Not found'}, status=404)
        return Response(user_to_dict(user))

    def patch(self, request, pk):
        user = self._get_user(pk)
        if not user:
            return Response({'error': 'Not found'}, status=404)
        s = UserUpdateSerializer(data=request.data, partial=True)
        if s.is_valid():
            for k, v in s.validated_data.items():
                setattr(user, k, v)
            user.save()
            return Response(user_to_dict(user))
        return Response(s.errors, status=400)

    def delete(self, request, pk):
        user = self._get_user(pk)
        if not user:
            return Response({'error': 'Not found'}, status=404)
        user.delete()
        return Response(status=204)


class MeView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def get(self, request):
        return Response(user_to_dict(request.user))

    def patch(self, request):
        s = UserUpdateSerializer(data=request.data, partial=True)
        if s.is_valid():
            for k, v in s.validated_data.items():
                setattr(request.user, k, v)
            request.user.save()
            return Response(user_to_dict(request.user))
        return Response(s.errors, status=400)


class ChangePasswordView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def post(self, request):
        s = ChangePasswordSerializer(data=request.data)
        if not s.is_valid():
            return Response(s.errors, status=400)
        if not request.user.check_password(s.validated_data['old_password']):
            return Response({'error': 'Incorrect current password'}, status=400)
        request.user.set_password(s.validated_data['new_password'])
        request.user.save()
        return Response({'message': 'Password updated'})


class BulkImportView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def post(self, request):
        if request.user.role not in ('admin', 'department_head'):
            return Response({'error': 'Forbidden'}, status=403)
        users_data = request.data.get('users', [])
        created, errors = [], []
        for item in users_data:
            s = UserCreateSerializer(data=item)
            if s.is_valid():
                s.save()
                created.append(item.get('username'))
            else:
                errors.append({'user': item.get('username'), 'errors': s.errors})
        return Response({'created': len(created), 'errors': errors})


class AuditLogView(APIView):
    permission_classes = [IsAdmin]

    def get(self, request):
        from users.models import AuditLog
        qs = AuditLog.objects.all().order_by('-created_at')
        if request.query_params.get('user_type'):
            qs = qs.filter(user_type=request.query_params['user_type'])
        if request.query_params.get('date'):
            from datetime import datetime
            try:
                d = datetime.strptime(request.query_params['date'], '%Y-%m-%d')
                qs = qs.filter(created_at__gte=d)
            except ValueError:
                pass
        return Response([log.to_dict() for log in qs[:200]])


class GlobalSearchView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def get(self, request):
        q = request.query_params.get('q', '').strip()
        if len(q) < 2:
            return Response({'students': [], 'instructors': [], 'courses': [], 'exams': []})

        from courses.models import Course
        from exams.models import Exam

        students = User.objects.filter(role='student', username__icontains=q)[:10]
        instructors = User.objects.filter(role='instructor', username__icontains=q)[:10]
        courses = Course.objects.filter(name__icontains=q)[:10]
        exams = Exam.objects.filter(title__icontains=q)[:10]

        return Response({
            'students': [user_to_dict(u) for u in students],
            'instructors': [user_to_dict(u) for u in instructors],
            'courses': [c.to_dict() for c in courses],
            'exams': [e.to_dict() for e in exams],
        })
