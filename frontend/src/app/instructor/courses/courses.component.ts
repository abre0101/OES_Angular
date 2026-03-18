import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../core/services/api.service';

@Component({
  selector: 'app-instructor-courses',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './courses.component.html'
})
export class InstructorCoursesComponent implements OnInit {
  courses: any[] = [];
  loading = true;
  constructor(private api: ApiService) { }
  ngOnInit() {
    this.api.getMyCourses().subscribe({ next: d => { this.courses = d; this.loading = false; }, error: () => { this.loading = false; } });
  }
}
