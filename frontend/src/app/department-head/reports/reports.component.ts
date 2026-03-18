import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../core/services/api.service';
import { forkJoin } from 'rxjs';

@Component({
    selector: 'app-dh-reports',
    standalone: true,
    imports: [CommonModule],
    templateUrl: './reports.component.html'
})
export class DhReportsComponent implements OnInit {
    loading = true;
    stats: any = {};
    results: any[] = [];

    constructor(private api: ApiService) { }

    ngOnInit() {
        forkJoin({ students: this.api.getUsers('student'), exams: this.api.getExams(), results: this.api.getResults(), courses: this.api.getCourses() }).subscribe({
            next: d => {
                this.stats = {
                    students: d.students.length,
                    courses: d.courses.length,
                    totalExams: d.exams.length,
                    activeExams: d.exams.filter((e: any) => e.status === 'active').length,
                    totalResults: d.results.length,
                    passedResults: d.results.filter((r: any) => r.passed).length,
                    avgScore: d.results.length ? Math.round(d.results.reduce((s: number, r: any) => s + r.percentage, 0) / d.results.length) : 0,
                };
                this.results = d.results.slice(0, 15);
                this.loading = false;
            },
            error: () => { this.loading = false; }
        });
    }

    get passRate() { return this.stats.totalResults ? Math.round((this.stats.passedResults / this.stats.totalResults) * 100) : 0; }
}
