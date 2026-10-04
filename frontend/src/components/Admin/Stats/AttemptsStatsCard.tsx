import { AdminAttemptsStats } from "@/interfaces/admin.interface";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Percent, Target, Activity, Calendar } from "lucide-react";

interface AttemptsStatsCardProps {
  stats: AdminAttemptsStats;
}

export default function AttemptsStatsCard({ stats }: AttemptsStatsCardProps) {
  const cards = [
    {
      title: "Всего попыток",
      value: stats.totalAttempts,
      hint: "Общее количество запусков",
      icon: Target,
    },
    {
      title: "За сегодня",
      value: `+${stats.attemptsToday}`,
      hint: "Новые попытки за день",
      icon: Activity,
    },
    {
      title: "За неделю",
      value: `+${stats.attemptsThisWeek}`,
      hint: "Динамика за 7 дней",
      icon: Calendar,
    },
    {
      title: "Успешность",
      value: `${stats.successRate}%`,
      hint: "Доля успешных прохождений",
      icon: Percent,
    },
  ];

  return (
    <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      {cards.map((card) => (
        <Card key={card.title} className="shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              {card.title}
            </CardTitle>
            <card.icon className="size-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            <div className="text-2xl font-semibold tracking-tight">{card.value}</div>
            <p className="mt-1 text-xs text-muted-foreground">{card.hint}</p>
          </CardContent>
        </Card>
      ))}
    </div>
  );
}
