import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ActivatedRoute, RouterModule } from '@angular/router';
import { ApiService } from '../../core/services/api.service';
import { forkJoin } from 'rxjs';

@Component({
    selector: 'app-view-course',
    standalone: true,
    imports: [CommonModule, RouterModule],
    templateUrl: './view-course.component.html'
})
export class ViewCourseComponent implements OnInit {
    course: any = null;
    instructors: any[] = [];
    students: any[] = [];
    loading = true;

    constructor(private route: ActivatedRoute, private api: ApiService) { }

    ngOnInit() {
        const id = Number(this.route.snapshot.paramMap.get('id'));
        forkJoin({
            course: this.api.getCourse(id),
            instructors: this.api.getUsers('instructor'),
            enrollments: this.api.getEnrollments({ course_id: id }),
        }).subscribe({
            next: d => {
                this.course = d.course;
                this.instructors = d.instructors.filter((i: any) =>
                    d.enrollments.some((e: any) => e.instructor_id === i.id || e.user_id === i.id));
                this.students = d.enrollments.filter((e: any) => e.student_id || e.role === 'student');
                this.loading = false;
            },
            error: () => { this.loading = false; }
        });
    }
}
