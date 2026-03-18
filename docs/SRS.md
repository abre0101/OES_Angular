# Software Requirements Specification (SRS)
## Online Examination System (OES)

**Version:** 1.0  
**Date:** March 18, 2026  
**Technology Stack:** Angular (Frontend) + Django (Backend)

---

## 1. Introduction

### 1.1 Purpose
This document specifies the software requirements for the Online Examination System (OES). It describes the functional and non-functional requirements of the system built using Angular as the frontend framework and Django as the backend framework.

### 1.2 Scope
The OES is a web-based platform that enables students to take online exams, instructors to create and manage exams, department heads to approve exams, and administrators to manage the entire system. The system is built using Angular for the frontend and Django REST API for the backend.

### 1.3 Definitions and Acronyms

| Term | Definition |
|------|-----------|
| OES | Online Examination System |
| SRS | Software Requirements Specification |
| API | Application Programming Interface |
| REST | Representational State Transfer |
| JWT | JSON Web Token |
| SPA | Single Page Application |
| DRF | Django REST Framework |
| RBAC | Role-Based Access Control |

### 1.4 Technology Stack

| Layer | Technology |
|-------|-----------|
| Frontend | Angular 17+ (TypeScript, RxJS, Angular Material) |
| Backend | Django 5.x + Django REST Framework |
| Database | PostgreSQL 15+ |
| Authentication | JWT (djangorestframework-simplejwt) |
| Real-time | Django Channels (WebSocket) |
| Task Queue | Celery + Redis |
| File Storage | Django media storage / S3-compatible |
| Deployment | Docker + Nginx |

---

## 2. Overall Description

### 2.1 System Architecture

The system follows a decoupled architecture:

```
Angular SPA  <-->  Django REST API  <-->  PostgreSQL
     |                   |
     |            Django Channels
     |            (WebSocket for real-time exam)
     |
Angular Router (client-side routing)
```

- Angular handles all UI rendering, routing, and state management.
- Django exposes RESTful API endpoints consumed by Angular via HTTP.
- JWT tokens are used for stateless authentication.
- WebSockets (Django Channels) handle real-time exam timer synchronization and auto-submit.

### 2.2 User Roles

| Role | Description |
|------|-------------|
| Student | Takes exams, views results, practices |
| Instructor | Creates exams, manages question banks |
| Department Head | Approves exams, monitors department |
| Administrator | Full system management |

### 2.3 General Constraints
- The system must be accessible via modern web browsers (Chrome, Firefox, Edge, Safari).
- All API communication must use HTTPS.
- The Angular frontend is a Single Page Application served as static files.
- The Django backend exposes only REST API endpoints (no server-side rendering).

---

## 3. Functional Requirements

### 3.1 Authentication Module

#### 3.1.1 Login
- **FR-AUTH-01:** Students shall log in via a dedicated student login page using student ID and password.
- **FR-AUTH-02:** Staff (Instructors, Department Heads, Administrators) shall log in via a staff login page.
- **FR-AUTH-03:** The Django backend shall issue a JWT access token and refresh token upon successful authentication.
- **FR-AUTH-04:** Angular shall store tokens in memory (access) and HttpOnly cookies or localStorage (refresh).
- **FR-AUTH-05:** The system shall redirect users to their role-specific dashboard after login.
- **FR-AUTH-06:** Angular route guards (`CanActivate`) shall enforce role-based access to routes.

#### 3.1.2 Password Management

**Forgot Password (Unauthenticated Flow)**

- **FR-AUTH-07:** The Angular login pages shall include a "Forgot Password?" link that navigates to the password reset request page.
- **FR-AUTH-08:** The reset request page shall accept the user's registered email address.
- **FR-AUTH-09:** On submission, the Django backend shall verify the email exists in the system. If it does not exist, the API shall still return a generic success response to prevent user enumeration.
- **FR-AUTH-10:** The backend shall generate a cryptographically secure, single-use reset token (UUID or signed token via Django's `PasswordResetTokenGenerator`) and store it with an expiry of 30 minutes.
- **FR-AUTH-11:** The backend shall dispatch an email asynchronously (via Celery) containing a reset link in the format: `https://<domain>/reset-password?token=<token>&uid=<user_id>`.
- **FR-AUTH-12:** The Angular reset-password page shall extract the `token` and `uid` from the URL query params and validate them against the backend before rendering the new password form.
- **FR-AUTH-13:** If the token is invalid or expired, the Angular page shall display a clear error message and offer a link to request a new reset email.
- **FR-AUTH-14:** The new password form shall require the user to enter and confirm the new password, with client-side validation (minimum 8 characters, at least one number and one uppercase letter).
- **FR-AUTH-15:** On successful submission, the Django backend shall hash and save the new password, invalidate the reset token, and invalidate all existing refresh tokens for that user.
- **FR-AUTH-16:** The system shall rate-limit reset requests to a maximum of 5 requests per email per hour to prevent abuse.

**Change Password (Authenticated Flow)**

- **FR-AUTH-17:** Authenticated users shall be able to change their password from their profile/settings page.
- **FR-AUTH-18:** The change password form shall require the current password, new password, and confirmation of the new password.
- **FR-AUTH-19:** The Django backend shall verify the current password before applying the change.
- **FR-AUTH-20:** On success, all existing refresh tokens for the user shall be invalidated, requiring re-login.

**Admin Password Reset**

- **FR-AUTH-21:** Administrators shall be able to reset any user's password directly from the user management panel without requiring the user's current password.
- **FR-AUTH-22:** The admin-triggered reset shall either set a temporary password or send a reset link to the user's email.

#### 3.1.3 Logout
- **FR-AUTH-11:** Users shall be able to log out, which invalidates the JWT refresh token on the server.

---

### 3.2 Student Module

#### 3.2.1 Dashboard
- **FR-STU-01:** The student dashboard shall display upcoming exams, recent results, and notifications.
- **FR-STU-02:** The dashboard shall show a schedule/calendar of assigned exams.

#### 3.2.2 Exam Taking
- **FR-STU-03:** Students shall see a list of available (approved and scheduled) exams.
- **FR-STU-04:** Students shall view exam instructions before starting.
- **FR-STU-05:** The exam interface shall display one or multiple questions with a countdown timer.
- **FR-STU-06:** The timer shall be synchronized via WebSocket to prevent client-side manipulation.
- **FR-STU-07:** The system shall auto-submit the exam when the timer expires.
- **FR-STU-08:** Students shall be able to navigate between questions and flag questions for review.
- **FR-STU-09:** The system shall save answers periodically (auto-save) to prevent data loss.
- **FR-STU-10:** Students shall submit the exam manually before the timer expires.

#### 3.2.3 Results
- **FR-STU-11:** Students shall view their exam results after submission.
- **FR-STU-12:** The results page shall show score, percentage, pass/fail status, and per-question breakdown.
- **FR-STU-13:** Students shall be able to review their submitted answers.
- **FR-STU-14:** Students shall have access to their full exam history.

#### 3.2.4 Practice Mode
- **FR-STU-15:** Students shall access a practice mode with sample questions per course.
- **FR-STU-16:** Practice mode shall provide immediate feedback on answers.
- **FR-STU-17:** Students shall be able to select a course/topic for practice.

#### 3.2.5 Profile Management
- **FR-STU-18:** Students shall view and edit their profile information.
- **FR-STU-19:** Profile updates shall be submitted to the Django API via a PATCH request.

#### 3.2.6 Issue Reporting
- **FR-STU-20:** Students shall report technical issues encountered during exams.
- **FR-STU-21:** Students shall view the status of their reported issues.
- **FR-STU-22:** A quick-report widget shall be available during active exams.

#### 3.2.7 Notifications
- **FR-STU-23:** Students shall receive notifications for new exam schedules and results.

---

### 3.3 Instructor Module

#### 3.3.1 Dashboard
- **FR-INS-01:** The instructor dashboard shall display exam statistics, pending tasks, and recent activity.

#### 3.3.2 Exam Management
- **FR-INS-02:** Instructors shall create exams by specifying title, course, duration, total marks, and instructions.
- **FR-INS-03:** Instructors shall add questions to an exam from the question bank or create new ones inline.
- **FR-INS-04:** Instructors shall edit exam details before submission for approval.
- **FR-INS-05:** Instructors shall delete exams that have not yet been approved.
- **FR-INS-06:** Instructors shall submit exams for department head approval.
- **FR-INS-07:** Instructors shall view the approval status of submitted exams.
- **FR-INS-08:** Instructors shall manage exam questions (add, edit, remove, reorder).
- **FR-INS-09:** Instructors shall update total marks for an exam.

#### 3.3.3 Question Bank
- **FR-INS-10:** Instructors shall create questions with types: Multiple Choice, True/False, Short Answer.
- **FR-INS-11:** Questions shall be organized by course and topic.
- **FR-INS-12:** Instructors shall manage topics per course (create, edit, delete).
- **FR-INS-13:** Instructors shall edit and delete questions from the question bank.
- **FR-INS-14:** Instructors shall view questions filtered by topic or status.

#### 3.3.4 Practice Questions
- **FR-INS-15:** Instructors shall create and manage practice questions per course.
- **FR-INS-16:** Instructors shall edit and delete practice questions.

#### 3.3.5 Scheduling
- **FR-INS-17:** Instructors shall create exam schedules specifying start time, end time, and assigned students/groups.
- **FR-INS-18:** Instructors shall edit and delete schedules.
- **FR-INS-19:** Instructors shall view all schedules for their exams.

#### 3.3.6 Results & Analytics
- **FR-INS-20:** Instructors shall view results for all students who took their exams.
- **FR-INS-21:** Instructors shall view detailed results per student.
- **FR-INS-22:** The analytics page shall display pass rate, average score, score distribution charts (using Angular charts library).
- **FR-INS-23:** Instructors shall view a results overview across all their exams.

#### 3.3.7 Courses & Students
- **FR-INS-24:** Instructors shall view their assigned courses.
- **FR-INS-25:** Instructors shall view students enrolled in their courses.

#### 3.3.8 Issue Tracking
- **FR-INS-26:** Instructors shall view technical issues reported by students in their exams.

#### 3.3.9 Notifications
- **FR-INS-27:** Instructors shall receive notifications for exam approvals/rejections.

#### 3.3.10 Profile & Settings
- **FR-INS-28:** Instructors shall manage their profile and change their password.

---

### 3.4 Department Head Module

#### 3.4.1 Dashboard
- **FR-DH-01:** The department head dashboard shall display pending approvals count, department exam statistics, and recent activity.

#### 3.4.2 Exam Approval Workflow
- **FR-DH-02:** Department heads shall view a list of exams pending approval.
- **FR-DH-03:** Department heads shall view full exam details including questions before approving.
- **FR-DH-04:** Department heads shall approve or reject an exam with an optional comment.
- **FR-DH-05:** The system shall notify the instructor of the approval decision via notification.
- **FR-DH-06:** Department heads shall view approved exams and approval history.
- **FR-DH-07:** Department heads shall view and approve individual questions.

#### 3.4.3 Exam Monitoring
- **FR-DH-08:** Department heads shall monitor ongoing exams in their department in real-time.
- **FR-DH-09:** Department heads shall view all department exams and their schedules.
- **FR-DH-10:** Department heads shall schedule exams for their department.
- **FR-DH-11:** Department heads shall view exam details.

#### 3.4.4 Student Management
- **FR-DH-12:** Department heads shall view students in their department.
- **FR-DH-13:** Department heads shall register new students.
- **FR-DH-14:** Department heads shall edit student information.
- **FR-DH-15:** Department heads shall bulk import students via CSV/Excel upload.
- **FR-DH-16:** Department heads shall auto-enroll students into courses.

#### 3.4.5 Course Management
- **FR-DH-17:** Department heads shall view, register, and edit courses in their department.
- **FR-DH-18:** Department heads shall assign instructors to courses.

#### 3.4.6 Reports & Exports
- **FR-DH-19:** Department heads shall generate and export the following reports:
  - Student performance report
  - Course performance report
  - Exam participation report
  - Examination schedule report
  - Instructor compliance report
  - Question bank quality report
  - All-reports export
- **FR-DH-20:** Reports shall be exportable as Excel/CSV files.

#### 3.4.7 Issue Management
- **FR-DH-21:** Department heads shall view and manage technical issues reported in their department.

#### 3.4.8 Profile & Settings
- **FR-DH-22:** Department heads shall manage their profile and change their password.

---

### 3.5 Administrator Module

#### 3.5.1 Dashboard
- **FR-ADM-01:** The admin dashboard shall display system-wide statistics: total students, instructors, exams, departments.

#### 3.5.2 User Management
- **FR-ADM-02:** Administrators shall perform full CRUD operations on students.
- **FR-ADM-03:** Administrators shall perform full CRUD operations on instructors.
- **FR-ADM-04:** Administrators shall perform full CRUD operations on department heads.
- **FR-ADM-05:** Administrators shall bulk import users via CSV/Excel.
- **FR-ADM-06:** Administrators shall reset any user's password.

#### 3.5.3 Academic Structure Management
- **FR-ADM-07:** Administrators shall manage faculties (create, edit, delete).
- **FR-ADM-08:** Administrators shall manage departments (create, edit, delete).
- **FR-ADM-09:** Administrators shall manage courses (create, edit, delete).

#### 3.5.4 System Configuration
- **FR-ADM-10:** Administrators shall configure system-wide settings (institution name, logo, etc.).
- **FR-ADM-11:** Administrators shall manage their own profile.

#### 3.5.5 Security & Audit
- **FR-ADM-12:** Administrators shall view security/audit logs of system activity.
- **FR-ADM-13:** The system shall log all significant actions (login, exam submission, approval, etc.).

#### 3.5.6 Database Backup
- **FR-ADM-14:** Administrators shall trigger a database backup from the UI.
- **FR-ADM-15:** Administrators shall download existing backup files.

#### 3.5.7 Reports
- **FR-ADM-16:** Administrators shall generate comprehensive system reports.

#### 3.5.8 Technical Issues
- **FR-ADM-17:** Administrators shall view and manage all reported technical issues system-wide.
- **FR-ADM-18:** Administrators shall view issue details and update issue status.

#### 3.5.9 Global Search
- **FR-ADM-19:** Administrators shall perform a global search across students, instructors, courses, and departments.

---

## 4. Non-Functional Requirements

### 4.1 Performance
- **NFR-PERF-01:** API responses shall complete within 500ms under normal load (< 100 concurrent users).
- **NFR-PERF-02:** The Angular SPA initial load shall complete within 3 seconds on a standard broadband connection.
- **NFR-PERF-03:** Exam auto-save shall not introduce noticeable UI lag (debounced, async).
- **NFR-PERF-04:** The system shall support at least 200 concurrent exam sessions.

### 4.2 Security
- **NFR-SEC-01:** All API endpoints shall require JWT authentication except login and password reset.
- **NFR-SEC-02:** Django shall enforce RBAC — each endpoint checks the user's role before processing.
- **NFR-SEC-03:** Passwords shall be hashed using Django's default PBKDF2 (or bcrypt) algorithm.
- **NFR-SEC-04:** The system shall protect against SQL injection via Django ORM parameterized queries.
- **NFR-SEC-05:** The system shall protect against XSS via Angular's built-in template sanitization.
- **NFR-SEC-06:** CSRF protection shall be enabled for cookie-based sessions; JWT-based flows are inherently CSRF-safe.
- **NFR-SEC-07:** JWT access tokens shall expire after 15 minutes; refresh tokens after 7 days.
- **NFR-SEC-08:** All HTTP communication shall be over HTTPS (TLS 1.2+).
- **NFR-SEC-09:** Django shall implement rate limiting on login and password reset endpoints.
- **NFR-SEC-10:** Sensitive actions shall be logged in the audit log.

### 4.3 Reliability & Availability
- **NFR-REL-01:** The system shall target 99.5% uptime during academic periods.
- **NFR-REL-02:** Exam answers shall be auto-saved every 30 seconds to prevent data loss on disconnection.
- **NFR-REL-03:** The system shall handle WebSocket disconnections gracefully and attempt reconnection.

### 4.4 Usability
- **NFR-USE-01:** The Angular UI shall be fully responsive (mobile, tablet, desktop).
- **NFR-USE-02:** The exam interface shall be distraction-free and clearly display the timer.
- **NFR-USE-03:** Error messages from the API shall be displayed clearly in the Angular UI.
- **NFR-USE-04:** The system shall provide loading indicators for all async operations.

### 4.5 Maintainability
- **NFR-MNT-01:** The Django backend shall follow a modular app structure (one Django app per domain: `exams`, `users`, `courses`, `reports`, etc.).
- **NFR-MNT-02:** The Angular frontend shall follow a feature-module structure with lazy loading.
- **NFR-MNT-03:** All API endpoints shall be documented via drf-spectacular (OpenAPI/Swagger).
- **NFR-MNT-04:** Code shall follow PEP 8 (Django) and Angular style guide conventions.

### 4.6 Scalability
- **NFR-SCA-01:** The Django backend shall be stateless (JWT-based) to allow horizontal scaling.
- **NFR-SCA-02:** Celery workers shall handle background tasks (email, exports) independently.
- **NFR-SCA-03:** The system shall be containerized with Docker for consistent deployment.

### 4.7 Exam Integrity
- **NFR-INT-01:** The exam timer shall be enforced server-side; client-side timer is display-only.
- **NFR-INT-02:** The system shall prevent a student from submitting an exam more than once.
- **NFR-INT-03:** The system shall reject answer submissions after the exam deadline has passed.
- **NFR-INT-04:** Each exam session shall be tied to a unique server-side session token to prevent replay attacks.
- **NFR-INT-05:** Students shall not be able to access exam questions before the scheduled start time.
- **NFR-INT-06:** The system shall detect and log suspicious activity such as repeated rapid answer changes.
- **NFR-INT-07:** Question order and answer options shall be randomizable per student to reduce copying.

### 4.8 Performance Monitoring
- **NFR-MON-01:** The Django backend shall expose a health-check endpoint (`/api/health/`) for uptime monitoring.
- **NFR-MON-02:** Slow API requests (> 1 second) shall be logged for performance analysis.
- **NFR-MON-03:** The system shall track and expose metrics for active exam sessions, API error rates, and response times.
- **NFR-MON-04:** Database query performance shall be monitored; N+1 queries shall be avoided using `select_related` / `prefetch_related`.
- **NFR-MON-05:** Frontend performance shall be measured using Angular's built-in profiling and Lighthouse scores (target: Performance > 80).

### 4.9 Accessibility
- **NFR-ACC-01:** The Angular UI shall conform to WCAG 2.1 Level AA guidelines.
- **NFR-ACC-02:** All interactive elements (buttons, inputs, links) shall be keyboard navigable.
- **NFR-ACC-03:** All images and icons shall have descriptive `alt` text or `aria-label` attributes.
- **NFR-ACC-04:** Color contrast ratios shall meet WCAG AA minimums (4.5:1 for normal text, 3:1 for large text).
- **NFR-ACC-05:** The exam interface shall support screen readers (ARIA roles and live regions for timer updates).
- **NFR-ACC-06:** Form fields shall have associated labels and descriptive error messages.
- **NFR-ACC-07:** The system shall not rely solely on color to convey information (e.g., pass/fail status).

### 4.10 State Management
- **NFR-STATE-01:** Angular shall use a centralized state management solution (NgRx or Angular Signals) for exam session state.
- **NFR-STATE-02:** Exam answers, current question index, and timer state shall be persisted in the Angular store and synced to the server.
- **NFR-STATE-03:** On page refresh during an active exam, the system shall restore the student's session state from the server.
- **NFR-STATE-04:** Navigation away from an active exam shall trigger a confirmation dialog to prevent accidental data loss.
- **NFR-STATE-05:** Authentication state (JWT token, user role) shall be managed centrally and accessible across all feature modules.

---

## 5. System Architecture (Angular + Django)

### 5.1 Django Backend Structure

```
backend/
├── config/                  # Django project settings
│   ├── settings/
│   │   ├── base.py
│   │   ├── development.py
│   │   └── production.py
│   ├── urls.py
│   └── wsgi.py
├── apps/
│   ├── users/               # Custom user model, auth, roles
│   ├── students/            # Student profiles
│   ├── instructors/         # Instructor profiles
│   ├── department_heads/    # Department head profiles
│   ├── courses/             # Courses, departments, faculties
│   ├── exams/               # Exams, questions, question bank
│   ├── results/             # Exam results, answers
│   ├── schedules/           # Exam scheduling
│   ├── approvals/           # Approval workflow
│   ├── notifications/       # In-app notifications
│   ├── reports/             # Report generation
│   ├── issues/              # Technical issue tracking
│   └── audit/               # Audit/security logs
└── requirements.txt
```

### 5.2 Angular Frontend Structure

```
frontend/
├── src/
│   ├── app/
│   │   ├── core/            # Auth service, interceptors, guards
│   │   ├── shared/          # Shared components, pipes, directives
│   │   ├── features/
│   │   │   ├── auth/        # Login, forgot-password pages
│   │   │   ├── student/     # Student feature module (lazy loaded)
│   │   │   ├── instructor/  # Instructor feature module (lazy loaded)
│   │   │   ├── department-head/ # DH feature module (lazy loaded)
│   │   │   └── admin/       # Admin feature module (lazy loaded)
│   │   └── app-routing.module.ts
│   ├── environments/
│   └── assets/
└── angular.json
```

### 5.3 Key API Endpoints (Django REST Framework)

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/login/` | Obtain JWT tokens |
| POST | `/api/auth/refresh/` | Refresh access token |
| POST | `/api/auth/logout/` | Blacklist refresh token |
| POST | `/api/auth/password-reset/` | Request password reset |
| POST | `/api/auth/password-reset/confirm/` | Confirm password reset |
| GET/POST | `/api/exams/` | List / create exams |
| GET/PUT/DELETE | `/api/exams/{id}/` | Retrieve / update / delete exam |
| POST | `/api/exams/{id}/submit-for-approval/` | Submit exam for approval |
| GET/POST | `/api/questions/` | List / create questions |
| GET/POST | `/api/schedules/` | List / create schedules |
| GET/POST | `/api/approvals/` | List pending approvals |
| POST | `/api/approvals/{id}/approve/` | Approve exam |
| POST | `/api/approvals/{id}/reject/` | Reject exam |
| GET/POST | `/api/results/` | List / submit exam results |
| GET | `/api/results/{id}/` | Get result detail |
| GET/POST | `/api/students/` | List / create students |
| GET/POST | `/api/instructors/` | List / create instructors |
| GET/POST | `/api/courses/` | List / create courses |
| GET/POST | `/api/departments/` | List / create departments |
| GET/POST | `/api/issues/` | List / create technical issues |
| GET | `/api/reports/{type}/` | Generate report |
| GET | `/api/audit-logs/` | View audit logs |
| WS | `ws://api/exam/{session_id}/` | WebSocket exam session |

---

## 6. Data Models

### 6.1 Core Models (Django)

```python
# users/models.py
class User(AbstractBaseUser):
    email = EmailField(unique=True)
    role = CharField(choices=['student','instructor','department_head','admin'])
    is_active = BooleanField()

# courses/models.py
class Faculty(Model): name, description
class Department(Model): name, faculty, head
class Course(Model): name, code, department, instructor

# exams/models.py
class Exam(Model):
    title, course, instructor, duration_minutes,
    total_marks, instructions, status  # draft|pending|approved|rejected

class Question(Model):
    exam, text, question_type, marks, correct_answer, options (JSONField)

class QuestionBank(Model):
    course, topic, text, question_type, options, correct_answer, created_by

# results/models.py
class ExamResult(Model):
    student, exam, score, total_marks, submitted_at, status

class ExamAnswer(Model):
    result, question, student_answer, is_correct, marks_awarded

# schedules/models.py
class ExamSchedule(Model):
    exam, start_time, end_time, assigned_students (M2M)

# approvals/models.py
class ExamApproval(Model):
    exam, reviewed_by, decision, comment, reviewed_at

# issues/models.py
class TechnicalIssue(Model):
    reported_by, exam, description, status, created_at

# audit/models.py
class AuditLog(Model):
    user, action, target_model, target_id, ip_address, timestamp
```

---

## 7. External Interface Requirements

### 7.1 User Interfaces
- Angular Material or a compatible UI library shall be used for consistent design.
- The landing page shall display university branding, system stats, and login options.
- Each role shall have a dedicated sidebar navigation and dashboard.

### 7.2 Hardware Interfaces
- No specific hardware interfaces required beyond standard web server infrastructure.

### 7.3 Software Interfaces
- Django REST Framework for API layer.
- PostgreSQL as the primary database.
- Redis for Celery task queue and Django Channels channel layer.
- SMTP server for email notifications (password reset, exam notifications).

### 7.4 Communication Interfaces
- REST over HTTPS for standard API calls.
- WebSocket (WSS) for real-time exam timer and auto-submit.

---

## 8. Constraints and Assumptions

- The Angular frontend is served as a static build (e.g., via Nginx) and communicates with the Django API via a configured base URL.
- Django's `CORS_ALLOWED_ORIGINS` must be configured to allow the Angular origin.
- The system assumes a single institution deployment.
- Email functionality requires a configured SMTP server or email service.
- Bulk import accepts CSV or Excel files; Django will parse them server-side.

---

## 9. Appendix

### 9.1 Exam Workflow (State Machine)

```
[Draft] --> [Pending Approval] --> [Approved] --> [Scheduled] --> [Active] --> [Completed]
                                \-> [Rejected] --> [Draft]
```

### 9.2 Angular Route Structure

```
/                          → Landing page
/login/student             → Student login
/login/staff               → Staff login
/forgot-password           → Password reset request
/student/
  dashboard                → Student dashboard
  exams                    → Available exams
  exam/:id/instructions    → Exam instructions
  exam/:id/start           → Exam interface
  exam/:id/result          → Exam result
  results                  → Results history
  practice                 → Practice selection
  practice/:courseId       → Practice mode
  profile                  → Profile
  notifications            → Notifications
  issues                   → Reported issues
/instructor/
  dashboard                → Instructor dashboard
  exams                    → Exam management
  exams/create             → Create exam
  exams/:id/edit           → Edit exam
  exams/:id/questions      → Manage questions
  question-bank            → Question bank
  topics                   → Topic management
  practice-questions       → Practice questions
  schedules                → Schedule management
  results                  → Results overview
  analytics                → Analytics
  courses                  → My courses
  students                 → View students
  issues                   → Issue tracking
  profile                  → Profile
/department-head/
  dashboard                → DH dashboard
  pending-approvals        → Pending approvals
  approved-exams           → Approved exams
  approval-history         → Approval history
  exams                    → Department exams
  monitor                  → Monitor exams
  students                 → Student management
  courses                  → Course management
  reports                  → Reports
  issues                   → Issue management
  profile                  → Profile
/admin/
  dashboard                → Admin dashboard
  students                 → Student management
  instructors              → Instructor management
  department-heads         → DH management
  faculties                → Faculty management
  departments              → Department management
  courses                  → Course management
  reports                  → Reports
  security-logs            → Audit logs
  backup                   → Database backup
  settings                 → System settings
  issues                   → Technical issues
  search                   → Global search
  profile                  → Profile
```
