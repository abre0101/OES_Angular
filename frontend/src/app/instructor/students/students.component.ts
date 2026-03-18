import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../core/services/api.service';

@Component({
  selector: 'app-instructor-students',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './students.component.html'
})
export class InstructorStudentsComponent implements OnInit {
  results: any[] = [];
  loading = true;
  constructor(private api: ApiService) { }
  ngOnInit() {
    this.api.getResults().subscribe({ next: d => { this.results = d; this.loading = false; }, error: () => { this.loading = false; } });
  }
}
