import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-register-course',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './register-course.component.html'
})
export class RegisterCourseComponent implements OnInit {
    departments: any[] = [];
    saving = false;
    success = '';
    error = '';
    form: any = { name: '', code: '', credit_hours: 3, semester: '', description: '', department_id: '' };

    constructor(private api: ApiService) { }

    ngOnInit() {
        this.api.getDepartments().subscribe(d => this.departments = d);
    }

    submit() {
        if (!this.form.name || !this.form.code) { this.error = 'Course name and code are required.'; return; }
        this.saving = true; this.error = ''; this.success = '';
        this.api.createCourse(this.form).subscribe({
            next: () => {
                this.success = `Course "${this.form.name}" registered successfully.`;
                this.form = { name: '', code: '', credit_hours: 3, semester: '', description: '', department_id: '' };
                this.saving = false;
            },
            error: err => { this.error = err.error?.detail ?? 'Failed to register course.'; this.saving = false; }
        });
    }
}
