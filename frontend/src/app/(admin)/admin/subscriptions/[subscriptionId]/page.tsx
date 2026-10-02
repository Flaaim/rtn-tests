import { fetchSubscriptionAction } from "@/actions/subscriptions";
import { SubscriptionAdminDTO } from "@/interfaces/subscription.interface";
import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";

interface SubscriptionOverviewPageProps {
  params: Promise<{ subscriptionId: string }>;
}
export default async function SubscriptionOverviewPage({ params }: SubscriptionOverviewPageProps) {
  const { subscriptionId } = await params;

  const result = await fetchSubscriptionAction(subscriptionId);

  if (!result.ok || !result.data) {
    return null;
  }

  const subscription: SubscriptionAdminDTO = result.data;

  const items = [{ title: "Подписки", href: "/admin/subscriptions" }, { title: subscription.id }];

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={items} />
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Основная информация</h1>
      </div>
    </div>
  );
}
