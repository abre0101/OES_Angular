import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-faculties',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './faculties.component.html'
})
export class FacultiesComponent implements OnInit {
    faculties: any[] = [];
    loading = true;
    showForm = false;
    editing: any = null;
    form: any = {};
    error = '';

    constructor(private api: ApiService) { }
    ngOnInit() { this.load(); }

    load() {
        this.loading = true;
        this.api.getFaculties().subscribe({ next: d => { this.faculties = d; this.loading = false; }, error: () => { this.loading = false; } });
    }
    openCreate() { this.editing = null; this.form = {}; this.showForm = true; this.error = ''; }
    openEdit(f: any) { this.editing = f; this.form = { ...f }; this.showForm = true; this.error = ''; }
    save() {
        const obs = this.editing ? this.api.updateFaculty(this.editing.id, this.form) : this.api.createFaculty(this.form);
        obs.subscribe({ next: () => { this.showForm = false; this.load(); }, error: err => { this.error = JSON.stringify(err.error); } });
    }
    delete(id: string) {
        if (!confirm('Delete this college?')) return;
        this.api.deleteFaculty(id).subscribe(() => this.load());
    }
}
