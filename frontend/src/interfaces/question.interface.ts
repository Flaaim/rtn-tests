export interface BaseQuestionData {
  id: string;
  text: string;
  form: string;
  test: PublicTestData;
  category: PublicCategoryData;
  product: ProductDTO | null;
}

export interface ProtectedQuestionData extends BaseQuestionData {
  course: {
    courseId: string;
    isProtected: true;
  };
  answers: BaseAnswerData[];
}

export interface OpenQuestionData extends BaseQuestionData {
  course: {
    courseId: string;
    isProtected: false;
  };
  answers: OpenAnswerData[];
}

export type PublicQuestionData = ProtectedQuestionData | OpenQuestionData;

export function isOpenQuestion(question: PublicQuestionData): question is OpenQuestionData {
  return !question.course.isProtected;
}

export interface BaseAnswerData {
  id: string;
  text: string;
}

export interface OpenAnswerData extends BaseAnswerData {
  isCorrect: boolean;
}

export interface PublicTestData {
  id: string;
  name: string;
  description: string;
  slug: string;
  category: CategoryDTO;
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

export interface ProductDTO {
  productId: string;
  amount: string;
  addedAt: string;
}

interface CategoryDTO {
  name: string;
  slug: string;
  parent: CategoryDTO | null;
}
