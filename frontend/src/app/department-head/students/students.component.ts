import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-dh-students',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './students.component.html'
})
export class DhStudentsComponent implements OnInit {
    students: any[] = [];
    loading = true;
    search = '';

    constructor(private api: ApiService) { }
    ngOnInit() {
        this.api.getUsers('student').subscribe({ next: d => { this.students = d; this.loading = false; }, error: () => { this.loading = false; } });
    }

    get filtered() {
        if (!this.search) return this.students;
        const q = this.search.toLowerCase();
        return this.students.filter(s => s.full_name?.toLowerCase().includes(q) || s.username?.toLowerCase().includes(q) || s.student_id?.toLowerCase().includes(q));
    }
}
