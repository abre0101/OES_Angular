import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

@Component({
    selector: 'app-public-layout',
    standalone: true,
    imports: [CommonModule, RouterModule],
    templateUrl: './public-layout.component.html',
    styleUrls: ['./public-layout.component.css']
})
export class PublicLayoutComponent {
    constructor(public auth: AuthService) { }

    get dashboardRoute(): string {
        const role = this.auth.role;
        const map: Record<string, string> = {
            admin: '/admin', instructor: '/instructor',
            department_head: '/department-head', student: '/student'
        };
        return map[role] ?? '/login';
    }
}
