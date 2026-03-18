import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-forgot-password',
    standalone: true,
    imports: [CommonModule, FormsModule, RouterModule],
    templateUrl: './forgot-password.component.html',
    styleUrls: ['./forgot-password.component.css']
})
export class ForgotPasswordComponent {
    form = { role: 'student', username: '', email: '', reason: '', details: '' };
    submitted = false;
    error = '';
    loading = false;

    constructor(private api: ApiService) { }

    submit() {
        if (!this.form.username || !this.form.email || !this.form.reason) {
            this.error = 'Please fill in all required fields.';
            return;
        }
        this.loading = true;
        this.error = '';
        // In a real system this would call a backend endpoint.
        // For now we simulate success after a short delay.
        setTimeout(() => {
            this.submitted = true;
            this.loading = false;
        }, 800);
    }
}
