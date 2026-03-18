import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';
import { AuthService } from '../../core/services/auth.service';

@Component({
    selector: 'app-profile',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './profile.component.html'
})
export class ProfileComponent implements OnInit {
    user: any = {};
    form: any = {};
    pwForm = { old_password: '', new_password: '', confirm: '' };
    loading = true;
    saving = false;
    savingPw = false;
    success = '';
    error = '';
    pwError = '';
    pwSuccess = '';
    editMode = false;

    constructor(private api: ApiService, public auth: AuthService) { }

    ngOnInit() {
        this.api.getMe().subscribe({
            next: u => { this.user = u; this.form = { ...u }; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    saveProfile() {
        this.saving = true; this.error = ''; this.success = '';
        this.api.updateMe(this.form).subscribe({
            next: u => { this.user = u; this.saving = false; this.editMode = false; this.success = 'Profile updated successfully.'; },
            error: err => { this.error = JSON.stringify(err.error); this.saving = false; }
        });
    }

    changePassword() {
        this.pwError = ''; this.pwSuccess = '';
        if (this.pwForm.new_password !== this.pwForm.confirm) { this.pwError = 'Passwords do not match.'; return; }
        this.savingPw = true;
        this.api.changePassword({ old_password: this.pwForm.old_password, new_password: this.pwForm.new_password }).subscribe({
            next: () => { this.pwSuccess = 'Password changed successfully.'; this.pwForm = { old_password: '', new_password: '', confirm: '' }; this.savingPw = false; },
            error: err => { this.pwError = err.error?.error ?? 'Failed to change password.'; this.savingPw = false; }
        });
    }
}
