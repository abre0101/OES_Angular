import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../core/services/api.service';
import { forkJoin } from 'rxjs';

@Component({
    selector: 'app-instructor-reports',
    standalone: true,
    imports: [CommonModule],
    templateUrl: './reports.component.html'
})
export class InstructorReportsComponent implements OnInit {
    loading = true;
    exams: any[] = [];
    results: any[] = [];
    stats = { totalExams: 0, activeExams: 0, pendingExams: 0, totalResults: 0, passedResults: 0, avgScore: 0 };

    constructor(private api: ApiService) { }

    ngOnInit() {
        forkJoin({ exams: this.api.getExams(), results: this.api.getResults() }).subscribe({
            next: d => {
                this.exams = d.exams;
                this.results = d.results;
                this.stats.totalExams = d.exams.length;
                this.stats.activeExams = d.exams.filter((e: any) => e.status === 'active').length;
                this.stats.pendingExams = d.exams.filter((e: any) => e.status === 'pending').length;
                this.stats.totalResults = d.results.length;
                this.stats.passedResults = d.results.filter((r: any) => r.passed).length;
                this.stats.avgScore = d.results.length
                    ? Math.round(d.results.reduce((s: number, r: any) => s + r.percentage, 0) / d.results.length) : 0;
                this.loading = false;
            },
            error: () => { this.loading = false; }
        });
    }

    get passRate() {
        return this.stats.totalResults ? Math.round((this.stats.passedResults / this.stats.totalResults) * 100) : 0;
    }
}
