import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';
import { forkJoin } from 'rxjs';

@Component({
    selector: 'app-view-student',
    standalone: true,
    imports: [CommonModule, RouterModule],
    templateUrl: './view-student.component.html'
})
export class ViewStudentComponent implements OnInit {
    student: any = null;
    results: any[] = [];
    loading = true;

    constructor(private route: ActivatedRoute, private api: ApiService) { }

    ngOnInit() {
        const id = Number(this.route.snapshot.paramMap.get('id'));
        forkJoin({ user: this.api.getUser(id), results: this.api.getResults() }).subscribe({
            next: d => {
                this.student = d.user;
                this.results = d.results.filter((r: any) => r.student_id === id);
                this.loading = false;
            },
            error: () => { this.loading = false; }
        });
    }

    get avgScore(): number {
        if (!this.results.length) return 0;
        return Math.round(this.results.reduce((s, r) => s + r.percentage, 0) / this.results.length);
    }
}
