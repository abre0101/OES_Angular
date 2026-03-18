import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-practice',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './practice.component.html'
})
export class PracticeComponent implements OnInit {
    questions: any[] = [];
    loading = true;
    answers: Record<string, string> = {};
    submitted = false;
    score = 0;
    courses: any[] = [];
    selectedCourse = '';

    constructor(private api: ApiService) { }

    ngOnInit() {
        this.api.getMyCourses().subscribe(c => this.courses = c);
        this.loadQuestions();
    }

    loadQuestions() {
        this.loading = true;
        this.submitted = false;
        this.answers = {};
        this.score = 0;
        const params: any = {};
        if (this.selectedCourse) params['course'] = this.selectedCourse;
        this.api.getPracticeQuestions(params).subscribe({
            next: d => { this.questions = d; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    submit() {
        let correct = 0;
        this.questions.forEach(q => {
            if ((this.answers[q.id] ?? '').trim().toLowerCase() === q.correct_answer?.trim().toLowerCase()) correct++;
        });
        this.score = correct;
        this.submitted = true;
    }

    isCorrect(q: any) { return (this.answers[q.id] ?? '').trim().toLowerCase() === q.correct_answer?.trim().toLowerCase(); }
    get percentage() { return this.questions.length ? Math.round((this.score / this.questions.length) * 100) : 0; }
}
