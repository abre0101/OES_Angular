import { Component } from '@angular/core';
import { LayoutComponent, NavItem } from '../shared/layout/layout.component';

@Component({
    selector: 'app-student-shell',
    standalone: true,
    imports: [LayoutComponent],
    template: `<app-layout title="Student" subtitle="DMU" [navItems]="nav" />`
})
export class StudentShellComponent {
    nav: NavItem[] = [
        { label: 'Dashboard', icon: '📊', route: '/student/dashboard' },
        { label: 'Take Exam', icon: '📝', route: '/student/exams' },
        { label: 'My Results', icon: '📈', route: '/student/results' },
        { label: 'Practice', icon: '🎯', route: '/student/practice' },
        { label: 'Schedule', icon: '📅', route: '/student/schedule' },
        { label: 'Notifications', icon: '🔔', route: '/student/notifications' },
        { label: 'Report Issue', icon: '⚠️', route: '/student/issues' },
        { label: 'Profile', icon: '👤', route: '/student/profile' },
    ];
}
