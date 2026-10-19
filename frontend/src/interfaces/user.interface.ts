import { NetworkItem } from "@/interfaces/auth.interface";

export interface UserAttemptDTO {
  id: string;
  status: string;
  score: number;
  mistakes: number;
  ticketNumber: number;
  startedAt: string;
  finishedAt: string | null;
  name: string;
  cipher: string;
  allowedMistakes: number;
}

export interface ListAttemptsDTO {
  items: UserAttemptDTO[];
  totalCount: number;
  totalPages: number;
}

export interface UserAttemptStatsDTO {
  completedTests: number;
  inProgressTests: number;
  averageScore: number;
}

export interface ProfileDTO {
  id: string;
  email: string;
  status: string;
  role: string;
  date: string;
  networks: NetworkItem[];
}
export interface AddProfilePayload {
  email: string;
  role: string;
  name: string;
  surname: string;
}
export interface PaginatedProfiles {
  items: ProfileDTO[];
  totalCount: number;
  totalPages: number;
}

export interface ProfileFull {
  id: string;
  email: string;
  profileStatus: string;
  role: string;
  date: string;
  authStatus: string;
  networks: NetworkItem[];
  passwordHash?: string | null;
  name?: string | null;
  surname?: string | null;
}

export interface UserSubscription {
  hasAccess: boolean;
  plan: string;
  status: string;
  periodStart: string;
  periodEnd: string;
}
