import { Routes } from '@angular/router';
import { StudentShellComponent } from './student-shell.component';

export const studentRoutes: Routes = [
    {
        path: '',
        component: StudentShellComponent,
        children: [
            { path: '', redirectTo: 'dashboard', pathMatch: 'full' },
            { path: 'dashboard', loadComponent: () => import('./dashboard/dashboard.component').then(m => m.StudentDashboardComponent) },
            { path: 'exams', loadComponent: () => import('./exams/exams.component').then(m => m.StudentExamsComponent) },
            { path: 'exams/:id/take', loadComponent: () => import('./take-exam/take-exam.component').then(m => m.TakeExamComponent) },
            { path: 'exams/:id/instructions', loadComponent: () => import('./exam-instructions/exam-instructions.component').then(m => m.ExamInstructionsComponent) },
            { path: 'results', loadComponent: () => import('./results/results.component').then(m => m.StudentResultsComponent) },
            { path: 'results/:id', loadComponent: () => import('./result-detail/result-detail.component').then(m => m.ResultDetailComponent) },
            { path: 'results/:id/review', loadComponent: () => import('./review-answers/review-answers.component').then(m => m.ReviewAnswersComponent) },
            { path: 'practice', loadComponent: () => import('./practice/practice.component').then(m => m.PracticeComponent) },
            { path: 'schedule', loadComponent: () => import('./schedule/schedule.component').then(m => m.StudentScheduleComponent) },
            { path: 'notifications', loadComponent: () => import('./notifications/notifications.component').then(m => m.NotificationsComponent) },
            { path: 'issues', loadComponent: () => import('./issues/issues.component').then(m => m.StudentIssuesComponent) },
            { path: 'profile', loadComponent: () => import('../shared/profile/profile.component').then(m => m.ProfileComponent) },
        ]
    }
];
