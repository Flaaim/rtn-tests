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

export interface SubscriptionAdminListDTO {
  items: SubscriptionAdminDTO[];
  totalPages: number;
  totalCount: number;
}

export interface SubscriptionAdminDTO extends Omit<SubscriptionDTO, "plan"> {
  plan: SubscriptionType;
  status: SubscriptionStatus;
  email: string;
}
