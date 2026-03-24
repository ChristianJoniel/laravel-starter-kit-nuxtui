import type { User } from '@/types/auth';

export type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
};

export type UserListItem = Pick<
    User,
    'id' | 'name' | 'email' | 'email_verified_at'
>;
