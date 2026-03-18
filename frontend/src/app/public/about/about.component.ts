import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
    selector: 'app-about',
    standalone: true,
    imports: [CommonModule],
    templateUrl: './about.component.html',
    styleUrls: ['./about.component.css']
})
export class AboutComponent {
    team = [
        { name: 'Fetsum Taye', initials: 'FT', color: 'linear-gradient(135deg,#667eea,#764ba2)' },
        { name: 'Amanuel Asefa', initials: 'AA', color: 'linear-gradient(135deg,#f093fb,#f5576c)' },
        { name: 'Rediet Ayenekulu', initials: 'RA', color: 'linear-gradient(135deg,#4facfe,#00f2fe)' },
        { name: 'Gizachew Kumie', initials: 'GK', color: 'linear-gradient(135deg,#43e97b,#38f9d7)' },
        { name: 'Hana Abate', initials: 'HA', color: 'linear-gradient(135deg,#fa709a,#fee140)' },
        { name: 'Askal Tariko', initials: 'AT', color: 'linear-gradient(135deg,#30cfd0,#330867)' },
    ];
}
