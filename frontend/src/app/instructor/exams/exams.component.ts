import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-instructor-exams',
    standalone: true,
    imports: [CommonModule, FormsModule, RouterModule],
    templateUrl: './exams.component.html'
})
export class InstructorExamsComponent implements OnInit {
    exams: any[] = [];
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
        this.api.getExams().subscribe({
            next: d => { this.exams = d; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    openCreate() { this.editing = null; this.form = { duration_minutes: 60 }; this.showForm = true; this.error = ''; }
    openEdit(e: any) { this.editing = e; this.form = { ...e }; this.showForm = true; this.error = ''; }

    save() {
        const obs = this.editing
            ? this.api.updateExam(this.editing.id, this.form)
            : this.api.createExam(this.form);
        obs.subscribe({
            next: () => { this.showForm = false; this.load(); },
            error: err => { this.error = JSON.stringify(err.error); }
        });
    }

    submitForApproval(id: number) {
        if (!confirm('Submit this exam for approval?')) return;
        this.api.submitForApproval(id).subscribe(() => this.load());
    }

    delete(id: number) {
        if (!confirm('Delete this exam?')) return;
        this.api.deleteExam(id).subscribe(() => this.load());
    }

    statusBadge(status: string) {
        const map: Record<string, string> = {
            draft: 'badge-secondary', pending: 'badge-warning',
            approved: 'badge-success', rejected: 'badge-danger',
            active: 'badge-info', completed: 'badge-secondary'
        };
        return map[status] ?? 'badge-secondary';
    }
}
