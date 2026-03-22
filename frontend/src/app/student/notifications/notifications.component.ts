import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';

@Component({
  selector: 'app-notifications',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './notifications.component.html',
  styleUrl: './notifications.component.css'
})
export class NotificationsComponent implements OnInit, OnDestroy {
  activeExams: any[] = [];
  resolvedIssues: any[] = [];
  loading = true;
  prefs = { newExams: true, results: true, issueUpdates: true, reminders: false };
  private refreshTimer: any;

  constructor(private api: ApiService) { }

  ngOnInit() {
    this.load();
    this.refreshTimer = setInterval(() => this.load(), 60000);
  }

  ngOnDestroy() {
    clearInterval(this.refreshTimer);
  }

  load() {
    this.api.getExams().subscribe(exams => {
      this.activeExams = exams.filter((e: any) => e.status === 'active');
    });
    this.api.getIssues().subscribe({
      next: issues => {
        this.resolvedIssues = issues.filter((i: any) => i.status === 'resolved');
        this.loading = false;
      },
      error: () => { this.loading = false; }
    });
  }

  get unreadCount(): number {
    return [...this.activeExams, ...this.resolvedIssues].filter(n => !n._read).length;
  }

  get totalCount(): number {
    return this.activeExams.length + this.resolvedIssues.length;
  }

  markRead(item: any) { item._read = true; }

  markAllRead() {
    [...this.activeExams, ...this.resolvedIssues].forEach(n => n._read = true);
  }
}
