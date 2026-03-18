import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { environment } from '../../../environments/environment';

@Injectable({ providedIn: 'root' })
export class ApiService {
    private readonly API = environment.apiUrl;

    constructor(private http: HttpClient) { }

    // Users
    getUsers(role?: string) {
        let params = new HttpParams();
        if (role) params = params.set('role', role);
        return this.http.get<any[]>(`${this.API}/auth/users/`, { params });
    }
    getUser(id: number) { return this.http.get<any>(`${this.API}/auth/users/${id}/`); }
    createUser(data: any) { return this.http.post<any>(`${this.API}/auth/register/`, data); }
    updateUser(id: number, data: any) { return this.http.patch<any>(`${this.API}/auth/users/${id}/`, data); }
    deleteUser(id: number) { return this.http.delete(`${this.API}/auth/users/${id}/`); }
    getMe() { return this.http.get<any>(`${this.API}/auth/me/`); }
    updateMe(data: any) { return this.http.patch<any>(`${this.API}/auth/me/`, data); }
    changePassword(data: any) { return this.http.post<any>(`${this.API}/auth/change-password/`, data); }
    bulkImport(users: any[]) { return this.http.post<any>(`${this.API}/auth/bulk-import/`, { users }); }

    // Courses
    getCourses(params?: any) { return this.http.get<any[]>(`${this.API}/courses/`, { params }); }
    getCourse(id: number) { return this.http.get<any>(`${this.API}/courses/${id}/`); }
    createCourse(data: any) { return this.http.post<any>(`${this.API}/courses/`, data); }
    updateCourse(id: number, data: any) { return this.http.patch<any>(`${this.API}/courses/${id}/`, data); }
    deleteCourse(id: number) { return this.http.delete(`${this.API}/courses/${id}/`); }
    getMyCourses() { return this.http.get<any[]>(`${this.API}/courses/my-courses/`); }

    // Departments & Faculties
    getDepartments() { return this.http.get<any[]>(`${this.API}/courses/departments/`); }
    createDepartment(data: any) { return this.http.post<any>(`${this.API}/courses/departments/`, data); }
    updateDepartment(id: number, data: any) { return this.http.patch<any>(`${this.API}/courses/departments/${id}/`, data); }
    deleteDepartment(id: number) { return this.http.delete(`${this.API}/courses/departments/${id}/`); }
    getFaculties() { return this.http.get<any[]>(`${this.API}/courses/faculties/`); }
    createFaculty(data: any) { return this.http.post<any>(`${this.API}/courses/faculties/`, data); }
    updateFaculty(id: string, data: any) { return this.http.patch<any>(`${this.API}/courses/faculties/${id}/`, data); }
    deleteFaculty(id: string) { return this.http.delete(`${this.API}/courses/faculties/${id}/`); }

    // Enrollments
    getEnrollments(params?: any) { return this.http.get<any[]>(`${this.API}/courses/enrollments/`, { params }); }
    enroll(data: any) { return this.http.post<any>(`${this.API}/courses/enrollments/`, data); }

    // Questions
    getQuestions(params?: any) { return this.http.get<any[]>(`${this.API}/exams/questions/`, { params }); }
    createQuestion(data: any) { return this.http.post<any>(`${this.API}/exams/questions/`, data); }
    updateQuestion(id: number, data: any) { return this.http.patch<any>(`${this.API}/exams/questions/${id}/`, data); }
    deleteQuestion(id: number) { return this.http.delete(`${this.API}/exams/questions/${id}/`); }

    // Exams
    getExams() { return this.http.get<any[]>(`${this.API}/exams/`); }
    getExam(id: number) { return this.http.get<any>(`${this.API}/exams/${id}/`); }
    createExam(data: any) { return this.http.post<any>(`${this.API}/exams/`, data); }
    updateExam(id: number | string, data: any) { return this.http.patch<any>(`${this.API}/exams/${id}/`, data); }
    deleteExam(id: number) { return this.http.delete(`${this.API}/exams/${id}/`); }
    getExamQuestions(examId: number) { return this.http.get<any[]>(`${this.API}/exams/${examId}/questions/`); }
    addExamQuestion(examId: number, data: any) { return this.http.post<any>(`${this.API}/exams/${examId}/questions/`, data); }
    submitForApproval(examId: number) { return this.http.post<any>(`${this.API}/exams/${examId}/submit-for-approval/`, {}); }
    reviewExam(examId: number, action: 'approve' | 'reject', comment: string) {
        return this.http.post<any>(`${this.API}/exams/${examId}/review/`, { action, comment });
    }

    // Taking exams
    startExam(examId: number) { return this.http.get<any>(`${this.API}/exams/${examId}/start/`); }
    submitExam(examId: number, answers: any[]) {
        return this.http.post<any>(`${this.API}/exams/${examId}/submit/`, { answers });
    }

    // Results
    getResults(params?: any) { return this.http.get<any[]>(`${this.API}/exams/results/`, { params }); }
    getResult(id: number) { return this.http.get<any>(`${this.API}/exams/results/${id}/`); }

    // Issues
    getIssues() { return this.http.get<any[]>(`${this.API}/exams/issues/`); }
    createIssue(data: any) { return this.http.post<any>(`${this.API}/exams/issues/`, data); }
    updateIssue(id: number, data: any) { return this.http.patch<any>(`${this.API}/exams/issues/${id}/`, data); }

    // Schedules
    getSchedules(params?: any) { return this.http.get<any[]>(`${this.API}/exams/schedules/`, { params }); }
    createSchedule(data: any) { return this.http.post<any>(`${this.API}/exams/schedules/`, data); }
    updateSchedule(id: number, data: any) { return this.http.patch<any>(`${this.API}/exams/schedules/${id}/`, data); }
    deleteSchedule(id: number) { return this.http.delete(`${this.API}/exams/schedules/${id}/`); }

    // Practice Questions
    getPracticeQuestions(params?: any) { return this.http.get<any[]>(`${this.API}/exams/practice-questions/`, { params }); }
    createPracticeQuestion(data: any) { return this.http.post<any>(`${this.API}/exams/practice-questions/`, data); }
    updatePracticeQuestion(id: number, data: any) { return this.http.patch<any>(`${this.API}/exams/practice-questions/${id}/`, data); }
    deletePracticeQuestion(id: number) { return this.http.delete(`${this.API}/exams/practice-questions/${id}/`); }

    // Topics
    getTopics(params?: any) { return this.http.get<any[]>(`${this.API}/exams/topics/`, { params }); }
    createTopic(data: any) { return this.http.post<any>(`${this.API}/exams/topics/`, data); }
    updateTopic(id: number, data: any) { return this.http.patch<any>(`${this.API}/exams/topics/${id}/`, data); }
    deleteTopic(id: number) { return this.http.delete(`${this.API}/exams/topics/${id}/`); }

    // Notifications
    getNotifications() { return this.http.get<any[]>(`${this.API}/exams/notifications/`); }
    markNotificationRead(id: number) { return this.http.patch<any>(`${this.API}/exams/notifications/${id}/`, { is_read: true }); }

    // Audit / Security Logs
    getAuditLogs(params?: any) { return this.http.get<any[]>(`${this.API}/auth/audit-logs/`, { params }); }

    // Global Search
    globalSearch(q: string) { return this.http.get<any>(`${this.API}/auth/search/?q=${encodeURIComponent(q)}`); }

    // Assign instructor to course
    assignInstructor(data: any) { return this.http.post<any>(`${this.API}/courses/assign-instructor/`, data); }
    getInstructorCourses() { return this.http.get<any[]>(`${this.API}/courses/instructor-courses/`); }
}
