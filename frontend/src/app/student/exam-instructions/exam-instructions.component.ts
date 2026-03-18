import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-exam-instructions',
    standalone: true,
    imports: [CommonModule, RouterModule],
    templateUrl: './exam-instructions.component.html'
})
export class ExamInstructionsComponent implements OnInit {
    exam: any = null;
    loading = true;
    examId = 0;

    constructor(private route: ActivatedRoute, private api: ApiService) { }

    ngOnInit() {
        this.examId = Number(this.route.snapshot.paramMap.get('id'));
        this.api.getExam(this.examId).subscribe({
            next: e => { this.exam = e; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }
}
