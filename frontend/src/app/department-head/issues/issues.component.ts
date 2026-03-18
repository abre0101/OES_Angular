import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-dh-issues',
    standalone: true,
    imports: [CommonModule],
    templateUrl: './issues.component.html'
})
export class DhIssuesComponent implements OnInit {
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
}
