import { Component, OnInit, signal, HostListener } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, RouterOutlet } from '@angular/router';
import { AuthService } from '../core/services/auth.service';

@Component({
    selector: 'app-student-shell',
    standalone: true,
    imports: [CommonModule, RouterModule, RouterOutlet],
    templateUrl: './student-shell.component.html',
    styleUrl: './student-shell.component.css'
})
export class StudentShellComponent implements OnInit {
    dropdownOpen = signal(false);
    user: any = {};

    constructor(public auth: AuthService) { }

    ngOnInit() {
        this.user = this.auth.currentUser() ?? {};
    }

    get avatarInitials(): string {
        const u = this.auth.currentUser();
        if (!u) return 'S';
        const fn = u.first_name?.[0] ?? '';
        const ln = u.last_name?.[0] ?? '';
        return (fn + ln).toUpperCase() || (u.username?.[0] ?? 'S').toUpperCase();
    }

    toggleDropdown() { this.dropdownOpen.update(v => !v); }

    @HostListener('document:click', ['$event'])
    onDocClick(e: Event) {
        const target = e.target as HTMLElement;
        if (!target.closest('.user-dropdown-wrapper')) {
            this.dropdownOpen.set(false);
        }
    }

    logout() { this.auth.logout(); }
}
