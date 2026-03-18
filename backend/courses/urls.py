from django.urls import path
from . import views

urlpatterns = [
    path('faculties/', views.FacultyView.as_view()),
    path('faculties/<str:pk>/', views.FacultyDetailView.as_view()),
    path('departments/', views.DepartmentView.as_view()),
    path('departments/<str:pk>/', views.DepartmentDetailView.as_view()),
    path('my-courses/', views.MyCoursesView.as_view()),
    path('enrollments/', views.EnrollmentView.as_view()),
    path('assign-instructor/', views.AssignInstructorView.as_view()),
    path('instructor-courses/', views.InstructorCoursesView.as_view()),
    path('', views.CourseView.as_view()),
    path('<str:pk>/', views.CourseDetailView.as_view()),
]
