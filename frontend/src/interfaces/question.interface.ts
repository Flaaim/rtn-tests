export interface PublicQuestionData {
  id: string;
  text: string;
  answers: PublicAnswerData[];
  form: string;
  test: PublicTestData;
  category: PublicCategoryData;
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
}
export interface PublicAnswerData {
  id: string;
  text: string;
  isCorrect: boolean;
}
