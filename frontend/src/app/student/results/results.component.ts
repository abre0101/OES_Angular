import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';

@Component({
  selector: 'app-student-results',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './results.component.html'
})
export class StudentResultsComponent implements OnInit {
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
