import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';

@Component({
  selector: 'app-student-schedule',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './schedule.component.html'
})
export class StudentScheduleComponent implements OnInit {
  exams: any[] = [];
  loading = true;
  constructor(private api: ApiService) { }
  ngOnInit() {
    this.api.getExams().subscribe({ next: d => { this.exams = d; this.loading = false; }, error: () => { this.loading = false; } });
  }
}
