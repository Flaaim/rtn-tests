export interface AdminUsersStats {
  totalUsers: number;
  registrationsToday: number;
  registrationsThisWeek: number;
  registrationsLast30Days: number;
}

export interface AdminSubscriptionsStats {
  trialSubscriptions: number;
  activeSubscriptions: number;
  expiredSubscriptions: number;
  waitSubscriptions: number;
  conversionRate: number;
}
