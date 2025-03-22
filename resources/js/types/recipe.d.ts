import { User } from './user'

export interface Recipe {
  id: number
  title: string
  description: string
  instructions: string
  prep_time: number
  cooking_time: number
  servings: number
  url: string | null
  author: string | null
  images: string[] | null
  slug: string
  status: string
  user: User
  categories: Array<{
    id: number
    name: string
  }>
  ingredients: Array<{
    id: number
    name: string
    pivot: {
      amount: number
      unit: string
    }
  }>
}
