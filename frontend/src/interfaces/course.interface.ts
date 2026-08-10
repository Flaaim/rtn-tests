export interface AddCoursePayload {
  name: string;
  cipher: string;
  draft: string;
}

export interface RenameCoursePayload {
  id: string;
  name: string;
  cipher: string;
}
export interface UpdateQuestionCoursePayload {
  id: string;
  rawJson: string;
}
export interface PaginatedCourses {
  items: CourseItem[];
  totalCount: number;
  totalPages: number;
}

export interface CourseItem {
  courseId: string;
  status: string;
  name: string;
  cipher: string;
  createdAt: string;
}

export interface CourseFull {
  courseId: string;
  status: string;
  name: string;
  cipher: string;
  createdAt: string;
  questions: Question[];
}

export interface Question {
  id: string;
  number: number;
  text: string;
  questionImg: string;
  answers: Answer[];
  form: string;
}

interface Answer {
  id: string;
  text: string;
  isCorrect: boolean;
  answerImg: string;
}
