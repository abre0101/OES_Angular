import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

@Component({
    selector: 'app-admin-settings',
    standalone: true,
    imports: [CommonModule, RouterModule],
    templateUrl: './settings.component.html'
})
export class AdminSettingsComponent {
    toggles: Record<string, boolean> = {
        autoBackup: true,
        emailNotifications: true,
        autoSubmit: true,
        shuffleQuestions: true,
        showResults: false,
        maintenanceMode: false,
    };

    toggle(key: string) { this.toggles[key] = !this.toggles[key]; }
}
