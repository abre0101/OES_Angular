import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../core/services/api.service';

@Component({
  selector: 'app-instructor-results',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './results.component.html'
})
export class InstructorResultsComponent implements OnInit {
  results: any[] = [];
  loading = true;

  constructor(private api: ApiService) { }

  ngOnInit() {
    this.api.getResults().subscribe({
      next: d => { this.results = d; this.loading = false; },
      error: () => { this.loading = false; }
    });
  }
}
