import { Component } from '@angular/core';
import { RouterModule } from '@angular/router';

@Component({
    selector: 'app-unauthorized',
    standalone: true,
    imports: [RouterModule],
    template: `
    <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;gap:1rem;">
      <div style="font-size:4rem;">🚫</div>
      <h1 style="font-size:1.5rem;font-weight:800;">Access Denied</h1>
      <p style="color:#6b7280;">You don't have permission to view this page.</p>
      <a routerLink="/login" class="btn btn-primary">Back to Login</a>
    </div>
  `
})
export class UnauthorizedComponent { }
