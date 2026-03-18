import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';

@Component({
  selector: 'app-student-exams',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './exams.component.html'
})
export class StudentExamsComponent implements OnInit {
  exams: any[] = [];
  loading = true;

  constructor(private api: ApiService) { }

  ngOnInit() {
    this.api.getExams().subscribe({
      next: d => { this.exams = d; this.loading = false; },
      error: () => { this.loading = false; }
    });
  }
}
