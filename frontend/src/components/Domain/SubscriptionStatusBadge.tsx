import { Badge } from "@/components/ui/badge";

const SUBSCRIPTION_STATUS: Record<string, { label: string; className: string }> = {
  active: {
    label: "Активный",
    className: "bg-green-500",
  },
  expired: {
    label: "Срок истек",
    className: "bg-gray-500",
  },
  cancelled: {
    label: "Отменен",
    className: "destructive",
  },
};

interface SubscriptionStatusBadgeProps {
  status: string;
}

export default function SubscriptionStatusBadge({ status }: SubscriptionStatusBadgeProps) {
  const config = SUBSCRIPTION_STATUS[status];

  if (config) {
    return <Badge className={config.className}>{config.label}</Badge>;
  }

  return <Badge variant="outline">{status}</Badge>;
}
