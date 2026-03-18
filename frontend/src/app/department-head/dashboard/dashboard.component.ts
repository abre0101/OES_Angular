import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';

@Component({
  selector: 'app-dh-dashboard',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './dashboard.component.html'
})
export class DhDashboardComponent implements OnInit {
  pending = 0; approved = 0; total = 0;
  loading = true;

  constructor(private api: ApiService) { }

  ngOnInit() {
    this.api.getExams().subscribe({
      next: exams => {
        this.total = exams.length;
        this.pending = exams.filter((e: any) => e.status === 'pending').length;
        this.approved = exams.filter((e: any) => e.status === 'approved').length;
        this.loading = false;
      },
      error: () => { this.loading = false; }
    });
  }
}
