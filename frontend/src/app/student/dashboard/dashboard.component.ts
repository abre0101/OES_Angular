import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';
import { AuthService } from '../../core/services/auth.service';
import { forkJoin } from 'rxjs';

@Component({
  selector: 'app-student-dashboard',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.css'
})
export class StudentDashboardComponent implements OnInit {
  availableExams = 0;
  completedExams = 0;
  avgScore = 0;
  user: any = {};
  loading = true;

  constructor(private api: ApiService, public auth: AuthService) { }

  ngOnInit() {
    forkJoin({ exams: this.api.getExams(), results: this.api.getResults(), me: this.api.getMe() }).subscribe({
      next: data => {
        this.availableExams = data.exams.length;
        this.completedExams = data.results.length;
        if (data.results.length) {
          this.avgScore = Math.round(data.results.reduce((s: number, r: any) => s + r.percentage, 0) / data.results.length);
        }
        this.user = data.me;
        this.loading = false;
      },
      error: () => { this.loading = false; }
    });
  }
}
