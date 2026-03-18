from django.urls import path
from . import views

urlpatterns = [
    # Non-exam resource endpoints first
    path('questions/', views.QuestionView.as_view()),
    path('questions/<str:pk>/', views.QuestionDetailView.as_view()),
    path('results/', views.ResultView.as_view()),
    path('results/<str:pk>/', views.ResultDetailView.as_view()),
    path('issues/', views.IssueView.as_view()),
    path('issues/<str:pk>/', views.IssueDetailView.as_view()),
    path('schedules/', views.ScheduleView.as_view()),
    path('schedules/<str:pk>/', views.ScheduleDetailView.as_view()),
    path('practice-questions/', views.PracticeQuestionView.as_view()),
    path('practice-questions/<str:pk>/', views.PracticeQuestionDetailView.as_view()),
    path('topics/', views.TopicView.as_view()),
    path('topics/<str:pk>/', views.TopicDetailView.as_view()),
    path('notifications/', views.NotificationView.as_view()),
    path('notifications/<str:pk>/', views.NotificationDetailView.as_view()),
    # Exam list/create
    path('', views.ExamView.as_view()),
    # Exam sub-actions — MUST come before the generic <pk>/ to avoid conflicts
    path('<str:exam_id>/questions/', views.ExamQuestionsView.as_view()),
    path('<str:pk>/submit-for-approval/', views.SubmitForApprovalView.as_view()),
    path('<str:pk>/review/', views.ReviewExamView.as_view()),
    path('<str:pk>/start/', views.StartExamView.as_view()),
    path('<str:pk>/submit/', views.SubmitExamView.as_view()),
    # Generic exam detail — LAST so it doesn't swallow the sub-action routes
    path('<str:pk>/', views.ExamDetailView.as_view()),
]
