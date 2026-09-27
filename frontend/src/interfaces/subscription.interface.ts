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
