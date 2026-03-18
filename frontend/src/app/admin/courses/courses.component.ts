import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-courses',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './courses.component.html'
})
export class CoursesComponent implements OnInit {
    courses: any[] = [];
    departments: any[] = [];
    loading = true;
    showForm = false;
    editingCourse: any = null;
    form: any = {};
    error = '';

    constructor(private api: ApiService) { }

    ngOnInit() {
        this.api.getDepartments().subscribe(d => this.departments = d);
        this.load();
    }

    load() {
        this.loading = true;
        this.api.getCourses().subscribe({
            next: data => { this.courses = data; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    openCreate() { this.editingCourse = null; this.form = {}; this.showForm = true; this.error = ''; }
    openEdit(c: any) { this.editingCourse = c; this.form = { ...c }; this.showForm = true; this.error = ''; }

    save() {
        const obs = this.editingCourse
            ? this.api.updateCourse(this.editingCourse.id, this.form)
            : this.api.createCourse(this.form);
        obs.subscribe({
            next: () => { this.showForm = false; this.load(); },
            error: err => { this.error = JSON.stringify(err.error); }
        });
    }

    delete(id: number) {
        if (!confirm('Delete this course?')) return;
        this.api.deleteCourse(id).subscribe(() => this.load());
    }
}
