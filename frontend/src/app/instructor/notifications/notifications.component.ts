import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-instructor-notifications',
    standalone: true,
    imports: [CommonModule],
    templateUrl: './notifications.component.html'
})
export class InstructorNotificationsComponent implements OnInit {
    notifications: any[] = [];
    loading = true;

    constructor(private api: ApiService) { }

    ngOnInit() {
        this.api.getNotifications().subscribe({
            next: d => { this.notifications = d; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    markRead(n: any) {
        if (n.is_read) return;
        this.api.markNotificationRead(n.id).subscribe(() => n.is_read = true);
    }
}
