import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-questions',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './questions.component.html'
})
export class QuestionsComponent implements OnInit {
    questions: any[] = [];
    courses: any[] = [];
    loading = true;
    showForm = false;
    editing: any = null;
    form: any = {};
    error = '';

    constructor(private api: ApiService) { }

    ngOnInit() {
        this.api.getMyCourses().subscribe(c => this.courses = c);
        this.load();
    }

    load() {
        this.loading = true;
        this.api.getQuestions().subscribe({
            next: d => { this.questions = d; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    openCreate() { this.editing = null; this.form = { question_type: 'mcq', points: 1 }; this.showForm = true; this.error = ''; }
    openEdit(q: any) { this.editing = q; this.form = { ...q }; this.showForm = true; this.error = ''; }

    save() {
        const obs = this.editing
            ? this.api.updateQuestion(this.editing.id, this.form)
            : this.api.createQuestion(this.form);
        obs.subscribe({
            next: () => { this.showForm = false; this.load(); },
            error: err => { this.error = JSON.stringify(err.error); }
        });
    }

    delete(id: number) {
        if (!confirm('Delete this question?')) return;
        this.api.deleteQuestion(id).subscribe(() => this.load());
    }
}
