import { Routes } from '@angular/router';
import { InstructorShellComponent } from './instructor-shell.component';

export const instructorRoutes: Routes = [
    {
        path: '',
        component: InstructorShellComponent,
        children: [
            { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
            { path: 'dashboard', loadComponent: () => import('./dashboard/dashboard.component').then(m => m.InstructorDashboardComponent) },
            { path: 'courses', loadComponent: () => import('./courses/courses.component').then(m => m.InstructorCoursesComponent) },
            { path: 'exams', loadComponent: () => import('./exams/exams.component').then(m => m.InstructorExamsComponent) },
            { path: 'exams/:id/questions', loadComponent: () => import('./exam-questions/exam-questions.component').then(m => m.ExamQuestionsComponent) },
            { path: 'questions', loadComponent: () => import('./questions/questions.component').then(m => m.QuestionsComponent) },
            { path: 'students', loadComponent: () => import('./students/students.component').then(m => m.InstructorStudentsComponent) },
            { path: 'results', loadComponent: () => import('./results/results.component').then(m => m.InstructorResultsComponent) },
            { path: 'reports', loadComponent: () => import('./reports/reports.component').then(m => m.InstructorReportsComponent) },
            { path: 'notifications', loadComponent: () => import('./notifications/notifications.component').then(m => m.InstructorNotificationsComponent) },
            { path: 'schedule', loadComponent: () => import('./schedule/schedule.component').then(m => m.InstructorScheduleComponent) },
            { path: 'practice-questions', loadComponent: () => import('./practice-questions/practice-questions.component').then(m => m.InstructorPracticeQuestionsComponent) },
            { path: 'topics', loadComponent: () => import('./topics/topics.component').then(m => m.InstructorTopicsComponent) },
            { path: 'search', loadComponent: () => import('./global-search/global-search.component').then(m => m.InstructorGlobalSearchComponent) },
            { path: 'analytics', loadComponent: () => import('./analytics/analytics.component').then(m => m.InstructorAnalyticsComponent) },
            { path: 'settings', loadComponent: () => import('./settings/settings.component').then(m => m.InstructorSettingsComponent) },
            { path: 'view-issues', loadComponent: () => import('./view-issues/view-issues.component').then(m => m.InstructorViewIssuesComponent) },
            { path: 'results-overview', loadComponent: () => import('./results-overview/results-overview.component').then(m => m.ResultsOverviewComponent) },
            { path: 'profile', loadComponent: () => import('../shared/profile/profile.component').then(m => m.ProfileComponent) },
        ]
    }
];
