import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-security-logs',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './security-logs.component.html'
})
export class SecurityLogsComponent implements OnInit {
    logs: any[] = [];
    filtered: any[] = [];
    loading = true;
    filterType = 'all';
    filterDate = '';
    search = '';

    stats = { total: 0, today: 0, failed: 0, success: 0 };

    constructor(private api: ApiService) { }

    ngOnInit() {
        this.api.getAuditLogs().subscribe({
            next: d => {
                this.logs = d;
                this.filtered = d;
                const today = new Date().toDateString();
                this.stats.total = d.length;
                this.stats.today = d.filter((l: any) => new Date(l.created_at).toDateString() === today).length;
                this.stats.failed = d.filter((l: any) => /failed|error|denied/i.test(l.action)).length;
                this.stats.success = d.filter((l: any) => /success|login|created|updated/i.test(l.action)).length;
                this.loading = false;
            },
            error: () => { this.loading = false; }
        });
    }

    applyFilter() {
        this.filtered = this.logs.filter(l => {
            const matchType = this.filterType === 'all' || l.user_type === this.filterType;
            const matchDate = !this.filterDate || l.created_at?.startsWith(this.filterDate);
            const matchSearch = !this.search ||
                l.action?.toLowerCase().includes(this.search.toLowerCase()) ||
                l.user_id?.toString().includes(this.search);
            return matchType && matchDate && matchSearch;
        });
    }

    logClass(log: any): string {
        if (/failed|error|denied/i.test(log.action)) return 'failed';
        if (/delete/i.test(log.action)) return 'warning';
        return 'success';
    }
}
