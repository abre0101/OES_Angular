export interface Question {
    id: number;
    course: number;
    topic: string;
    text: string;
    question_type: 'mcq' | 'true_false' | 'short_answer';
    option_a: string;
    option_b: string;
    option_c: string;
    option_d: string;
    correct_answer?: string;
    points: number;
    is_practice: boolean;
}

export type ExamStatus = 'draft' | 'pending' | 'approved' | 'rejected' | 'active' | 'completed';

export interface Exam {
    id: number;
    title: string;
    course: number;
    course_name: string;
    created_by: number;
    created_by_name: string;
    status: ExamStatus;
    duration_minutes: number;
    total_marks: number;
    pass_marks: number;
    instructions: string;
    start_time: string | null;
    end_time: string | null;
    question_count: number;
    created_at: string;
    review_comment: string;
}

export interface ExamResult {
    id: number;
    exam: number;
    exam_title: string;
    student: number;
    student_name: string;
    score: number;
    total_marks: number;
    percentage: number;
    passed: boolean;
    started_at: string;
    submitted_at: string;
}

export interface TechnicalIssue {
    id: number;
    title: string;
    description: string;
    status: 'open' | 'in_progress' | 'resolved';
    reported_by: number;
    reported_by_name: string;
    exam: number | null;
    created_at: string;
}
