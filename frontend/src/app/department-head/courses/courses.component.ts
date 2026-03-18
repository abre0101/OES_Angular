import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';

@Component({
  selector: 'app-dh-courses',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './courses.component.html'
})
export class DhCoursesComponent implements OnInit {
  courses: any[] = [];
  loading = true;
  constructor(private api: ApiService) { }
  ngOnInit() { this.api.getCourses().subscribe({ next: d => { this.courses = d; this.loading = false; }, error: () => { this.loading = false; } }); }
}
