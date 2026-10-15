export interface AttemptInterface {
  id: string;
  status: string;
  ticketNumber: number;
  questions: Question[];
}

export interface LaunchAttemptPayload {
  testId: string;
  ticketNumber: number;
}

export interface Question {
  id: string;
  text: string;
  questionImg: string;
  answers: Answer[];
  form: string;
}

interface Answer {
  id: string;
  text: string;
  answerImg: string;
}

export interface SubmitAnswerPayload {
  id: string;
  questionId: string;
  selectedAnswersIds: string[];
}

export interface UserResultDTO {
  selectedIds: string[];
  isCorrect: boolean;
}
export interface AnswerItemDTO {
  id: string;
  text: string;
  isCorrect: boolean;
  answerImg: string;
}

export interface MatchingAnswersDTO {
  left: AnswerItemDTO[];
  right: AnswerItemDTO[];
}

export interface ResultQuestionDTO {
  id: string;
  text: string;
  questionImg: string | null;
  answers: AnswerItemDTO[] | MatchingAnswersDTO;
  form: "single_choice" | "multiple_choice" | "sequence" | "matching";
  userResult: UserResultDTO | null;
}
export interface TestInfoDTO {
  id: string;
  name: string;
  cipher: string;
  allowedMistakes: number;
  slug: string;
}

export interface CategoryInfoDTO {
  name: string;
  slug: string;
  parent: CategoryInfoDTO;
}

export interface AttemptResultData {
  id: string;
  status: string;
  score: number;
  mistakes: number;
  ticketNumber: number;
  startedAt: string;
  finishedAt: string | null;
  test: TestInfoDTO;
  questions: ResultQuestionDTO[];
  email: string;
  category: CategoryInfoDTO;
}
