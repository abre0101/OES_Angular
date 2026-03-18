import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-instructor-view-issues',
    standalone: true,
    imports: [CommonModule],
    templateUrl: './view-issues.component.html'
})
export class InstructorViewIssuesComponent implements OnInit {
    issues: any[] = [];
    loading = true;

    constructor(private api: ApiService) { }

    ngOnInit() { this.load(); }

    load() {
        this.api.getIssues().subscribe({
            next: d => { this.issues = d; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    updateStatus(id: number, status: string) {
        this.api.updateIssue(id, { status }).subscribe(() => this.load());
    }

    statusClass(status: string): string {
        if (status === 'open') return 'badge-danger';
        if (status === 'in_progress') return 'badge-warning';
        return 'badge-success';
    }
}
