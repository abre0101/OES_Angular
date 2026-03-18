import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-monitor-exams',
    standalone: true,
    imports: [CommonModule],
    templateUrl: './monitor-exams.component.html'
})
export class MonitorExamsComponent implements OnInit {
    exams: any[] = [];
    loading = true;

    constructor(private api: ApiService) { }

    ngOnInit() {
        this.api.getExams().subscribe({
            next: d => { this.exams = d; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    get active() { return this.exams.filter(e => e.status === 'active'); }
    get pending() { return this.exams.filter(e => e.status === 'pending'); }
    get completed() { return this.exams.filter(e => e.status === 'completed'); }
}
