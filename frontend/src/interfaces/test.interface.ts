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
