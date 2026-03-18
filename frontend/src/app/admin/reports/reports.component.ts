import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../core/services/api.service';
import { forkJoin } from 'rxjs';

@Component({
    selector: 'app-admin-reports',
    standalone: true,
    imports: [CommonModule],
    templateUrl: './reports.component.html'
})
export class AdminReportsComponent implements OnInit {
    loading = true;
    stats: any = {};
    results: any[] = [];
    exams: any[] = [];

    constructor(private api: ApiService) { }

    ngOnInit() {
        forkJoin({
            students: this.api.getUsers('student'),
            instructors: this.api.getUsers('instructor'),
            deptHeads: this.api.getUsers('department_head'),
            courses: this.api.getCourses(),
            departments: this.api.getDepartments(),
            exams: this.api.getExams(),
            results: this.api.getResults(),
        }).subscribe({
            next: d => {
                this.stats = {
                    students: d.students.length,
                    instructors: d.instructors.length,
                    deptHeads: d.deptHeads.length,
                    courses: d.courses.length,
                    departments: d.departments.length,
                    totalExams: d.exams.length,
                    activeExams: d.exams.filter((e: any) => e.status === 'active').length,
                    pendingExams: d.exams.filter((e: any) => e.status === 'pending').length,
                    totalResults: d.results.length,
                    passedResults: d.results.filter((r: any) => r.passed).length,
                    avgScore: d.results.length ? Math.round(d.results.reduce((s: number, r: any) => s + r.percentage, 0) / d.results.length) : 0,
                };
                this.results = d.results.slice(0, 10);
                this.exams = d.exams;
                this.loading = false;
            },
            error: () => { this.loading = false; }
        });
    }

    get passRate() {
        if (!this.stats.totalResults) return 0;
        return Math.round((this.stats.passedResults / this.stats.totalResults) * 100);
    }
}
