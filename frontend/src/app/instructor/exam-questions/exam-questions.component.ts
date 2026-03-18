import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-exam-questions',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './exam-questions.component.html'
})
export class ExamQuestionsComponent implements OnInit {
    examId!: number;
    examQuestions: any[] = [];
    allQuestions: any[] = [];
    loading = true;
    selectedQuestionId: number | null = null;
    error = '';

    constructor(private api: ApiService, private route: ActivatedRoute) { }

    ngOnInit() {
        this.examId = +this.route.snapshot.params['id'];
        this.load();
        this.api.getQuestions().subscribe(q => this.allQuestions = q);
    }

    load() {
        this.loading = true;
        this.api.getExamQuestions(this.examId).subscribe({
            next: d => { this.examQuestions = d; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    addQuestion() {
        if (!this.selectedQuestionId) return;
        this.api.addExamQuestion(this.examId, { question_id: this.selectedQuestionId, order: this.examQuestions.length + 1 }).subscribe({
            next: () => { this.selectedQuestionId = null; this.load(); },
            error: err => { this.error = JSON.stringify(err.error); }
        });
    }

    get availableQuestions() {
        const added = new Set(this.examQuestions.map((eq: any) => eq.question?.id));
        return this.allQuestions.filter(q => !added.has(q.id));
    }
}
