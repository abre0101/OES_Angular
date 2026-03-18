from datetime import datetime
from rest_framework.views import APIView
from rest_framework.response import Response
from rest_framework import permissions
from .models import Question, Exam, ExamResult, TechnicalIssue, Schedule, Topic, Notification
from users.permissions import IsAdmin, IsInstructor, IsDepartmentHead, IsStudent, IsAdminOrInstructor


# ── Questions ──────────────────────────────────────────────────────────────────

class QuestionView(APIView):
    permission_classes = [IsAdminOrInstructor]

    def get(self, request):
        qs = Question.objects.filter(is_practice=False)
        if request.query_params.get('course'):
            qs = qs.filter(course_id=request.query_params['course'])
        if request.query_params.get('topic'):
            qs = qs.filter(topic__icontains=request.query_params['topic'])
        return Response([q.to_dict() for q in qs])

    def post(self, request):
        q = Question(
            course_id=request.data.get('course_id', '') or request.data.get('course', ''),
            course_name=request.data.get('course_name', ''),
            topic=request.data.get('topic', ''),
            text=request.data.get('question_text', '') or request.data.get('text', ''),
            question_type=request.data.get('question_type', 'mcq'),
            option_a=request.data.get('option_a', ''),
            option_b=request.data.get('option_b', ''),
            option_c=request.data.get('option_c', ''),
            option_d=request.data.get('option_d', ''),
            correct_answer=request.data.get('correct_answer', ''),
            points=request.data.get('points', 1.0),
            is_practice=False,
            created_by_id=str(request.user.id),
            created_by_name=request.user.full_name,
        )
        q.save()
        return Response(q.to_dict(), status=201)


class QuestionDetailView(APIView):
    permission_classes = [IsAdminOrInstructor]

    def _get(self, pk):
        try:
            return Question.objects.get(id=pk)
        except Exception:
            return None

    def patch(self, request, pk):
        q = self._get(pk)
        if not q:
            return Response({'error': 'Not found'}, status=404)
        for f in ('text', 'question_text', 'topic', 'question_type', 'option_a',
                  'option_b', 'option_c', 'option_d', 'correct_answer', 'points'):
            if f in request.data:
                setattr(q, 'text' if f == 'question_text' else f, request.data[f])
        q.updated_at = datetime.utcnow()
        q.save()
        return Response(q.to_dict())

    def delete(self, request, pk):
        q = self._get(pk)
        if not q:
            return Response({'error': 'Not found'}, status=404)
        q.delete()
        return Response(status=204)


# ── Practice Questions ─────────────────────────────────────────────────────────

class PracticeQuestionView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def get(self, request):
        qs = Question.objects.filter(is_practice=True)
        if request.query_params.get('course'):
            qs = qs.filter(course_id=request.query_params['course'])
        return Response([q.to_dict() for q in qs])

    def post(self, request):
        q = Question(
            course_id=request.data.get('course_id', '') or request.data.get('course', ''),
            course_name=request.data.get('course_name', ''),
            topic=request.data.get('topic', ''),
            text=request.data.get('question_text', '') or request.data.get('text', ''),
            question_type=request.data.get('question_type', 'mcq'),
            option_a=request.data.get('option_a', ''),
            option_b=request.data.get('option_b', ''),
            option_c=request.data.get('option_c', ''),
            option_d=request.data.get('option_d', ''),
            correct_answer=request.data.get('correct_answer', ''),
            explanation=request.data.get('explanation', ''),
            points=request.data.get('points', 1.0),
            is_practice=True,
            created_by_id=str(request.user.id),
            created_by_name=request.user.full_name,
        )
        q.save()
        return Response(q.to_dict(), status=201)


class PracticeQuestionDetailView(APIView):
    permission_classes = [IsAdminOrInstructor]

    def _get(self, pk):
        try:
            return Question.objects.get(id=pk, is_practice=True)
        except Exception:
            return None

    def patch(self, request, pk):
        q = self._get(pk)
        if not q:
            return Response({'error': 'Not found'}, status=404)
        for f in ('text', 'question_text', 'topic', 'option_a', 'option_b',
                  'option_c', 'option_d', 'correct_answer', 'explanation', 'points'):
            if f in request.data:
                setattr(q, 'text' if f == 'question_text' else f, request.data[f])
        q.updated_at = datetime.utcnow()
        q.save()
        return Response(q.to_dict())

    def delete(self, request, pk):
        q = self._get(pk)
        if not q:
            return Response({'error': 'Not found'}, status=404)
        q.delete()
        return Response(status=204)


# ── Exams ──────────────────────────────────────────────────────────────────────

class ExamView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def get(self, request):
        user = request.user
        qs = Exam.objects.all()
        if user.role == 'instructor':
            qs = qs.filter(created_by_id=str(user.id))
        elif user.role == 'student':
            qs = qs.filter(status='active')
        elif user.role == 'department_head':
            qs = qs.filter(status__in=['pending', 'approved', 'active', 'completed'])
        return Response([e.to_dict() for e in qs])

    def post(self, request):
        if request.user.role not in ('admin', 'instructor'):
            return Response({'error': 'Forbidden'}, status=403)
        e = Exam(
            title=request.data.get('title', ''),
            course_id=request.data.get('course_id', '') or request.data.get('course', ''),
            course_name=request.data.get('course_name', ''),
            created_by_id=str(request.user.id),
            created_by_name=request.user.full_name,
            duration_minutes=request.data.get('duration_minutes', 60),
            total_marks=request.data.get('total_marks', 0),
            pass_marks=request.data.get('pass_marks', 0),
            instructions=request.data.get('instructions', ''),
        )
        e.save()
        return Response(e.to_dict(), status=201)


class ExamDetailView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def _get(self, pk):
        try:
            return Exam.objects.get(id=pk)
        except Exception:
            return None

    def get(self, request, pk):
        e = self._get(pk)
        if not e:
            return Response({'error': 'Not found'}, status=404)
        return Response(e.to_dict())

    def patch(self, request, pk):
        e = self._get(pk)
        if not e:
            return Response({'error': 'Not found'}, status=404)
        for f in ('title', 'duration_minutes', 'total_marks', 'pass_marks',
                  'instructions', 'status', 'start_time', 'end_time'):
            if f in request.data:
                setattr(e, f, request.data[f])
        e.updated_at = datetime.utcnow()
        e.save()
        return Response(e.to_dict())

    def delete(self, request, pk):
        e = self._get(pk)
        if not e:
            return Response({'error': 'Not found'}, status=404)
        e.delete()
        return Response(status=204)


class ExamQuestionsView(APIView):
    permission_classes = [IsAdminOrInstructor]

    def get(self, request, exam_id):
        try:
            exam = Exam.objects.get(id=exam_id)
        except Exception:
            return Response({'error': 'Not found'}, status=404)
        questions = Question.objects.filter(id__in=exam.question_ids)
        return Response([q.to_dict() for q in questions])

    def post(self, request, exam_id):
        try:
            exam = Exam.objects.get(id=exam_id)
        except Exception:
            return Response({'error': 'Not found'}, status=404)
        qid = request.data.get('question_id', '')
        if qid and qid not in exam.question_ids:
            exam.question_ids.append(qid)
            exam.save()
        return Response(exam.to_dict())


class SubmitForApprovalView(APIView):
    permission_classes = [IsInstructor]

    def post(self, request, pk):
        try:
            exam = Exam.objects.get(id=pk, created_by_id=str(request.user.id))
        except Exception:
            return Response({'error': 'Not found'}, status=404)
        if exam.status not in ('draft', 'rejected'):
            return Response({'error': 'Cannot submit in current state'}, status=400)
        exam.status = 'pending'
        exam.submitted_at = datetime.utcnow()
        exam.save()
        return Response({'message': 'Submitted for approval'})


class ReviewExamView(APIView):
    permission_classes = [IsDepartmentHead]

    def post(self, request, pk):
        try:
            exam = Exam.objects.get(id=pk)
        except Exception:
            return Response({'error': 'Exam not found'}, status=404)
        action = request.data.get('action')
        exam.reviewed_by_id = str(request.user.id)
        exam.reviewed_by_name = request.user.full_name
        exam.reviewed_at = datetime.utcnow()
        exam.review_comment = request.data.get('comment', '')
        exam.status = 'approved' if action == 'approve' else 'rejected'
        exam.save()
        return Response({'message': f'Exam {exam.status}'})


# ── Exam Taking ────────────────────────────────────────────────────────────────

class StartExamView(APIView):
    permission_classes = [IsStudent]

    def get(self, request, pk):
        try:
            exam = Exam.objects.get(id=pk, status='active')
        except Exception:
            return Response({'error': 'Exam not available'}, status=404)
        if ExamResult.objects.filter(exam_id=pk, student_id=str(request.user.id)).first():
            return Response({'error': 'You have already taken this exam'}, status=400)
        questions = Question.objects.filter(id__in=exam.question_ids)
        return Response({
            'exam': exam.to_dict(),
            'questions': [q.to_dict(hide_answer=True) for q in questions],
        })


class SubmitExamView(APIView):
    permission_classes = [IsStudent]

    def post(self, request, pk):
        try:
            exam = Exam.objects.get(id=pk, status='active')
        except Exception:
            return Response({'error': 'Exam not available'}, status=404)
        if ExamResult.objects.filter(exam_id=pk, student_id=str(request.user.id)).first():
            return Response({'error': 'Already submitted'}, status=400)

        answers_data = request.data.get('answers', [])
        score = 0.0
        stored_answers = []

        for ans in answers_data:
            qid = str(ans.get('question', ''))
            student_answer = str(ans.get('student_answer', '')).strip().lower()
            try:
                question = Question.objects.get(id=qid)
                correct = question.correct_answer.strip().lower()
                is_correct = student_answer == correct
                pts = question.points if is_correct else 0
                score += pts
                stored_answers.append({
                    'question_id': qid,
                    'question_text': question.text,
                    'option_a': question.option_a,
                    'option_b': question.option_b,
                    'option_c': question.option_c,
                    'option_d': question.option_d,
                    'selected_answer': ans.get('student_answer', ''),
                    'correct_answer': question.correct_answer,
                    'is_correct': is_correct,
                    'points_earned': pts,
                    'explanation': getattr(question, 'explanation', ''),
                })
            except Exception:
                continue

        total = exam.total_marks or 1
        percentage = round((score / total) * 100, 2)
        passed = score >= exam.pass_marks

        result = ExamResult(
            exam_id=pk,
            exam_title=exam.title,
            student_id=str(request.user.id),
            student_name=request.user.full_name,
            score=score,
            total_marks=total,
            percentage=percentage,
            passed=passed,
            answers=stored_answers,
            submitted_at=datetime.utcnow(),
        )
        result.save()
        return Response(result.to_dict(), status=201)


# ── Results ────────────────────────────────────────────────────────────────────

class ResultView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def get(self, request):
        user = request.user
        qs = ExamResult.objects.all()
        if user.role == 'student':
            qs = qs.filter(student_id=str(user.id))
        elif user.role == 'instructor':
            exam_ids = [str(e.id) for e in Exam.objects.filter(created_by_id=str(user.id))]
            qs = qs.filter(exam_id__in=exam_ids)
        if request.query_params.get('exam'):
            qs = qs.filter(exam_id=request.query_params['exam'])
        return Response([r.to_dict() for r in qs])


class ResultDetailView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def get(self, request, pk):
        try:
            r = ExamResult.objects.get(id=pk)
        except Exception:
            return Response({'error': 'Not found'}, status=404)
        return Response(r.to_dict())


# ── Issues ─────────────────────────────────────────────────────────────────────

class IssueView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def get(self, request):
        user = request.user
        if user.role in ('admin', 'department_head', 'instructor'):
            qs = TechnicalIssue.objects.all()
        else:
            qs = TechnicalIssue.objects.filter(reported_by_id=str(user.id))
        return Response([i.to_dict() for i in qs])

    def post(self, request):
        issue = TechnicalIssue(
            title=request.data.get('title', ''),
            description=request.data.get('description', ''),
            reported_by_id=str(request.user.id),
            reported_by_name=request.user.full_name,
            exam_id=request.data.get('exam_id', ''),
        )
        issue.save()
        return Response(issue.to_dict(), status=201)


class IssueDetailView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def patch(self, request, pk):
        try:
            issue = TechnicalIssue.objects.get(id=pk)
        except Exception:
            return Response({'error': 'Not found'}, status=404)
        if 'status' in request.data:
            issue.status = request.data['status']
            if request.data['status'] == 'resolved':
                issue.resolved_at = datetime.utcnow()
        issue.save()
        return Response(issue.to_dict())


# ── Schedules ──────────────────────────────────────────────────────────────────

class ScheduleView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def get(self, request):
        user = request.user
        qs = Schedule.objects.all()
        if user.role == 'instructor':
            qs = qs.filter(instructor_id=str(user.id))
        if request.query_params.get('course'):
            qs = qs.filter(course_id=request.query_params['course'])
        return Response([s.to_dict() for s in qs])

    def post(self, request):
        exam_id = request.data.get('exam', '') or request.data.get('exam_id', '')
        exam_title = ''
        course_id = ''
        course_name = ''
        try:
            exam = Exam.objects.get(id=exam_id)
            exam_title = exam.title
            course_id = exam.course_id
            course_name = exam.course_name
        except Exception:
            pass

        s = Schedule(
            exam_id=exam_id,
            exam_title=exam_title,
            course_id=course_id,
            course_name=course_name,
            instructor_id=str(request.user.id),
            instructor_name=request.user.full_name,
            start_time=request.data.get('start_time'),
            end_time=request.data.get('end_time'),
            location=request.data.get('location', ''),
        )
        s.save()
        return Response(s.to_dict(), status=201)


class ScheduleDetailView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def _get(self, pk):
        try:
            return Schedule.objects.get(id=pk)
        except Exception:
            return None

    def patch(self, request, pk):
        s = self._get(pk)
        if not s:
            return Response({'error': 'Not found'}, status=404)
        for f in ('start_time', 'end_time', 'location'):
            if f in request.data:
                setattr(s, f, request.data[f])
        s.save()
        return Response(s.to_dict())

    def delete(self, request, pk):
        s = self._get(pk)
        if not s:
            return Response({'error': 'Not found'}, status=404)
        s.delete()
        return Response(status=204)


# ── Topics ─────────────────────────────────────────────────────────────────────

class TopicView(APIView):
    permission_classes = [IsAdminOrInstructor]

    def get(self, request):
        qs = Topic.objects.all()
        if request.query_params.get('course'):
            qs = qs.filter(course_id=request.query_params['course'])
        return Response([t.to_dict() for t in qs])

    def post(self, request):
        course_id = request.data.get('course_id', '') or request.data.get('course', '')
        course_name = request.data.get('course_name', '')
        t = Topic(
            name=request.data.get('name', ''),
            course_id=course_id,
            course_name=course_name,
            description=request.data.get('description', ''),
            created_by_id=str(request.user.id),
        )
        t.save()
        return Response(t.to_dict(), status=201)


class TopicDetailView(APIView):
    permission_classes = [IsAdminOrInstructor]

    def _get(self, pk):
        try:
            return Topic.objects.get(id=pk)
        except Exception:
            return None

    def patch(self, request, pk):
        t = self._get(pk)
        if not t:
            return Response({'error': 'Not found'}, status=404)
        for f in ('name', 'description'):
            if f in request.data:
                setattr(t, f, request.data[f])
        t.save()
        return Response(t.to_dict())

    def delete(self, request, pk):
        t = self._get(pk)
        if not t:
            return Response({'error': 'Not found'}, status=404)
        t.delete()
        return Response(status=204)


# ── Notifications ──────────────────────────────────────────────────────────────

class NotificationView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def get(self, request):
        qs = Notification.objects.filter(user_id=str(request.user.id)).order_by('-created_at')
        return Response([n.to_dict() for n in qs])

    def post(self, request):
        # Admin/system can push notifications
        if request.user.role != 'admin':
            return Response({'error': 'Forbidden'}, status=403)
        n = Notification(
            user_id=request.data.get('user_id', ''),
            user_type=request.data.get('user_type', ''),
            title=request.data.get('title', ''),
            message=request.data.get('message', ''),
            notification_type=request.data.get('notification_type', 'general'),
            link=request.data.get('link', ''),
        )
        n.save()
        return Response(n.to_dict(), status=201)


class NotificationDetailView(APIView):
    permission_classes = [permissions.IsAuthenticated]

    def patch(self, request, pk):
        try:
            n = Notification.objects.get(id=pk, user_id=str(request.user.id))
        except Exception:
            return Response({'error': 'Not found'}, status=404)
        if 'is_read' in request.data:
            n.is_read = request.data['is_read']
        n.save()
        return Response(n.to_dict())
