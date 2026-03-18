import { Component } from '@angular/core';
import { LayoutComponent, NavItem } from '../shared/layout/layout.component';

@Component({
    selector: 'app-admin-shell',
    standalone: true,
    imports: [LayoutComponent],
    template: `<app-layout title="Administrator" subtitle="DMU" [navItems]="nav" />`
})
export class AdminShellComponent {
    nav: NavItem[] = [
        { label: 'Dashboard', icon: '📊', route: '/admin/dashboard' },
        { label: 'Students', icon: '👨‍🎓', route: '/admin/students' },
        { label: 'Instructors', icon: '👨‍🏫', route: '/admin/instructors' },
        { label: 'Dept. Heads', icon: '👔', route: '/admin/department-heads' },
        { label: 'Courses', icon: '📚', route: '/admin/courses' },
        { label: 'Departments', icon: '�', route: '/admin/departments' },
        { label: 'Colleges', icon: '🏛️', route: '/admin/faculties' },
        { label: 'Bulk Import', icon: '📥', route: '/admin/bulk-import' },
        { label: 'Issues', icon: '⚠️', route: '/admin/issues' },
        { label: 'Reports', icon: '📈', route: '/admin/reports' },
        { label: 'Security Logs', icon: '🔒', route: '/admin/security-logs' },
        { label: 'DB Backup', icon: '💾', route: '/admin/database-backup' },
        { label: 'Settings', icon: '⚙️', route: '/admin/settings' },
        { label: 'Search', icon: '🔍', route: '/admin/search' },
        { label: 'Profile', icon: '👤', route: '/admin/profile' },
    ];
}
