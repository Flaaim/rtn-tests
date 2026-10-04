import UsersStatsCard from "@/components/Admin/Stats/UsersStatsCard";
import {
  fetchAdminAttemptsStatsAction,
  fetchAdminSubscriptionsStatsAction,
  fetchAdminUsersStatsAction,
} from "@/actions/admin";
import SubscriptionsStatsCard from "@/components/Admin/Stats/SubscriptionsStatsCard";
import AttemptsStatsCard from "@/components/Admin/Stats/AttemptsStatsCard";

export default async function AdminDashboardPage() {
  const [usersStats, subscriptionsStats, attemptsStats] = await Promise.all([
    fetchAdminUsersStatsAction(),
    fetchAdminSubscriptionsStatsAction(),
    fetchAdminAttemptsStatsAction(),
  ]);

  return (
    <div className="space-y-8">
      <div>
        <h1 className="text-2xl font-semibold tracking-tight">Дашборд</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          Общая статистика пользователей и состояния подписок.
        </p>
      </div>

      <div className="space-y-4">
        <h2 className="text-lg font-medium tracking-tight">Пользователи</h2>
        {!usersStats.ok || !usersStats.data ? (
          <div className="rounded-md bg-destructive/10 p-3 text-sm text-destructive">
            Не удалось загрузить статистику пользователей.
          </div>
        ) : (
          <UsersStatsCard stats={usersStats.data} />
        )}
      </div>

      <div className="space-y-4">
        <h2 className="text-lg font-medium tracking-tight">Подписки</h2>
        {!subscriptionsStats.ok || !subscriptionsStats.data ? (
          <div className="rounded-md bg-destructive/10 p-3 text-sm text-destructive">
            Не удалось загрузить статистику подписок.
          </div>
        ) : (
          <SubscriptionsStatsCard stats={subscriptionsStats.data} />
        )}
      </div>

      <div className="space-y-4">
        <h2 className="text-lg font-medium tracking-tight">Попытки тестирования</h2>
        {!attemptsStats.ok || !attemptsStats.data ? (
          <div className="rounded-md bg-destructive/10 p-3 text-sm text-destructive">
            Не удалось загрузить статистику попыток тестирования.
          </div>
        ) : (
          <AttemptsStatsCard stats={attemptsStats.data} />
        )}
      </div>
    </div>
  );
}
