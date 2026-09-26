import UserBreadcrumbs from "@/components/User/UserBreadcrumbs";
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { CreditCard, CalendarDays, ShieldCheck, AlertCircle, Info } from "lucide-react";
import { format } from "date-fns";
import { ru } from "date-fns/locale";
import { UserSubscription } from "@/interfaces/user.interface";
import { fetchUserSubscriptionAction } from "@/actions/profile";
import SubscriptionStatusBadge from "@/components/Domain/SubscriptionStatusBadge";
import AddSubscriptionDialog from "@/components/Subscription/AddSubscriptionDialog";

const SUBSCRIPTION_PLAN: Record<string, string> = {
  trial: "Пробный",
  basic: "Базовый",
};

export default async function SubscriptionPage() {
  const result = await fetchUserSubscriptionAction();

  if (!result.ok || !result.data) {
    if (!result.ok || !result.data) {
      return (
        <div className="space-y-6">
          <UserBreadcrumbs items={[{ title: "Подписка" }]} />
          <Card className="max-w-2xl mt-8 border-blue-200 shadow-sm">
            <CardHeader>
              <CardTitle className="text-xl flex items-center gap-2 text-blue-800">
                <Info className="h-5 w-5" />
                Подписка не найдена
              </CardTitle>
            </CardHeader>
            <CardContent className="pt-6">
              <p className="text-muted-foreground leading-relaxed">
                Вероятно, вы только создали аккаунт. При первом запуске теста автоматически
                активируется
                <strong className="text-foreground"> пробная подписка (на 1 день)</strong>. Чтобы
                получить полный и неограниченный доступ ко всем материалам сайта, необходимо перейти
                на базовую подписку.
              </p>
            </CardContent>
            <CardFooter>
              <AddSubscriptionDialog name="Оформить подписку"/>
            </CardFooter>
          </Card>
        </div>
      );
    }
  }

  const subscription: UserSubscription = result.data;
  const formatDate = (dateString: string) => {
    return format(new Date(dateString), "dd MMMM yyyy", { locale: ru });
  };

  return (
    <div className="space-y-6">
      <UserBreadcrumbs items={[{ title: "Подписка" }]} />

      <div>
        <h1 className="text-3xl font-bold tracking-tight">Управление подпиской</h1>
        <p className="text-muted-foreground text-sm mt-2">
          На данной странице вы можете посмотреть статус вашего тарифа и управлять оплатой.
        </p>
      </div>

      <div className="grid gap-6 md:grid-cols-2">
        {/* Карточка 1: Текущий план */}
        <Card className="shadow-sm">
          <CardHeader>
            <div className="flex items-center justify-between mb-1">
              <div className="flex items-center gap-2">
                <ShieldCheck className="h-5 w-5 text-blue-600" />
                <CardTitle className="text-xl">Текущий план</CardTitle>
              </div>
              <SubscriptionStatusBadge status={subscription.status} />
            </div>
            <CardDescription>Основная информация о вашем тарифе.</CardDescription>
          </CardHeader>
          <CardContent className="space-y-6">
            <div className="flex items-center justify-between p-4 bg-muted/30 rounded-lg border">
              <div className="space-y-1">
                <p className="text-sm text-muted-foreground font-medium">Тариф</p>
                <p className="font-semibold text-lg flex items-center gap-2">
                  {SUBSCRIPTION_PLAN[subscription.plan] || "Неизвестный тариф"}
                </p>
              </div>
            </div>

            {subscription.hasAccess && subscription.periodStart && subscription.periodEnd && (
              <div className="grid grid-cols-2 gap-4">
                <div className="space-y-2">
                  <p className="text-sm font-medium flex items-center gap-2 text-muted-foreground">
                    <CalendarDays className="h-4 w-4" /> Начало
                  </p>
                  <p className="text-sm font-semibold">{formatDate(subscription.periodStart)}</p>
                </div>
                <div className="space-y-2">
                  <p className="text-sm font-medium flex items-center gap-2 text-muted-foreground">
                    <CalendarDays className="h-4 w-4" /> Окончание
                  </p>
                  <p className="text-sm font-semibold">{formatDate(subscription.periodEnd)}</p>
                </div>
              </div>
            )}
          </CardContent>
        </Card>

        {/* Карточка 2: Действия / Оплата */}
        <Card className="shadow-sm flex flex-col">
          <CardHeader>
            <div className="flex items-center gap-2 mb-1">
              <CreditCard className="h-5 w-5 text-blue-600" />
              <CardTitle className="text-xl">Продление и оплата</CardTitle>
            </div>
            <CardDescription>
              {subscription.hasAccess
                ? "Управляйте автопродлением или повысьте уровень тарифа."
                : "Оформите подписку, чтобы получить полный доступ ко всем материалам."}
            </CardDescription>
          </CardHeader>
          <CardContent className="flex-1">
            {!subscription.hasAccess ? (
              <div className="bg-blue-50 text-blue-900 border border-blue-200 p-4 rounded-lg flex gap-3">
                <AlertCircle className="h-5 w-5 shrink-0 mt-0.5 text-blue-600" />
                <p className="text-sm leading-relaxed">
                  Сейчас у вас нет активной подписки. Доступ к курсам и тестам ограничен. Выберите
                  подходящий тариф для продолжения обучения.
                </p>
              </div>
            ) : (
              <div className="space-y-4">
                <p className="text-sm text-muted-foreground">
                  Ваш текущий тариф:{" "}
                  <strong className="text-foreground capitalize">
                    {SUBSCRIPTION_PLAN[subscription.plan] || "Без подписки"}
                  </strong>
                  . Если вы отмените подписку, она продолжит работать до конца оплаченного периода.
                </p>
              </div>
            )}
          </CardContent>
          <CardFooter className="pt-4 border-t">
            {subscription.hasAccess ? (
              <div className="flex gap-3 w-full">
                <AddSubscriptionDialog name="Продлить подписку"/>
                <Button variant="outline" >
                  Отменить
                </Button>
              </div>
            ) : (
              <AddSubscriptionDialog name="Оформить подписку"/>
            )}
          </CardFooter>
        </Card>
      </div>
    </div>
  );
}
