import { AdminSubscriptionsStats } from "@/interfaces/admin.interface";
import {Activity, Clock, Zap, Percent, Target} from "lucide-react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

interface SubscriptionsStatsCardProps {
  stats: AdminSubscriptionsStats;
}

export default function SubscriptionsStatsCard({ stats }: SubscriptionsStatsCardProps) {
  const cards = [
    {
      title: "Активные",
      value: stats.activeSubscriptions,
      hint: "Оплаченные подписки",
      icon: Activity,
    },
    {
      title: "Триал (Пробные)",
      value: stats.trialSubscriptions,
      hint: "Тестируют сервис",
      icon: Zap,
    },
    {
      title: "Истекшие",
      value: stats.expiredSubscriptions,
      hint: "Требуют продления",
      icon: Clock,
    },
    {
      title: "Ожидающие",
      value: stats.waitSubscriptions,
      hint: "Запланированые админом",
      icon: Target,
    },
    {
      title: "Конверсия",
      value: `${stats.conversionRate}%`,
      hint: "Из регистрации в оплату",
      icon: Percent,
    },
  ];

  return (
    <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
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
