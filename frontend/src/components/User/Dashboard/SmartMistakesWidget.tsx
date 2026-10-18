"use client";

import { useEffect, useState } from "react";
import { CompetencyMatrix } from "@/interfaces/test.interface";
import { useRouter } from "next/navigation";
import { toast } from "sonner";
import { fetchCompetencyMatrix } from "@/actions/test";
import { AlertTriangle, BrainCircuit, CheckCircle2, HelpCircle, Loader2 } from "lucide-react";
import { Button } from "@base-ui/react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";

export default function SmartMistakesWidget() {
  const router = useRouter();
  const [matrix, setMatrix] = useState<CompetencyMatrix | null>(null);
  const [loading, setLoading] = useState(true);
  const [isLaunching, setIsLaunching] = useState(false);

  useEffect(() => {
    async function fetchMatrix() {
      setLoading(true);
      try {
        const result = await fetchCompetencyMatrix();
        console.log(result);
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
        {/* Статистика по проблемным зонам */}
        <div className="space-y-3">
          {matrix.needsWork.length > 0 && (
            <div className="space-y-2">
              <h4 className="text-sm font-semibold flex items-center text-destructive">
                <AlertTriangle className="w-4 h-4 mr-1" /> Требуют внимания
              </h4>
              {matrix.needsWork.map((test) => (
                <div
                  key={test.id}
                  className="flex justify-between text-sm bg-destructive/10 px-3 py-2 rounded-md"
                >
                  <span className="truncate pr-4">{test.name}</span>
                  <span className="truncate pr-4">{test.cipher}</span>
                  <span className="font-bold text-destructive">{test.percentage}%</span>
                </div>
              ))}
            </div>
          )}

          <div className="space-y-3">
            {matrix.good.length > 0 && (
              <div className="space-y-2">
                <h4 className="text-sm font-semibold flex items-center text-amber-600">
                  <HelpCircle className="w-4 h-4 mr-1" /> Неплохо
                </h4>
                {matrix.good.map((test) => (
                  <div
                    key={test.id}
                    className="flex justify-between text-sm bg-amber-500/10 px-3 py-2 rounded-full border"
                  >
                    <span className="truncate pr-4">{test.name}</span>
                    <span className="truncate pr-4">{test.cipher}</span>
                    <span className="font-bold text-destructive">{test.percentage}%</span>
                  </div>
                ))}
              </div>
            )}
          </div>

          {matrix.excellent.length > 0 && (
            <div className="space-y-2 pt-2">
              <h4 className="text-sm font-semibold flex items-center text-emerald-600">
                <CheckCircle2 className="w-4 h-4 mr-1" /> Отлично усвоено
              </h4>
              <div className="flex flex-wrap gap-2">
                {matrix.excellent.map((test) => (
                  <span
                    key={test.id}
                    className="text-xs bg-emerald-100 text-emerald-800 px-2 py-1 rounded-full border"
                  >
                    {test.name}
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
