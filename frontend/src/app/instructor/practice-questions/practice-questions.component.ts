import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-instructor-practice-questions',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './practice-questions.component.html'
})
export class InstructorPracticeQuestionsComponent implements OnInit {
    questions: any[] = [];
    courses: any[] = [];
    loading = true;
    showForm = false;
    saving = false;
    editing: any = null;
    error = '';
    form: any = { course: '', question_text: '', option_a: '', option_b: '', option_c: '', option_d: '', correct_answer: 'A', explanation: '' };

    constructor(private api: ApiService) { }

    ngOnInit() {
        this.api.getMyCourses().subscribe(c => this.courses = c);
        this.load();
    }

    load() {
        this.api.getPracticeQuestions().subscribe({
            next: d => { this.questions = d; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    openForm(q?: any) {
        this.editing = q ?? null;
        this.form = q ? { ...q } : { course: '', question_text: '', option_a: '', option_b: '', option_c: '', option_d: '', correct_answer: 'A', explanation: '' };
        this.showForm = true;
        this.error = '';
    }

    save() {
        if (!this.form.question_text || !this.form.course) { this.error = 'Course and question text are required.'; return; }
        this.saving = true; this.error = '';
        const req = this.editing
            ? this.api.updatePracticeQuestion(this.editing.id, this.form)
            : this.api.createPracticeQuestion(this.form);
        req.subscribe({
            next: () => { this.load(); this.showForm = false; this.saving = false; },
            error: () => { this.error = 'Failed to save.'; this.saving = false; }
        });
    }

    delete(id: number) {
        if (!confirm('Delete this question?')) return;
        this.api.deletePracticeQuestion(id).subscribe(() => this.questions = this.questions.filter(q => q.id !== id));
    }
}
