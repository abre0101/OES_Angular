import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';
import { forkJoin } from 'rxjs';

@Component({
    selector: 'app-results-overview',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './results-overview.component.html'
})
export class ResultsOverviewComponent implements OnInit {
    allResults: any[] = [];
    filtered: any[] = [];
    exams: any[] = [];
    courses: any[] = [];
    loading = true;
    filterExam = '';
    filterPass = '';
    search = '';
    stats = { total: 0, unique: 0, avg: 0, passRate: 0, highest: 0, lowest: 0 };

    constructor(private api: ApiService) { }

    ngOnInit() {
        forkJoin({ results: this.api.getResults(), exams: this.api.getExams(), courses: this.api.getMyCourses() }).subscribe({
            next: d => {
                this.allResults = d.results;
                this.filtered = d.results;
                this.exams = d.exams;
                this.courses = d.courses;
                this.calcStats(d.results);
                this.loading = false;
            },
            error: () => { this.loading = false; }
        });
    }

    calcStats(r: any[]) {
        if (!r.length) return;
        const scores = r.map((x: any) => x.percentage);
        this.stats.total = r.length;
        this.stats.unique = new Set(r.map((x: any) => x.student_id)).size;
        this.stats.avg = Math.round(scores.reduce((a, b) => a + b, 0) / scores.length);
        this.stats.passRate = Math.round(r.filter((x: any) => x.passed).length / r.length * 100);
        this.stats.highest = Math.max(...scores);
        this.stats.lowest = Math.min(...scores);
    }

    applyFilter() {
        this.filtered = this.allResults.filter(r => {
            const matchExam = !this.filterExam || r.exam_id?.toString() === this.filterExam;
            const matchPass = !this.filterPass || (this.filterPass === 'pass' ? r.passed : !r.passed);
            const matchSearch = !this.search ||
                r.student_name?.toLowerCase().includes(this.search.toLowerCase()) ||
                r.exam_title?.toLowerCase().includes(this.search.toLowerCase());
            return matchExam && matchPass && matchSearch;
        });
        this.calcStats(this.filtered);
    }

    scoreClass(p: number): string {
        if (p >= 85) return 'badge-success';
        if (p >= 70) return 'badge-info';
        if (p >= 50) return 'badge-warning';
        return 'badge-danger';
    }
}
