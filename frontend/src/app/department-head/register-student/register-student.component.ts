import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-register-student',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './register-student.component.html'
})
export class RegisterStudentComponent implements OnInit {
    departments: any[] = [];
    saving = false;
    success = '';
    error = '';
    form: any = {
        username: '', full_name: '', email: '', phone: '',
        gender: '', password: '', academic_year: '', semester: '', department_id: '', role: 'student'
    };

    constructor(private api: ApiService) { }

    ngOnInit() {
        this.api.getDepartments().subscribe(d => this.departments = d);
    }

    onNameInput() {
        if (!this.form.full_name) return;
        const parts = this.form.full_name.toLowerCase().trim().split(' ').filter((p: string) => p);
        if (parts.length >= 2) {
            this.form.username = parts[0] + parts[parts.length - 1][0];
        } else if (parts.length === 1) {
            this.form.username = parts[0].substring(0, 6);
        }
    }

    submit() {
        if (!this.form.username || !this.form.full_name || !this.form.email || !this.form.password) {
            this.error = 'Please fill in all required fields.'; return;
        }
        this.saving = true; this.error = ''; this.success = '';
        this.api.createUser(this.form).subscribe({
            next: () => {
                this.success = 'Student registered successfully.';
                this.form = { username: '', full_name: '', email: '', phone: '', gender: '', password: '', academic_year: '', semester: '', department_id: '', role: 'student' };
                this.saving = false;
            },
            error: err => { this.error = err.error?.detail ?? 'Registration failed.'; this.saving = false; }
        });
    }
}
