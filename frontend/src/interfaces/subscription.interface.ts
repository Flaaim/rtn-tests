export type SubscriptionStatus = "active" | "expired" | "cancelled";
export type SubscriptionType = "basic" | "trial";

export interface SubscriptionListDTO {
  items: SubscriptionDTO[];
  totalPages: number;
  totalCount: number;
}

export interface SubscriptionDTO {
  id: string;
  plan: string;
  durationDays: number;
  periodStart: string;
  periodEnd: string;
}

export interface SubscriptionAdminDTO {
  id: string;
  plan: SubscriptionType;
  status: SubscriptionStatus;
  durationDays: number;
  periodStart: string;
  periodEnd: string;
  email: string;
}
