import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../core/services/api.service';
import { forkJoin } from 'rxjs';

@Component({
    selector: 'app-performance-reports',
    standalone: true,
    imports: [CommonModule],
    templateUrl: './performance-reports.component.html'
})
export class PerformanceReportsComponent implements OnInit {
    loading = true;
    stats = { total: 0, passed: 0, failed: 0, passRate: 0 };
    topStudents: any[] = [];
    coursePerformance: any[] = [];
    results: any[] = [];

    constructor(private api: ApiService) { }

    ngOnInit() {
        forkJoin({ results: this.api.getResults(), courses: this.api.getCourses(), students: this.api.getUsers('student') }).subscribe({
            next: d => {
                this.results = d.results;
                this.stats.total = d.results.length;
                this.stats.passed = d.results.filter((r: any) => r.passed).length;
                this.stats.failed = d.results.filter((r: any) => !r.passed).length;
                this.stats.passRate = d.results.length ? Math.round(this.stats.passed / d.results.length * 100) : 0;

                // Top students by avg score
                const byStudent: Record<string, any> = {};
                d.results.forEach((r: any) => {
                    if (!byStudent[r.student_id]) byStudent[r.student_id] = { name: r.student_name, scores: [], count: 0 };
                    byStudent[r.student_id].scores.push(r.percentage);
                    byStudent[r.student_id].count++;
                });
                this.topStudents = Object.values(byStudent)
                    .map((s: any) => ({ ...s, avg: Math.round(s.scores.reduce((a: number, b: number) => a + b, 0) / s.scores.length) }))
                    .sort((a, b) => b.avg - a.avg).slice(0, 10);

                // Course performance
                this.coursePerformance = d.courses.map((c: any) => {
                    const cr = d.results.filter((r: any) => r.course_name === c.name || r.course_id === c.id);
                    const avg = cr.length ? Math.round(cr.reduce((s: number, r: any) => s + r.percentage, 0) / cr.length) : 0;
                    return { ...c, attempts: cr.length, avg, highest: cr.length ? Math.max(...cr.map((r: any) => r.percentage)) : 0, lowest: cr.length ? Math.min(...cr.map((r: any) => r.percentage)) : 0 };
                }).filter((c: any) => c.attempts > 0);

                this.loading = false;
            },
            error: () => { this.loading = false; }
        });
    }
}
