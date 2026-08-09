import { Badge } from "@/components/ui/badge";

const STATUS_CONFIG: Record<string, { label: string; className: string }> = {
  created: {
    label: "Создан",
    className: "bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-50",
  },
  processing: {
    label: "В обработке",
    className: "bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-50",
  },
};

export interface CourseStatusBadgeProps {
  status: string;
}
export default function CourseStatusBadge({ status }: CourseStatusBadgeProps) {
  const config = STATUS_CONFIG[status];

  if (config) {
    return (
      <Badge variant="outline" className={config.className}>
        {config.label}
      </Badge>
    );
  }

  return <Badge variant="outline">{status}</Badge>;
}
