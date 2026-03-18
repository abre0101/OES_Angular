import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-review-answers',
    standalone: true,
    imports: [CommonModule, RouterModule],
    templateUrl: './review-answers.component.html'
})
export class ReviewAnswersComponent implements OnInit {
    result: any = null;
    loading = true;

    constructor(private route: ActivatedRoute, private api: ApiService) { }

    ngOnInit() {
        const id = Number(this.route.snapshot.paramMap.get('id'));
        this.api.getResult(id).subscribe({
            next: r => { this.result = r; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    answerClass(q: any): string {
        if (q.is_correct) return 'correct';
        return 'incorrect';
    }
}
