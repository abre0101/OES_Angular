from django.urls import path
from . import views

urlpatterns = [
    path('login/', views.LoginView.as_view()),
    path('token/refresh/', views.TokenRefreshView.as_view()),
    path('register/', views.RegisterView.as_view()),
    path('me/', views.MeView.as_view()),
    path('change-password/', views.ChangePasswordView.as_view()),
    path('users/', views.UserListView.as_view()),
    path('users/<str:pk>/', views.UserDetailView.as_view()),
    path('bulk-import/', views.BulkImportView.as_view()),
    path('audit-logs/', views.AuditLogView.as_view()),
    path('search/', views.GlobalSearchView.as_view()),
]
