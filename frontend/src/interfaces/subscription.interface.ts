export interface SubscriptionFull {
  hasAccess: boolean;
  plan: string;
  status: string;
  periodStart: string;
  periodEnd: string;
  trialUser: boolean;
}
