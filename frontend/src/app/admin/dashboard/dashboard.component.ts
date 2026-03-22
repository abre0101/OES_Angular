import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';
import { AuthService } from '../../core/services/auth.service';
import { forkJoin, of } from 'rxjs';
import { catchError } from 'rxjs/operators';

@Component({
    selector: 'app-admin-dashboard',
    standalone: true,
    imports: [CommonModule, RouterModule],
    templateUrl: './dashboard.component.html'
})
export class AdminDashboardComponent implements OnInit {
    stats = { students: 0, activeStudents: 0, instructors: 0, courses: 0, departments: 0, pendingExams: 0, totalExams: 0 };
    recentStudents: any[] = [];
    recentIssues: any[] = [];
    loading = true;
    username = '';

    constructor(private api: ApiService, private auth: AuthService) { }

    ngOnInit() {
        this.username = this.auth.currentUser()?.username ?? 'Admin';
        forkJoin({
            students: this.api.getUsers('student').pipe(catchError(() => of([]))),
            instructors: this.api.getUsers('instructor').pipe(catchError(() => of([]))),
            courses: this.api.getCourses().pipe(catchError(() => of([]))),
            departments: this.api.getDepartments().pipe(catchError(() => of([]))),
            exams: this.api.getExams().pipe(catchError(() => of([]))),
            issues: this.api.getIssues().pipe(catchError(() => of([]))),
        }).subscribe({
            next: data => {
                this.stats.students = data.students.length;
                this.stats.activeStudents = data.students.filter((s: any) => s.is_active).length;
                this.stats.instructors = data.instructors.length;
                this.stats.courses = data.courses.length;
                this.stats.departments = data.departments.length;
                this.stats.pendingExams = data.exams.filter((e: any) => e.status === 'pending').length;
                this.stats.totalExams = data.exams.length;
                this.recentStudents = data.students.slice(0, 5);
                this.recentIssues = data.issues.slice(0, 5);
                this.loading = false;
            },
            error: () => { this.loading = false; }
        });
    }
}
