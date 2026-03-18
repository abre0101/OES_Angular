import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-dh-schedule',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './schedule.component.html'
})
export class DhScheduleComponent implements OnInit {
    exams: any[] = [];
    loading = true;
    editingId: string | null = null;
    scheduleForm: any = {};
    success = '';
    error = '';

    constructor(private api: ApiService) { }

    ngOnInit() {
        this.api.getExams().subscribe({
            next: d => { this.exams = d.filter((e: any) => e.status === 'approved'); this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    openSchedule(exam: any) {
        this.editingId = exam.id;
        this.scheduleForm = { start_time: exam.start_time?.slice(0, 16) ?? '', end_time: exam.end_time?.slice(0, 16) ?? '', status: 'active' };
    }

    saveSchedule() {
        this.api.updateExam(this.editingId!, this.scheduleForm).subscribe({
            next: () => { this.success = 'Exam scheduled successfully.'; this.editingId = null; this.ngOnInit(); },
            error: err => { this.error = JSON.stringify(err.error); }
        });
    }
}
