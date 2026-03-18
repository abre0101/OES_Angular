import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';

@Component({
  selector: 'app-notifications',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './notifications.component.html'
})
export class NotificationsComponent implements OnInit {
  activeExams: any[] = [];
  resolvedIssues: any[] = [];
  loading = true;
  constructor(private api: ApiService) { }
  ngOnInit() {
    this.api.getExams().subscribe(exams => { this.activeExams = exams.filter((e: any) => e.status === 'active'); });
    this.api.getIssues().subscribe({ next: issues => { this.resolvedIssues = issues.filter((i: any) => i.status === 'resolved'); this.loading = false; }, error: () => { this.loading = false; } });
  }
}
