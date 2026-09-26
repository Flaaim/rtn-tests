export type PaidSubscriptionPlan = "basic";

export interface CreatePaymentPayload {
  plan: PaidSubscriptionPlan;
  amount: string;
  durationDays: number;
}

export interface CreatePaymentResponse {
  paymentId: string;
  confirmationUrl: string;
}
