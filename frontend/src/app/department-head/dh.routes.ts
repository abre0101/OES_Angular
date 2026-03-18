import { Routes } from '@angular/router';
import { DhShellComponent } from './dh-shell.component';

export const dhRoutes: Routes = [
    {
        path: '',
        component: DhShellComponent,
        children: [
            { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
            { path: 'dashboard', loadComponent: () => import('./dashboard/dashboard.component').then(m => m.DhDashboardComponent) },
            { path: 'approvals', loadComponent: () => import('./approvals/approvals.component').then(m => m.ApprovalsComponent) },
            { path: 'exams', loadComponent: () => import('./exams/exams.component').then(m => m.DhExamsComponent) },
            { path: 'students', loadComponent: () => import('./students/students.component').then(m => m.DhStudentsComponent) },
            { path: 'courses', loadComponent: () => import('./courses/courses.component').then(m => m.DhCoursesComponent) },
            { path: 'assign-instructor', loadComponent: () => import('./assign-instructor/assign-instructor.component').then(m => m.AssignInstructorComponent) },
            { path: 'schedule', loadComponent: () => import('./schedule/schedule.component').then(m => m.DhScheduleComponent) },
            { path: 'results', loadComponent: () => import('./results/results.component').then(m => m.DhResultsComponent) },
            { path: 'reports', loadComponent: () => import('./reports/reports.component').then(m => m.DhReportsComponent) },
            { path: 'monitor-exams', loadComponent: () => import('./monitor-exams/monitor-exams.component').then(m => m.MonitorExamsComponent) },
            { path: 'issues', loadComponent: () => import('./issues/issues.component').then(m => m.DhIssuesComponent) },
            { path: 'bulk-import', loadComponent: () => import('./bulk-import/bulk-import.component').then(m => m.DhBulkImportComponent) },
            { path: 'search', loadComponent: () => import('./global-search/global-search.component').then(m => m.DhGlobalSearchComponent) },
            { path: 'register-student', loadComponent: () => import('./register-student/register-student.component').then(m => m.RegisterStudentComponent) },
            { path: 'register-course', loadComponent: () => import('./register-course/register-course.component').then(m => m.RegisterCourseComponent) },
            { path: 'students/:id', loadComponent: () => import('./view-student/view-student.component').then(m => m.ViewStudentComponent) },
            { path: 'courses/:id', loadComponent: () => import('./view-course/view-course.component').then(m => m.ViewCourseComponent) },
            { path: 'exams/:id', loadComponent: () => import('./view-exam-details/view-exam-details.component').then(m => m.ViewExamDetailsComponent) },
            { path: 'performance-reports', loadComponent: () => import('./performance-reports/performance-reports.component').then(m => m.PerformanceReportsComponent) },
            { path: 'change-password', loadComponent: () => import('./change-password/change-password.component').then(m => m.DhChangePasswordComponent) },
            { path: 'profile', loadComponent: () => import('../shared/profile/profile.component').then(m => m.ProfileComponent) },
        ]
    }
];
