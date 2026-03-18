import { Component, OnInit, OnDestroy } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

@Component({
    selector: 'app-home',
    standalone: true,
    imports: [CommonModule, RouterModule],
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit, OnDestroy {
    currentSlide = 0;
    private timer: any;

    slides = [
        { img: 'assets/images/home1.jpg', caption: 'Welcome to Online Examination System', sub: 'A secure, efficient, and modern platform for conducting examinations', btn: 'Get Started', link: '/login' },
        { img: 'assets/images/home2.jpg', caption: 'Excellence in Digital Education', sub: 'Empowering students and educators with cutting-edge technology', btn: 'Learn More', link: '/login' },
    ];

    ngOnInit() {
        this.timer = setInterval(() => this.nextSlide(), 5000);
    }

    ngOnDestroy() { clearInterval(this.timer); }

    nextSlide() { this.currentSlide = (this.currentSlide + 1) % this.slides.length; }
    prevSlide() { this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length; }
    goTo(i: number) { this.currentSlide = i; }
}
