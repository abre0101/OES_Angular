import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-approvals',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './approvals.component.html'
})
export class ApprovalsComponent implements OnInit {
    exams: any[] = [];
    loading = true;
    reviewingExam: any = null;
    comment = '';

    constructor(private api: ApiService) { }

    ngOnInit() { this.load(); }

    load() {
        this.loading = true;
        this.api.getExams().subscribe({
            next: d => { this.exams = d.filter((e: any) => e.status === 'pending'); this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    openReview(exam: any) { this.reviewingExam = exam; this.comment = ''; }

    review(action: 'approve' | 'reject') {
        this.api.reviewExam(this.reviewingExam.id, action, this.comment).subscribe(() => {
            this.reviewingExam = null;
            this.load();
        });
    }
}
