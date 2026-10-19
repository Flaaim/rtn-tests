"use client";

import { useEffect, useState } from "react";
import { CompetencyMatrix } from "@/interfaces/test.interface";
import { useRouter } from "next/navigation";
import { toast } from "sonner";
import { fetchCompetencyMatrix } from "@/actions/test";
import { AlertTriangle, BrainCircuit, CheckCircle2, HelpCircle, Loader2 } from "lucide-react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { launchMistakesAttemptAction } from "@/actions/attempt";
import { Button } from "@/components/ui/button";

export default function SmartMistakesWidget() {
  const router = useRouter();
  const [matrix, setMatrix] = useState<CompetencyMatrix | null>(null);
  const [loading, setLoading] = useState(true);
  const [launchingId, setLaunchingId] = useState<string | null>(null);

  useEffect(() => {
    async function fetchMatrix() {
      setLoading(true);
      try {
        const result = await fetchCompetencyMatrix();

        if (result.ok && result.data) {
          setMatrix(result.data);
        }
      } catch (error) {
        const err = error instanceof Error ? error : new Error("Ошибка при получении данных");
        toast.error(err.message);
      } finally {
        setLoading(false);
      }
    }
    void fetchMatrix();
  }, []);

  const handleLaunchMistakes = async (testId: string) => {
    setLaunchingId(testId);
    try {
      const result = await launchMistakesAttemptAction(testId);
      if (!result.ok || !result.data) {
        toast.error("Не удалось запустить тест. Нет ошибок для работы.");
        return;
      }
      router.push(`/attempts/${result.data.attemptId}`);
    } catch (error) {
      const err = error instanceof Error ? error : new Error("Ошибка при получении данных");
      toast.error(err.message);
    } finally {
      setLaunchingId(null);
    }
  };

  if (loading)
    return (
      <div className="h-40 flex items-center justify-center">
        <Loader2 className="animate-spin" />
      </div>
    );

  if (!matrix) return null;

  const totalErrors = matrix.needsWork.length + matrix.good.length;

  return (
    <Card className="border-primary/20 shadow-sm bg-gradient-to-br from-card to-primary/5">
      <CardHeader>
        <div className="flex items-center gap-3">
          <div className="p-2 bg-primary/10 rounded-lg">
            <BrainCircuit className="w-6 h-6 text-primary" />
          </div>
          <div>
            <CardTitle>Умная работа над ошибками</CardTitle>
            <CardDescription>Умный подбор вопросов на основе ваших слабых мест</CardDescription>
          </div>
        </div>
      </CardHeader>
      <CardContent className="space-y-6">
        {totalErrors === 0 && matrix.excellent.length === 0 ? (
          <p className="text-sm text-muted-foreground">У вас пока нет завершенных тестов.</p>
        ) : null}

        <div className="space-y-5">
          {/* Блок: ТРЕБУЮТ ВНИМАНИЯ */}
          {matrix.needsWork.length > 0 && (
            <div className="space-y-2">
              <h4 className="text-sm font-semibold flex items-center text-destructive">
                <AlertTriangle className="w-4 h-4 mr-1" /> Требуют внимания
              </h4>
              <div className="space-y-2">
                {matrix.needsWork.map((test) => (
                  <div
                    key={test.id}
                    className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-sm bg-destructive/10 px-3 py-2.5 rounded-md"
                  >
                    <div className="flex flex-col min-w-0 flex-1">
                      <div className="flex items-center gap-2">
                        <span className="font-bold text-foreground truncate">{test.cipher}</span>
                        <span className="font-bold text-destructive">{test.percentage}% верно</span>
                      </div>
                      <span className="truncate text-muted-foreground text-xs" title={test.name}>
                        {test.name}
                      </span>
                    </div>

                    <Button
                      size="sm"
                      variant="destructive"
                      disabled={launchingId !== null}
                      onClick={() => handleLaunchMistakes(test.id)}
                      className="shrink-0 w-full sm:w-auto"
                    >
                      {launchingId === test.id ? (
                        <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                      ) : null}
                      Проработать
                    </Button>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* Блок: НЕПЛОХО */}
          {matrix.good.length > 0 && (
            <div className="space-y-2 pt-2">
              <h4 className="text-sm font-semibold flex items-center text-amber-600">
                <HelpCircle className="w-4 h-4 mr-1" /> Неплохо усвоено
              </h4>
              <div className="space-y-2">
                {matrix.good.map((test) => (
                  <div
                    key={test.id}
                    className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-sm bg-amber-500/10 px-3 py-2.5 rounded-md border border-amber-500/20"
                  >
                    <div className="flex flex-col min-w-0 flex-1">
                      <div className="flex items-center gap-2">
                        <span className="font-bold text-foreground truncate">{test.cipher}</span>
                        {/* ❗️ Поправил цвет процентов на янтарный */}
                        <span className="font-bold text-amber-600">{test.percentage}% верно</span>
                      </div>
                      <span className="truncate text-muted-foreground text-xs" title={test.name}>
                        {test.name}
                      </span>
                    </div>

                    <Button
                      size="sm"
                      variant="outline"
                      disabled={launchingId !== null}
                      onClick={() => handleLaunchMistakes(test.id)}
                      className="shrink-0 w-full sm:w-auto text-amber-700 hover:text-amber-800 hover:bg-amber-500/20 border-amber-500/30"
                    >
                      {launchingId === test.id ? (
                        <Loader2 className="w-4 h-4 mr-2 animate-spin" />
                      ) : null}
                      Повторить
                    </Button>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* Блок: ОТЛИЧНО */}
          {matrix.excellent.length > 0 && (
            <div className="space-y-2 pt-2">
              <h4 className="text-sm font-semibold flex items-center text-emerald-600">
                <CheckCircle2 className="w-4 h-4 mr-1" /> Отлично усвоено
              </h4>
              <div className="flex flex-wrap gap-2">
                {matrix.excellent.map((test) => (
                  <span
                    key={test.id}
                    className="text-xs bg-emerald-100 text-emerald-800 px-2.5 py-1.5 rounded-md border border-emerald-200 font-medium"
                    title={test.name}
                  >
                    {test.cipher} • {test.percentage}%
                  </span>
                ))}
              </div>
            </div>
          )}
        </div>
      </CardContent>
    </Card>
  );
}
