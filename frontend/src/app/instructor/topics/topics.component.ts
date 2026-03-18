import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-instructor-topics',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './topics.component.html'
})
export class InstructorTopicsComponent implements OnInit {
    topics: any[] = [];
    courses: any[] = [];
    loading = true;
    showForm = false;
    saving = false;
    editing: any = null;
    error = '';
    form: any = { course: '', name: '', description: '' };

    constructor(private api: ApiService) { }

    ngOnInit() {
        this.api.getMyCourses().subscribe(c => this.courses = c);
        this.load();
    }

    load() {
        this.api.getTopics().subscribe({
            next: d => { this.topics = d; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    openForm(t?: any) {
        this.editing = t ?? null;
        this.form = t ? { ...t } : { course: '', name: '', description: '' };
        this.showForm = true; this.error = '';
    }

    save() {
        if (!this.form.name || !this.form.course) { this.error = 'Course and name are required.'; return; }
        this.saving = true; this.error = '';
        const req = this.editing ? this.api.updateTopic(this.editing.id, this.form) : this.api.createTopic(this.form);
        req.subscribe({
            next: () => { this.load(); this.showForm = false; this.saving = false; },
            error: () => { this.error = 'Failed to save.'; this.saving = false; }
        });
    }

    delete(id: number) {
        if (!confirm('Delete this topic?')) return;
        this.api.deleteTopic(id).subscribe(() => this.topics = this.topics.filter(t => t.id !== id));
    }
}
