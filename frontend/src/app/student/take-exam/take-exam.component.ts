import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-take-exam',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './take-exam.component.html',
    styleUrls: ['./take-exam.component.css']
})
export class TakeExamComponent implements OnInit, OnDestroy {
    examId!: number;
    exam: any = null;
    questions: any[] = [];
    answers: Record<number, string> = {};
    loading = true;
    submitting = false;
    error = '';
    timeLeft = 0;
    private timer: any;
    started = false;

    constructor(private api: ApiService, private route: ActivatedRoute, private router: Router) { }

    ngOnInit() {
        this.examId = +this.route.snapshot.params['id'];
        this.api.startExam(this.examId).subscribe({
            next: data => {
                this.exam = data.exam;
                this.questions = data.questions;
                this.timeLeft = this.exam.duration_minutes * 60;
                this.loading = false;
                this.started = true;
                this.startTimer();
            },
            error: err => {
                this.error = err.error?.error ?? 'Failed to load exam.';
                this.loading = false;
            }
        });
    }

    ngOnDestroy() { clearInterval(this.timer); }

    startTimer() {
        this.timer = setInterval(() => {
            this.timeLeft--;
            if (this.timeLeft <= 0) {
                clearInterval(this.timer);
                this.submit();
            }
        }, 1000);
    }

    get timeDisplay() {
        const m = Math.floor(this.timeLeft / 60).toString().padStart(2, '0');
        const s = (this.timeLeft % 60).toString().padStart(2, '0');
        return `${m}:${s}`;
    }

    get answeredCount() {
        return Object.values(this.answers).filter(a => a).length;
    }

    submit() {
        if (this.submitting) return;
        this.submitting = true;
        clearInterval(this.timer);
        const answers = this.questions.map(q => ({
            question: q.id,
            student_answer: this.answers[q.id] ?? ''
        }));
        this.api.submitExam(this.examId, answers).subscribe({
            next: result => this.router.navigate(['/student/results', result.id]),
            error: err => {
                this.error = err.error?.error ?? 'Submission failed.';
                this.submitting = false;
            }
        });
    }
}
