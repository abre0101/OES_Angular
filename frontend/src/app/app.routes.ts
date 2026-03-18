import { Routes } from '@angular/router';
import { authGuard, roleGuard } from './core/guards/auth.guard';

export const routes: Routes = [
    { path: '', loadComponent: () => import('./home/home.component').then(m => m.HomeComponent) },
    { path: 'login', loadComponent: () => import('./auth/login/login.component').then(m => m.LoginComponent) },
    { path: 'forgot-password', loadComponent: () => import('./auth/forgot-password/forgot-password.component').then(m => m.ForgotPasswordComponent) },
    { path: 'about', loadComponent: () => import('./public/about/about.component').then(m => m.AboutComponent) },
    { path: 'help', loadComponent: () => import('./public/help/help.component').then(m => m.HelpComponent) },
    { path: 'faqs', loadComponent: () => import('./public/faqs/faqs.component').then(m => m.FaqsComponent) },
    { path: 'contact', loadComponent: () => import('./public/contact/contact.component').then(m => m.ContactComponent) },
    { path: 'exam-tips', loadComponent: () => import('./public/exam-tips/exam-tips.component').then(m => m.ExamTipsComponent) },
    {
        path: 'admin',
        canActivate: [authGuard, roleGuard(['admin'])],
        loadChildren: () => import('./admin/admin.routes').then(m => m.adminRoutes)
    },
    {
        path: 'instructor',
        canActivate: [authGuard, roleGuard(['instructor'])],
        loadChildren: () => import('./instructor/instructor.routes').then(m => m.instructorRoutes)
    },
    {
        path: 'department-head',
        canActivate: [authGuard, roleGuard(['department_head'])],
        loadChildren: () => import('./department-head/dh.routes').then(m => m.dhRoutes)
    },
    {
        path: 'student',
        canActivate: [authGuard, roleGuard(['student'])],
        loadChildren: () => import('./student/student.routes').then(m => m.studentRoutes)
    },
    { path: 'unauthorized', loadComponent: () => import('./shared/unauthorized/unauthorized.component').then(m => m.UnauthorizedComponent) },
    { path: '**', redirectTo: '/' }
];
