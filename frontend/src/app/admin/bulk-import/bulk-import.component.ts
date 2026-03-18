import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-bulk-import',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './bulk-import.component.html'
})
export class BulkImportComponent {
    role = 'student';
    csvText = '';
    result: any = null;
    error = '';
    loading = false;

    constructor(private api: ApiService) { }

    get csvPlaceholder() {
        if (this.role === 'student')
            return 'username,email,password,first_name,last_name,student_id,department';
        return 'username,email,password,first_name,last_name,employee_id,department';
    }

    import() {
        this.error = ''; this.result = null;
        const lines = this.csvText.trim().split('\n').filter(l => l.trim());
        if (lines.length < 2) { this.error = 'Please provide at least a header row and one data row.'; return; }
        const headers = lines[0].split(',').map(h => h.trim());
        const users = lines.slice(1).map(line => {
            const vals = line.split(',').map(v => v.trim());
            const obj: any = { role: this.role };
            headers.forEach((h, i) => obj[h] = vals[i] ?? '');
            return obj;
        });
        this.loading = true;
        this.api.bulkImport(users).subscribe({
            next: r => { this.result = r; this.loading = false; },
            error: err => { this.error = JSON.stringify(err.error); this.loading = false; }
        });
    }
}
