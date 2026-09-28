import fetchAdminUsersStatsAction from "@/actions/admin";
import UsersStatsCard from "@/components/Admin/Stats/UsersStatsCard";

export default async function AdminDashboardPage() {
  const usersStats = await fetchAdminUsersStatsAction();

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-semibold tracking-tight">Дашборд</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          Обзор регистраций User и Active Status подписок.
        </p>
      </div>
      {!usersStats.ok || !usersStats.data ? (
        <div className="rounded-md bg-destructive/10 p-3 text-sm text-destructive">
          Не удалось загрузить статистику.
        </div>
      ) : (
        <UsersStatsCard stats={usersStats.data} />
      )}
    </div>
  );
}
