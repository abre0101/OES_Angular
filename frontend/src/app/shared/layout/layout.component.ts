import { Component, Input, signal } from '@angular/core';
import { CommonModule, TitleCasePipe } from '@angular/common';
import { RouterModule } from '@angular/router';
import { AuthService } from '../../core/services/auth.service';

export interface NavItem {
  label: string;
  icon: string;
  route: string;
}

@Component({
  selector: 'app-layout',
  standalone: true,
  imports: [CommonModule, RouterModule, TitleCasePipe],
  templateUrl: './layout.component.html',
  styleUrls: ['./layout.component.css']
})
export class LayoutComponent {
  @Input() navItems: NavItem[] = [];
  @Input() title = 'OES';
  @Input() subtitle = 'DMU';
  @Input() pageTitle = 'Dashboard';

  minimized = signal(localStorage.getItem('sidebarMinimized') === 'true');
  profileOpen = signal(false);

  constructor(public auth: AuthService) { }

  toggleMinimize() {
    this.minimized.update(v => !v);
    localStorage.setItem('sidebarMinimized', String(this.minimized()));
  }

  toggleProfile() { this.profileOpen.update(v => !v); }

  logout() { this.auth.logout(); }

  get avatarLetter(): string {
    return (this.auth.currentUser()?.username ?? 'U')[0].toUpperCase();
  }
}
