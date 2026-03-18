import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { Router, RouterModule } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
    selector: 'app-login',
    standalone: true,
    imports: [CommonModule, FormsModule, RouterModule],
    templateUrl: './login.component.html',
    styleUrls: ['./login.component.css']
})
export class LoginComponent {
    username = '';
    password = '';
    error = '';
    loading = false;

    constructor(private auth: AuthService, private router: Router) {
        // Redirect if already logged in
        if (this.auth.isLoggedIn()) this.redirectByRole(this.auth.role);
    }

    onSubmit() {
        if (!this.username || !this.password) {
            this.error = 'Please enter username and password.';
            return;
        }
        this.loading = true;
        this.error = '';
        this.auth.login(this.username, this.password).subscribe({
            next: res => this.redirectByRole(res.user.role),
            error: () => {
                this.error = 'Invalid username or password.';
                this.loading = false;
            }
        });
    }

    private redirectByRole(role: string) {
        const routes: Record<string, string> = {
            admin: '/admin',
            instructor: '/instructor',
            department_head: '/department-head',
            student: '/student',
        };
        this.router.navigate([routes[role] ?? '/login']);
    }
}
