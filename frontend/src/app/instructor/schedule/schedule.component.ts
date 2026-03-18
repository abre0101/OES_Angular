import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-instructor-schedule',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './schedule.component.html'
})
export class InstructorScheduleComponent implements OnInit {
    schedules: any[] = [];
    exams: any[] = [];
    loading = true;
    showForm = false;
    saving = false;
    error = '';
    form: any = { exam: '', start_time: '', end_time: '', location: '' };

    constructor(private api: ApiService) { }

    ngOnInit() {
        this.api.getExams().subscribe(e => { this.exams = e; });
        this.api.getSchedules().subscribe({
            next: d => { this.schedules = d; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    save() {
        if (!this.form.exam || !this.form.start_time) { this.error = 'Exam and start time are required.'; return; }
        this.saving = true; this.error = '';
        this.api.createSchedule(this.form).subscribe({
            next: s => { this.schedules.unshift(s); this.showForm = false; this.form = { exam: '', start_time: '', end_time: '', location: '' }; this.saving = false; },
            error: () => { this.error = 'Failed to save schedule.'; this.saving = false; }
        });
    }

    delete(id: number) {
        if (!confirm('Delete this schedule?')) return;
        this.api.deleteSchedule(id).subscribe(() => this.schedules = this.schedules.filter(s => s.id !== id));
    }
}
