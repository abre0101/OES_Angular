import { Component } from '@angular/core';
import { LayoutComponent, NavItem } from '../shared/layout/layout.component';

@Component({
    selector: 'app-instructor-shell',
    standalone: true,
    imports: [LayoutComponent],
    template: `<app-layout title="Instructor" subtitle="DMU" [navItems]="nav" />`
})
export class InstructorShellComponent {
    nav: NavItem[] = [
        { label: 'Dashboard', icon: '📊', route: '/instructor/dashboard' },
        { label: 'My Courses', icon: '📚', route: '/instructor/courses' },
        { label: 'Questions', icon: '❓', route: '/instructor/questions' },
        { label: 'My Exams', icon: '📝', route: '/instructor/exams' },
        { label: 'Students', icon: '👨‍🎓', route: '/instructor/students' },
        { label: 'Results', icon: '📈', route: '/instructor/results' },
        { label: 'Schedule', icon: '📅', route: '/instructor/schedule' },
        { label: 'Practice Qs', icon: '🎯', route: '/instructor/practice-questions' },
        { label: 'Topics', icon: '🏷️', route: '/instructor/topics' },
        { label: 'Reports', icon: '📊', route: '/instructor/reports' },
        { label: 'Analytics', icon: '📉', route: '/instructor/analytics' },
        { label: 'Results Overview', icon: '🏆', route: '/instructor/results-overview' },
        { label: 'View Issues', icon: '⚠️', route: '/instructor/view-issues' },
        { label: 'Notifications', icon: '🔔', route: '/instructor/notifications' },
        { label: 'Search', icon: '🔍', route: '/instructor/search' },
        { label: 'Settings', icon: '⚙️', route: '/instructor/settings' },
        { label: 'Profile', icon: '👤', route: '/instructor/profile' },
    ];
}
