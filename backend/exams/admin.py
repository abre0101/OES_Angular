from django.contrib import admin
from .models import Question, Exam, ExamQuestion, ExamResult, ExamAnswer, TechnicalIssue

admin.site.register(Question)
admin.site.register(Exam)
admin.site.register(ExamQuestion)
admin.site.register(ExamResult)
admin.site.register(TechnicalIssue)
