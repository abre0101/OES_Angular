import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/services/api.service';
import { forkJoin } from 'rxjs';

@Component({
    selector: 'app-assign-instructor',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './assign-instructor.component.html'
})
export class AssignInstructorComponent implements OnInit {
    instructors: any[] = [];
    courses: any[] = [];
    assignments: any[] = [];
    loading = true;
    form = { instructor_id: '', instructor_name: '', course_id: '', course_name: '', academic_year: '', semester: '' };
    success = '';
    error = '';

    constructor(private api: ApiService) { }

    ngOnInit() {
        forkJoin({ instructors: this.api.getUsers('instructor'), courses: this.api.getCourses(), assignments: this.api.getEnrollments() }).subscribe({
            next: d => { this.instructors = d.instructors; this.courses = d.courses; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    assign() {
        const instructor = this.instructors.find(i => i.id === this.form.instructor_id);
        const course = this.courses.find(c => c.id === this.form.course_id);
        if (!instructor || !course) { this.error = 'Select both instructor and course.'; return; }
        this.form.instructor_name = instructor.full_name;
        this.form.course_name = course.name;
        this.api.enroll({ ...this.form, student_id: this.form.instructor_id, student_name: this.form.instructor_name }).subscribe({
            next: () => { this.success = 'Instructor assigned successfully.'; this.form = { instructor_id: '', instructor_name: '', course_id: '', course_name: '', academic_year: '', semester: '' }; },
            error: err => { this.error = JSON.stringify(err.error); }
        });
    }
}
