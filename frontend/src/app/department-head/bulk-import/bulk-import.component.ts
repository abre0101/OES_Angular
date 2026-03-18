import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-dh-bulk-import',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './bulk-import.component.html'
})
export class DhBulkImportComponent {
    csvText = '';
    preview: any[] = [];
    importing = false;
    message = '';
    messageType = '';
    error = '';

    constructor(private api: ApiService) { }

    parseCSV() {
        this.error = '';
        const lines = this.csvText.trim().split('\n').filter(l => l.trim());
        if (lines.length < 2) { this.error = 'CSV must have a header row and at least one data row.'; return; }
        const headers = lines[0].split(',').map(h => h.trim().toLowerCase());
        this.preview = lines.slice(1).map(line => {
            const vals = line.split(',').map(v => v.trim());
            const obj: any = {};
            headers.forEach((h, i) => obj[h] = vals[i] ?? '');
            obj.role = obj.role || 'student';
            return obj;
        });
    }

    import() {
        if (!this.preview.length) return;
        this.importing = true; this.message = '';
        this.api.bulkImport(this.preview).subscribe({
            next: (r: any) => {
                this.message = `Imported ${r.created ?? this.preview.length} students successfully.`;
                this.messageType = 'success';
                this.preview = []; this.csvText = '';
                this.importing = false;
            },
            error: () => { this.message = 'Import failed. Check your data.'; this.messageType = 'error'; this.importing = false; }
        });
    }
}
