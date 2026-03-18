import { Routes } from '@angular/router';
import { AdminShellComponent } from './admin-shell.component';

export const adminRoutes: Routes = [
    {
        path: '',
        component: AdminShellComponent,
        children: [
            { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
            { path: 'dashboard', loadComponent: () => import('./dashboard/dashboard.component').then(m => m.AdminDashboardComponent) },
            { path: 'students', loadComponent: () => import('./users/users.component').then(m => m.UsersComponent), data: { role: 'student', label: 'Students' } },
            { path: 'instructors', loadComponent: () => import('./users/users.component').then(m => m.UsersComponent), data: { role: 'instructor', label: 'Instructors' } },
            { path: 'department-heads', loadComponent: () => import('./users/users.component').then(m => m.UsersComponent), data: { role: 'department_head', label: 'Department Heads' } },
            { path: 'courses', loadComponent: () => import('./courses/courses.component').then(m => m.CoursesComponent) },
            { path: 'departments', loadComponent: () => import('./departments/departments.component').then(m => m.DepartmentsComponent) },
            { path: 'faculties', loadComponent: () => import('./faculties/faculties.component').then(m => m.FacultiesComponent) },
            { path: 'bulk-import', loadComponent: () => import('./bulk-import/bulk-import.component').then(m => m.BulkImportComponent) },
            { path: 'issues', loadComponent: () => import('./issues/issues.component').then(m => m.IssuesComponent) },
            { path: 'reports', loadComponent: () => import('./reports/reports.component').then(m => m.AdminReportsComponent) },
            { path: 'security-logs', loadComponent: () => import('./security-logs/security-logs.component').then(m => m.SecurityLogsComponent) },
            { path: 'settings', loadComponent: () => import('./settings/settings.component').then(m => m.AdminSettingsComponent) },
            { path: 'database-backup', loadComponent: () => import('./database-backup/database-backup.component').then(m => m.DatabaseBackupComponent) },
            { path: 'search', loadComponent: () => import('./global-search/global-search.component').then(m => m.GlobalSearchComponent) },
            { path: 'profile', loadComponent: () => import('../shared/profile/profile.component').then(m => m.ProfileComponent) },
        ]
    }
];
