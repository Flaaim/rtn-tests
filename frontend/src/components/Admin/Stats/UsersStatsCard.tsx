import { AdminUsersStats } from "@/interfaces/admin.interface";
import { Users, UserPlus, Calendar, BarChart3 } from "lucide-react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

interface UsersStatsCardProps {
  stats: AdminUsersStats;
}

export default function UsersStatsCard({ stats }: UsersStatsCardProps) {
  const cards = [
    {
      title: "Всего пользователей",
      value: stats.totalUsers,
      hint: "Общая база",
      icon: Users,
    },
    {
      title: "За сегодня",
      value: `+${stats.registrationsToday}`,
      hint: "Новые регистрации",
      icon: UserPlus,
    },
    {
      title: "За неделю",
      value: `+${stats.registrationsThisWeek}`,
      hint: "Динамика за 7 дней",
      icon: Calendar,
    },
    {
      title: "За 30 дней",
      value: `+${stats.registrationsLast30Days}`,
      hint: "Динамика за месяц",
      icon: BarChart3,
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
