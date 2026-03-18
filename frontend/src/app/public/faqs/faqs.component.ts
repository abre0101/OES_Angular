import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
    selector: 'app-faqs',
    standalone: true,
    imports: [CommonModule],
    templateUrl: './faqs.component.html',
    styleUrls: ['./faqs.component.css']
})
export class FaqsComponent {
    activeIndex: number | null = null;

    faqs = [
        { q: 'How do I login to the system?', a: 'Navigate to the home page and click on either "Student Login" or "Staffs Login" depending on your role. Enter your credentials (username and password) and click the login button to access your dashboard.' },
        { q: 'What should I do if I forget my password?', a: 'Click on the "Forgot Password?" link on the login page. You\'ll need to provide your registered email or student ID. Follow the instructions to reset your password. If you encounter issues, contact the IT support team.' },
        { q: 'What are the system requirements?', a: 'You need: a modern web browser (Chrome, Firefox, Safari, or Edge — latest version), stable internet connection (minimum 2 Mbps), JavaScript and cookies enabled, and screen resolution of at least 1024×768 pixels.' },
        { q: 'How do I take an exam?', a: 'Login to your student account → navigate to "Available Exams" → select the exam → read all instructions → click "Start Exam" → answer questions within the time limit → review answers → click "Submit Exam".' },
        { q: 'What happens if my internet disconnects during an exam?', a: 'The system automatically saves your progress periodically. If your connection drops, simply log back in and continue from where you left off. Note that the exam timer continues running, so reconnect as quickly as possible.' },
        { q: 'How can I view my exam results?', a: 'Login to your student dashboard and navigate to the "Results" section. Results are typically available immediately after submission for objective exams.' },
        { q: 'Can I use my mobile phone to take exams?', a: 'Yes, the system is fully responsive and works on mobile devices. However, we strongly recommend using a computer or tablet for a better experience.' },
        { q: 'How do I report a technical issue?', a: 'Contact our technical support team immediately via email at debremarkos@dmu.edu.et or call +251-900469816. Provide details about the issue, including screenshots if possible, your student ID, and the time the issue occurred.' },
    ];

    toggle(i: number) { this.activeIndex = this.activeIndex === i ? null : i; }
}
