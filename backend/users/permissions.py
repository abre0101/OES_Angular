from rest_framework.permissions import BasePermission


class IsAdmin(BasePermission):
    def has_permission(self, request, view):
        return request.user and request.user.role == 'admin'


class IsInstructor(BasePermission):
    def has_permission(self, request, view):
        return request.user and request.user.role == 'instructor'


class IsDepartmentHead(BasePermission):
    def has_permission(self, request, view):
        return request.user and request.user.role == 'department_head'


class IsStudent(BasePermission):
    def has_permission(self, request, view):
        return request.user and request.user.role == 'student'


class IsAdminOrSelf(BasePermission):
    def has_permission(self, request, view):
        return bool(request.user)

    def has_object_permission(self, request, view, obj):
        return request.user.role == 'admin' or str(obj.id) == str(request.user.id)


class IsAdminOrInstructor(BasePermission):
    def has_permission(self, request, view):
        return request.user and request.user.role in ('admin', 'instructor')


class IsAdminOrDeptHead(BasePermission):
    def has_permission(self, request, view):
        return request.user and request.user.role in ('admin', 'department_head')
