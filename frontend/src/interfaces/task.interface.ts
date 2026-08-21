export interface TaskShort {
  id: string;
  parserId: string;
  status: string;
  created: string;
  failedReason?: string;
}

export interface TaskFull {
  id: string;
  status: string;
  created: string;
  draft?: string;
  failed_reason?: string;
}

interface Answer {
  id: string;
  text: string;
  isCorrect: boolean;
  answerImg: string;
}

export interface Question {
  id: string;
  number: number;
  text: string;
  questionImg: string;
  answers: Answer[];
  form: string;
}
