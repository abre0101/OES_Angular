import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
    selector: 'app-database-backup',
    standalone: true,
    imports: [CommonModule],
    templateUrl: './database-backup.component.html'
})
export class DatabaseBackupComponent implements OnInit {
    backups: any[] = [];
    message = '';
    messageType = '';
    creating = false;

    ngOnInit() {
        // In a real system this would call an API endpoint
        this.backups = [
            { name: 'oes_backup_2026-03-18_02-30-00.sql', size: '2.4 MB', date: new Date() }
        ];
    }

    createBackup() {
        this.creating = true;
        setTimeout(() => {
            const name = `oes_backup_${new Date().toISOString().replace(/[:.]/g, '-').slice(0, 19)}.sql`;
            this.backups.unshift({ name, size: '2.4 MB', date: new Date() });
            this.message = `Backup created: ${name}`;
            this.messageType = 'success';
            this.creating = false;
        }, 1200);
    }

    deleteBackup(i: number) {
        if (!confirm('Delete this backup?')) return;
        this.backups.splice(i, 1);
        this.message = 'Backup deleted.';
        this.messageType = 'success';
    }
}
