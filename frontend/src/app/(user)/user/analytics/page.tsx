import { fetchPopularTests } from "@/actions/test";
import UserBreadcrumbs from "@/components/User/UserBreadcrumbs";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { TrendingUp } from "lucide-react";
import { PopularTestsDTO } from "@/interfaces/test.interface";

export default async function AnalyticsPage() {
  const result = await fetchPopularTests();

  if (!result.ok || !result.data) {
    return (
      <div className="space-y-6">
        <UserBreadcrumbs items={[{ title: "Аналитика" }]} />
        <div className="flex items-center justify-between">
          <h1 className="text-3xl font-bold tracking-tight">Аналитика</h1>
        </div>
        <div className="flex h-64 w-full items-center justify-center rounded-lg border border-dashed bg-muted/30 text-muted-foreground">
          Данные отсутствуют. Возможно их просто еще нет.
        </div>
      </div>
    );
  }
  const popularTests: PopularTestsDTO[] = result.data;
  return (
    <div className="space-y-6">
      <UserBreadcrumbs items={[{ title: "Аналитика" }]} />
      <div>
        <h1 className="text-3xl font-bold tracking-tight">Аналитика</h1>
      </div>
      <div className="grid gap-6 md:grid-cols-1">
        <Card className="shadow-sm">
          <CardHeader className="flex flex-row items-center justify-between pb-2">
            <div className="space-y-1">
              <CardTitle className="text-base font-semibold">Популярные тесты</CardTitle>
              <p className="text-sm text-muted-foreground">
                Топ ваших тестов по количеству прохождений
              </p>
            </div>
            <TrendingUp className="size-4 text-muted-foreground" />
          </CardHeader>
          <CardContent>
            {popularTests.length === 0 ? (
              <p className="text-sm text-muted-foreground text-center py-4">
                Нет данных для отображения
              </p>
            ) : (
              <div className="space-y-4 mt-4">
                {popularTests.map((test, index) => (
                  <div key={`${test.cipher}-${index}`} className="flex items-center gap-4">
                    <div className="flex items-center gap-4 flex-1 min-w-0">
                      <div className="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary font-semibold text-sm">
                        {index + 1}
                      </div>
                      <div className="min-w-0">
                        <p className="text-sm font-medium leading-none truncate">{test.name}</p>
                        <p className="text-xs text-muted-foreground mt-1 truncate">
                          Шифр: {test.cipher}
                        </p>
                      </div>
                    </div>
                    <div className="flex items-center gap-6 shrink-0 text-right">
                      <div className="text-sm font-semibold">
                        {test.averageScore.toFixed(1)} %
                        <span className="text-xs text-muted-foreground font-normal ml-1">
                          правильных ответов
                        </span>
                      </div>
                      <div className="text-sm font-semibold">
                        {test.testsAttempts.toLocaleString("ru-RU")}
                        <span className="text-xs text-muted-foreground font-normal ml-1">
                          попыток
                        </span>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
