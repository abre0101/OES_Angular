import { Injectable, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { tap } from 'rxjs/operators';
import { environment } from '../../../environments/environment';
import { AuthResponse, User } from '../models/user.model';

@Injectable({ providedIn: 'root' })
export class AuthService {
    private readonly API = environment.apiUrl;
    currentUser = signal<User | null>(this.loadUser());

    constructor(private http: HttpClient, private router: Router) { }

    login(username: string, password: string) {
        return this.http.post<AuthResponse>(`${this.API}/auth/login/`, { username, password }).pipe(
            tap(res => {
                localStorage.setItem('access_token', res.access);
                localStorage.setItem('refresh_token', res.refresh);
                localStorage.setItem('user', JSON.stringify(res.user));
                this.currentUser.set(res.user);
            })
        );
    }

    logout() {
        localStorage.clear();
        this.currentUser.set(null);
        this.router.navigate(['/login']);
    }

    getToken(): string | null {
        return localStorage.getItem('access_token');
    }

    isLoggedIn(): boolean {
        return !!this.getToken();
    }

    get role(): string {
        return this.currentUser()?.role ?? '';
    }

    private loadUser(): User | null {
        const u = localStorage.getItem('user');
        return u ? JSON.parse(u) : null;
    }

    refreshToken() {
        const refresh = localStorage.getItem('refresh_token');
        return this.http.post<{ access: string }>(`${this.API}/auth/token/refresh/`, { refresh }).pipe(
            tap(res => localStorage.setItem('access_token', res.access))
        );
    }
}
