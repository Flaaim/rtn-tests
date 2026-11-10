import { Badge } from "@/components/ui/badge";

const RECIPIENT_STATUS: Record<string, { label: string; className: string }> = {
  sent: {
    label: "Отправлено",
    className:
      "bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-50 dark:bg-emerald-950/50 dark:text-emerald-400 dark:border-emerald-900 dark:hover:bg-emerald-950/50",
  },
  pending: {
    label: "Обработка",
    className:
      "bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-50 dark:bg-blue-950/50 dark:text-blue-400 dark:border-blue-900 dark:hover:bg-blue-950/50",
  },
  failed: {
    label: "Ошибка",
    className:
      "bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-50 dark:bg-rose-950/50 dark:text-rose-400 dark:border-rose-900 dark:hover:bg-rose-950/50",
  },
};

interface RecipientStatusBadgeProps {
  status: string;
}

export default function RecipientStatusBadge({ status }: RecipientStatusBadgeProps) {
  const config = RECIPIENT_STATUS[status];

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
      {status}
    </Badge>
  );
}
