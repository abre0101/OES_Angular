import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-dh-change-password',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './change-password.component.html'
})
export class DhChangePasswordComponent {
    form = { old_password: '', new_password: '', confirm: '' };
    saving = false;
    success = '';
    error = '';
    strength = 0;

    constructor(private api: ApiService) { }

    checkStrength() {
        const p = this.form.new_password;
        let s = 0;
        if (p.length >= 6) s++;
        if (/[A-Z]/.test(p)) s++;
        if (/[a-z]/.test(p)) s++;
        if (/[0-9]/.test(p)) s++;
        this.strength = s;
    }

    get strengthLabel(): string { return ['', 'Weak', 'Medium', 'Good', 'Strong'][this.strength] ?? ''; }
    get strengthColor(): string { return ['', '#dc3545', '#ffc107', '#17a2b8', '#28a745'][this.strength] ?? ''; }
    get strengthWidth(): string { return `${this.strength * 25}%`; }

    get hasUppercase(): boolean { return /[A-Z]/.test(this.form.new_password); }
    get hasLowercase(): boolean { return /[a-z]/.test(this.form.new_password); }
    get hasNumber(): boolean { return /[0-9]/.test(this.form.new_password); }

    submit() {
        this.error = ''; this.success = '';
        if (!this.form.old_password || !this.form.new_password) { this.error = 'All fields are required.'; return; }
        if (this.form.new_password !== this.form.confirm) { this.error = 'Passwords do not match.'; return; }
        if (this.form.new_password.length < 6) { this.error = 'Password must be at least 6 characters.'; return; }
        this.saving = true;
        this.api.changePassword({ old_password: this.form.old_password, new_password: this.form.new_password }).subscribe({
            next: () => { this.success = 'Password changed successfully.'; this.form = { old_password: '', new_password: '', confirm: '' }; this.strength = 0; this.saving = false; },
            error: err => { this.error = err.error?.error ?? 'Failed to change password.'; this.saving = false; }
        });
    }
}
