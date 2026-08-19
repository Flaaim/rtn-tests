import { CourseSelectOption, Question } from "@/interfaces/course.interface";

export interface PaginatedTests {
  items: TestItem[];
  totalCount: number;
  totalPages: number;
}

export interface TestItem {
  testId: string;
  name: string;
  cipher: string;
  status: string;
  createdAt: string;
}

export interface AddTestPayload {
  name: string;
  cipher: string;
  description: string;
  numberOfTickets: number;
  numberQuestionsInTicket: number;
  allowedMistakes: number;
  courseIds: string[];
}

export interface ChangeCipherTestPayload {
  id: string;
  cipher: string;
}

export interface RenameTestPayload {
  id: string;
  name: string;
  description: string;
}

export interface TestFull {
  id: string;
  name: string;
  cipher: string;
  description: string;
  allowedMistakes: number;
  courses: CourseSelectOption[];
  tickets: Ticket[];
  slug: string;
  createdAt: string;
  status: string;
  numberOfTickets: number;
  numberQuestionsInTicket: number;
}

export interface Ticket {
  number: number;
  questions: Question[];
}
