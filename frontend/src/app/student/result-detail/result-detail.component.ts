import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, ActivatedRoute } from '@angular/router';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-result-detail',
    standalone: true,
    imports: [CommonModule, RouterModule],
    templateUrl: './result-detail.component.html'
})
export class ResultDetailComponent implements OnInit {
    result: any = null;
    loading = true;

    constructor(private api: ApiService, private route: ActivatedRoute) { }

    ngOnInit() {
        const id = +this.route.snapshot.params['id'];
        this.api.getResult(id).subscribe({
            next: d => { this.result = d; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }
}
