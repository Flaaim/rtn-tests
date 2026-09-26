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
export interface TestItemPublic {
  id: string;
  name: string;
  description: string;
  slug: string;
  cipher: string;
  status: string;
  createdAt: string;
}
export interface AddTestPayload {
  categoryId: string;
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

export interface UpdateSettingsTestPayload {
  id: string;
  numberOfTickets: number;
  numberQuestionsInTicket: number;
  allowedMistakes: number;
}
export interface UpdateTestPayload {
  id: string;
  courseIds: string[];
}
export interface TestFull {
  id: string;
  name: string;
  cipher: string;
  description: string;
  courses: CourseSelectOption[];
  tickets: Ticket[];
  slug: string;
  createdAt: string;
  status: string;
  settings: Settings;
  category: { id: string; name: string };
}

export interface Ticket {
  number: number;
  questions: Question[];
}

export interface Settings {
  numberOfTickets: number;
  numberQuestionsInTicket: number;
  allowedMistakes: number;
}

export interface TestPublicDTO {
  id: string;
  name: string;
  cipher: string;
  description: string;
  status: string;
  createdAt: string;
  slug: string;
  tickets: TicketPublic[];
  settings: Settings;
  category: CategoryPublic;
}

interface TicketPublic {
  number: number;
}

interface CategoryPublic {
  name: string;
  slug: string;
}

export interface ChangeCategoryTestPayload {
  id: string;
  categoryId: string;
}
