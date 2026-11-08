import { ProductDTO } from "@/interfaces/course.interface";

export interface PublicQuestionData {
  id: string;
  text: string;
  answers: PublicAnswerData[];
  form: string;
  test: PublicTestData;
  category: PublicCategoryData;
  product: ProductDTO | null;
}
interface PublicTestData {
  id: string;
  name: string;
  description: string;
  slug: string;
}
interface PublicCategoryData {
  name: string;
  slug: string;
  parent: PublicCategoryData;
}
export interface PublicAnswerData {
  id: string;
  text: string;
  isCorrect: boolean;
}
