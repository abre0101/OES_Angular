import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-student-issues',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './issues.component.html'
})
export class StudentIssuesComponent implements OnInit {
    issues: any[] = [];
    loading = true;
    showForm = false;
    form: any = {};
    error = '';
    success = '';

    constructor(private api: ApiService) { }

    ngOnInit() { this.load(); }

    load() {
        this.loading = true;
        this.api.getIssues().subscribe({
            next: d => { this.issues = d; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    submit() {
        this.api.createIssue(this.form).subscribe({
            next: () => { this.showForm = false; this.form = {}; this.success = 'Issue reported successfully.'; this.load(); },
            error: err => { this.error = JSON.stringify(err.error); }
        });
    }
}
