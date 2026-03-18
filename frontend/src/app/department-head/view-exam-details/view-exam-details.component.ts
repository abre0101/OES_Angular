import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';
import { forkJoin } from 'rxjs';

@Component({
    selector: 'app-view-exam-details',
    standalone: true,
    imports: [CommonModule, FormsModule, RouterModule],
    templateUrl: './view-exam-details.component.html'
})
export class ViewExamDetailsComponent implements OnInit {
    exam: any = null;
    questions: any[] = [];
    loading = true;
    reviewing = false;
    comment = '';
    message = '';
    messageType = '';

    constructor(private route: ActivatedRoute, private api: ApiService) { }

    ngOnInit() {
        const id = Number(this.route.snapshot.paramMap.get('id'));
        forkJoin({ exam: this.api.getExam(id), questions: this.api.getExamQuestions(id) }).subscribe({
            next: d => { this.exam = d.exam; this.questions = d.questions; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    review(action: 'approve' | 'reject') {
        if (action === 'reject' && !this.comment.trim()) { this.message = 'Please provide a reason for rejection.'; this.messageType = 'error'; return; }
        this.reviewing = true;
        this.api.reviewExam(this.exam.id, action, this.comment).subscribe({
            next: () => {
                this.exam.status = action === 'approve' ? 'approved' : 'rejected';
                this.message = `Exam ${action === 'approve' ? 'approved' : 'rejected'} successfully.`;
                this.messageType = 'success';
                this.reviewing = false;
            },
            error: () => { this.message = 'Action failed.'; this.messageType = 'error'; this.reviewing = false; }
        });
    }
}
