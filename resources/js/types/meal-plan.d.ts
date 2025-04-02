import { Recipe } from './recipe';
import { User } from './user';

export interface MealPlan {
    id: number;
    name: string | null;
    start_date: string;
    end_date: string;
    duration: number;
    people_count: number;
    created_at: string;
    updated_at: string;
    user_id: number;
    user?: User;
    recipes?: Array<
        Recipe & {
            pivot: {
                scale_factor: number;
                servings_available: number;
            };
        }
    >;
}
