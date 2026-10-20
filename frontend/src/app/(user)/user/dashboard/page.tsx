import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { BookOpen, History, Trophy, Activity, ArrowRight } from "lucide-react";
import { Button } from "@/components/ui/button";
import Link from "next/link";
import { fetchAttemptStatAction } from "@/actions/profile";
import { UserAttemptStatsDTO } from "@/interfaces/user.interface";
import UserBreadcrumbs from "@/components/User/UserBreadcrumbs";
import SmartMistakesWidget from "@/components/User/Dashboard/SmartMistakesWidget";

export default async function DashboardPage() {
  const result = await fetchAttemptStatAction();

  if (!result.ok || !result.data) {
    return (
      <div className="space-y-6">
        <UserBreadcrumbs items={[{ title: "Главная" }]} />
        <div className="flex items-center justify-between">
          <h1 className="text-3xl font-bold tracking-tight">Личный кабинет</h1>
        </div>
        <div className="flex h-64 w-full items-center justify-center rounded-lg border border-dashed bg-muted/30 text-muted-foreground">
          Проблема при загрузке данных. Попробуйте обновить страницу.
        </div>
      </div>
    );
  }
  const userAttemptStats: UserAttemptStatsDTO = result.data;

  return (
    <div className="mx-auto max-w-4xl space-y-8 p-4 md:p-8">
      {/* Заголовок страницы */}
      <div>
        <h1 className="text-3xl font-bold tracking-tight">Личный кабинет</h1>
        <p className="text-muted-foreground text-sm mt-2">
          Добро пожаловать! Здесь собрана ваша статистика и быстрый доступ к обучению.
        </p>
      </div>

      {/* Верхний ряд: Статистика (3 колонки) */}
      <div className="grid gap-4 sm:grid-cols-3">
        <Card className="shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              Пройдено тестов
            </CardTitle>
            <Trophy className="h-4 w-4 text-green-600" />
          </CardHeader>
          <CardContent>
            <div className="text-3xl font-bold">{userAttemptStats.completedTests}</div>
            <p className="text-xs text-muted-foreground mt-1">Успешно завершенных билетов</p>
          </CardContent>
        </Card>

        <Card className="shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">В процессе</CardTitle>
            <Activity className="h-4 w-4 text-blue-600" />
          </CardHeader>
          <CardContent>
            <div className="text-3xl font-bold">{userAttemptStats.inProgressTests}</div>
            <p className="text-xs text-muted-foreground mt-1">Незавершенные попытки</p>
          </CardContent>
        </Card>

        <Card className="shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between pb-2">
            <CardTitle className="text-sm font-medium text-muted-foreground">
              Средний балл
            </CardTitle>
            <History className="h-4 w-4 text-indigo-600" />
          </CardHeader>
          <CardContent>
            <div className="text-3xl font-bold">
              {userAttemptStats.averageScore.toFixed(1) + " %"}
            </div>
            <p className="text-xs text-muted-foreground mt-1">Доля правильных ответов</p>
          </CardContent>
        </Card>
      </div>

      {/* Нижний ряд: Основные разделы (Навигация) */}
      <div className="grid gap-6 md:grid-cols-2">
        <Card className="relative overflow-hidden shadow-sm transition-colors hover:border-primary/50 group">
          <CardHeader>
            <div className="flex items-center gap-2 mb-1">
              <BookOpen className="h-6 w-6 text-primary" />
              <CardTitle className="text-xl">Каталог тестов</CardTitle>
            </div>
            <CardDescription>
              Выберите направление и начните подготовку по актуальным билетам.
            </CardDescription>
          </CardHeader>
          <CardContent className="pt-4 border-t mt-2">
            <Button
              className="w-full sm:w-auto group-hover:bg-primary/90"
              render={
                <Link href="/catalog">
                  Перейти в каталог{" "}
                  <ArrowRight className="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" />
                </Link>
              }
            ></Button>
          </CardContent>
        </Card>

        <Card className="relative overflow-hidden shadow-sm transition-colors hover:border-primary/50 group">
          <CardHeader>
            <div className="flex items-center gap-2 mb-1">
              <History className="h-6 w-6 text-primary" />
              <CardTitle className="text-xl">Мои результаты</CardTitle>
            </div>
            <CardDescription>
              Просматривайте историю тестирований, разбирайте ошибки и скачивайте отчеты.
            </CardDescription>
          </CardHeader>
          <CardContent className="pt-4 border-t mt-2">
            <Button
              variant="outline"
              className="w-full sm:w-auto"
              render={
                <Link href="/user/results">
                  {" "}
                  Смотреть историю
                  <ArrowRight className="ml-2 h-4 w-4 transition-transform group-hover:translate-x-1" />
                </Link>
              }
            ></Button>
          </CardContent>
        </Card>
      </div>

      <div className="grid gap-6 md:grid-cols-1">
        <SmartMistakesWidget />
      </div>
    </div>
  );
}
