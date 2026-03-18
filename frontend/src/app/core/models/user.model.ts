export type UserRole = 'admin' | 'instructor' | 'department_head' | 'student';

export interface User {
    id: number;
    username: string;
    email: string;
    first_name: string;
    last_name: string;
    full_name: string;
    role: UserRole;
    department: string;
    faculty: string;
    phone: string;
    profile_image: string | null;
    student_id: string;
    year_of_study: number | null;
    employee_id: string;
    is_active: boolean;
    date_joined: string;
}

export interface AuthResponse {
    access: string;
    refresh: string;
    user: User;
}
