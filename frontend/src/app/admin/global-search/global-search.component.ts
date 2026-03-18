import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';
import { debounceTime, distinctUntilChanged, Subject, switchMap, catchError, of } from 'rxjs';

@Component({
    selector: 'app-global-search',
    standalone: true,
    imports: [CommonModule, FormsModule, RouterModule],
    templateUrl: './global-search.component.html'
})
export class GlobalSearchComponent {
    query = '';
    results: any = null;
    loading = false;
    private search$ = new Subject<string>();

    constructor(private api: ApiService) {
        this.search$.pipe(
            debounceTime(400),
            distinctUntilChanged(),
            switchMap(q => q.length > 1 ? this.api.globalSearch(q) : of(null)),
            catchError(() => of(null))
        ).subscribe(r => { this.results = r; this.loading = false; });
    }

    onInput() {
        this.loading = true;
        this.search$.next(this.query);
    }

    get hasResults(): boolean {
        if (!this.results) return false;
        return Object.values(this.results).some((v: any) => v?.length > 0);
    }
}
