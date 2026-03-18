import { Component } from '@angular/core';
import { LayoutComponent, NavItem } from '../shared/layout/layout.component';

@Component({
    selector: 'app-dh-shell',
    standalone: true,
    imports: [LayoutComponent],
    template: `<app-layout title="Department Head" subtitle="DMU" [navItems]="nav" />`
})
export class DhShellComponent {
    nav: NavItem[] = [
        { label: 'Dashboard', icon: '📊', route: '/department-head/dashboard' },
        { label: 'Student Management', icon: '👨‍🎓', route: '/department-head/students' },
        { label: 'Course Management', icon: '📚', route: '/department-head/courses' },
        { label: 'Assign Instructor', icon: '👨‍🏫', route: '/department-head/assign-instructor' },
        { label: 'Exam Scheduling', icon: '📅', route: '/department-head/schedule' },
        { label: 'Pending Approvals', icon: '⏳', route: '/department-head/approvals' },
        { label: 'Department Exams', icon: '📋', route: '/department-head/exams' },
        { label: 'Monitor Exams', icon: '👁️', route: '/department-head/monitor-exams' },
        { label: 'Results', icon: '📈', route: '/department-head/results' },
        { label: 'Reports', icon: '📊', route: '/department-head/reports' },
        { label: 'Performance', icon: '📉', route: '/department-head/performance-reports' },
        { label: 'Issues', icon: '⚠️', route: '/department-head/issues' },
        { label: 'Bulk Import', icon: '📥', route: '/department-head/bulk-import' },
        { label: 'Reg. Student', icon: '➕', route: '/department-head/register-student' },
        { label: 'Reg. Course', icon: '📖', route: '/department-head/register-course' },
        { label: 'Search', icon: '🔍', route: '/department-head/search' },
        { label: 'Change Password', icon: '🔒', route: '/department-head/change-password' },
        { label: 'Profile', icon: '👤', route: '/department-head/profile' },
    ];
}
