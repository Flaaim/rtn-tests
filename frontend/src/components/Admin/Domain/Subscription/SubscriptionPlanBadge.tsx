import { Badge } from "@/components/ui/badge";

const STATUS_CONFIG: Record<string, { label: string; className: string }> = {
  trial: {
    label: "Триал",
    className:
      "bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-50 dark:bg-blue-950/50 dark:text-blue-400 dark:border-blue-900 dark:hover:bg-blue-950/50",
  },
  basic: {
    label: "Базовый",
    className:
      "bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-50 dark:bg-emerald-950/50 dark:text-emerald-400 dark:border-emerald-900 dark:hover:bg-emerald-950/50",
  },
};

interface SubscriptionPlanBadgeProps {
  plan: string;
}

export default function SubscriptionPlanBadge({ plan }: SubscriptionPlanBadgeProps) {
  const config = STATUS_CONFIG[plan];

  if (config) {
    return (
      <Badge variant="outline" className={config.className}>
        {config.label}
      </Badge>
    );
  }

  return (
    <Badge
      variant="outline"
      className="bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-50 dark:bg-slate-900 dark:text-slate-400 dark:border-slate-800"
    >
      {plan}
    </Badge>
  );
}
