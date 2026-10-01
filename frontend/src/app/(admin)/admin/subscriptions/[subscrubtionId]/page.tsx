import { fetchSubscriptionAction } from "@/actions/subscriptions";

interface SubscriptionOverviewPageProps {
  params: Promise<{ subscriptionId: string }>;
}
export default async function SubscriptionOverviewPage({ params }: SubscriptionOverviewPageProps) {
  const { subscriptionId } = await params;

  const result = fetchSubscriptionAction(subscriptionId);
}
