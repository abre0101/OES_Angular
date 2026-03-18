import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ActivatedRoute } from '@angular/router';
import { ApiService } from '../../core/services/api.service';

@Component({
    selector: 'app-users',
    standalone: true,
    imports: [CommonModule, FormsModule],
    templateUrl: './users.component.html'
})
export class UsersComponent implements OnInit {
    users: any[] = [];
    role = '';
    loading = true;
    showForm = false;
    editingUser: any = null;
    form: any = {};
    error = '';
    success = '';

    constructor(private api: ApiService, private route: ActivatedRoute) { }

    ngOnInit() {
        this.role = this.route.snapshot.data['role'];
        this.load();
    }

    load() {
        this.loading = true;
        this.api.getUsers(this.role).subscribe({
            next: data => { this.users = data; this.loading = false; },
            error: () => { this.loading = false; }
        });
    }

    openCreate() {
        this.editingUser = null;
        this.form = { role: this.role };
        this.showForm = true;
        this.error = '';
    }

    openEdit(user: any) {
        this.editingUser = user;
        this.form = { ...user };
        this.showForm = true;
        this.error = '';
    }

    save() {
        const obs = this.editingUser
            ? this.api.updateUser(this.editingUser.id, this.form)
            : this.api.createUser(this.form);
        obs.subscribe({
            next: () => { this.showForm = false; this.success = 'Saved successfully'; this.load(); },
            error: err => { this.error = JSON.stringify(err.error); }
        });
    }

    delete(id: number) {
        if (!confirm('Delete this user?')) return;
        this.api.deleteUser(id).subscribe(() => this.load());
    }

    get roleLabel() {
        return this.role.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase());
    }
}
