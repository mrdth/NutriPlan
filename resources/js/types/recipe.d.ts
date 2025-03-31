import { User } from './user';

export interface Recipe {
    id: number;
    title: string;
    description: string;
    instructions: string;
    prep_time: number;
    cooking_time: number;
    servings: number;
    url: string | null;
    author: string | null;
    is_public: boolean;
    images: string[] | null;
    slug: string;
    created_at: string;
    user: User;
    categories: Array<{
        id: number;
        name: string;
        slug: string;
    }>;
    ingredients: Array<{
        id: number;
        name: string;
        pivot: {
            amount: number;
            unit: string;
        };
    }>;
    nutrition_information?: {
        calories?: string;
        carbohydrate_content?: string;
        cholesterol_content?: string;
        fat_content?: string;
        fiber_content?: string;
        protein_content?: string;
        saturated_fat_content?: string;
        serving_size?: string;
        sodium_content?: string;
        sugar_content?: string;
        trans_fat_content?: string;
        unsaturated_fat_content?: string;
    } | null;
}
