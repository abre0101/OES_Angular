import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../core/services/api.service';
import { forkJoin } from 'rxjs';

@Component({
    selector: 'app-instructor-analytics',
    standalone: true,
    imports: [CommonModule],
    templateUrl: './analytics.component.html'
})
export class InstructorAnalyticsComponent implements OnInit {
    loading = true;
    stats = { totalQuestions: 0, totalResults: 0, avgScore: 0, passRate: 0 };
    coursePerformance: any[] = [];
    hardQuestions: any[] = [];
    results: any[] = [];

    constructor(private api: ApiService) { }

    ngOnInit() {
        forkJoin({
            questions: this.api.getQuestions(),
            results: this.api.getResults(),
            courses: this.api.getMyCourses(),
        }).subscribe({
            next: d => {
                this.stats.totalQuestions = d.questions.length;
                this.stats.totalResults = d.results.length;
                if (d.results.length) {
                    const total = d.results.reduce((s: number, r: any) => s + r.percentage, 0);
                    this.stats.avgScore = Math.round(total / d.results.length);
                    this.stats.passRate = Math.round((d.results.filter((r: any) => r.passed).length / d.results.length) * 100);
                }
                this.results = d.results;
                // Group results by course
                this.coursePerformance = d.courses.map((c: any) => {
                    const cr = d.results.filter((r: any) => r.course_id === c.id || r.course_name === c.name);
                    const avg = cr.length ? Math.round(cr.reduce((s: number, r: any) => s + r.percentage, 0) / cr.length) : 0;
                    return { ...c, attempts: cr.length, avg, passRate: cr.length ? Math.round(cr.filter((r: any) => r.passed).length / cr.length * 100) : 0 };
                });
                this.loading = false;
            },
            error: () => { this.loading = false; }
        });
    }

    scoreClass(score: number): string {
        if (score >= 85) return 'badge-success';
        if (score >= 70) return 'badge-info';
        if (score >= 50) return 'badge-warning';
        return 'badge-danger';
    }
}
