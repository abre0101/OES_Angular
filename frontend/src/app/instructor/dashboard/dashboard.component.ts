import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';
import { forkJoin } from 'rxjs';

@Component({
  selector: 'app-instructor-dashboard',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './dashboard.component.html'
})
export class InstructorDashboardComponent implements OnInit {
  stats = { exams: 0, pending: 0, questions: 0, courses: 0 };
  loading = true;

  constructor(private api: ApiService) { }

  ngOnInit() {
    forkJoin({
      exams: this.api.getExams(),
      questions: this.api.getQuestions(),
      courses: this.api.getMyCourses(),
    }).subscribe({
      next: data => {
        this.stats.exams = data.exams.length;
        this.stats.pending = data.exams.filter((e: any) => e.status === 'pending').length;
        this.stats.questions = data.questions.length;
        this.stats.courses = data.courses.length;
        this.loading = false;
      },
      error: () => { this.loading = false; }
    });
  }
}
