"use client";

import { useMemo, useState } from "react";
import { TaskFull, Question } from "@/interfaces/task.interface";
import { generateExportableHtml } from "@/lib/html-generator"; // <-- импорт нашей функции
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Button } from "@/components/ui/button";
import { CheckCircle2, XCircle, Copy, Check } from "lucide-react";
import { generateExportableYml } from "@/lib/yml-generator";
import AddCourseDialog from "@/components/Admin/Task/AddCourseDialog";

interface TaskFormProps {
  task: TaskFull;
}

export default function TaskForm({ task }: TaskFormProps) {
  const [isJsonCopied, setIsJsonCopied] = useState(false);
  const [isCopied, setIsCopied] = useState(false);
  const [isYamlCopied, setIsYamlCopied] = useState(false);

  let parsedDraft: Question[] | null = null;
  let rawJsonString = task.draft || "";
  let parseError = false;

  if (task.draft) {
    try {
      parsedDraft = JSON.parse(task.draft);
      rawJsonString = JSON.stringify(parsedDraft, null, 2);
    } catch (e) {
      console.error("Failed to parse draft JSON", e);
      parseError = true;
    }
  }

  const formattedDate = new Date(task.created).toLocaleString("ru-RU", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });

  const generatedYamlString = useMemo(() => {
    if (!parsedDraft) return "";
    return generateExportableYml(parsedDraft);
  }, [parsedDraft]);

  const handleCopyHtml = async () => {
    if (!parsedDraft) return;

    // Вызываем вынесенную функцию
    const htmlString = generateExportableHtml(parsedDraft);

    try {
      await navigator.clipboard.writeText(htmlString);
      setIsCopied(true);
      setTimeout(() => setIsCopied(false), 2000);
    } catch (err) {
      console.error("Failed to copy text: ", err);
    }
  };

  const handleCopyYaml = async () => {
    if (!generatedYamlString) return;

    try {
      await navigator.clipboard.writeText(generatedYamlString);
      setIsYamlCopied(true);
      setTimeout(() => setIsYamlCopied(false), 2000);
    } catch (err) {
      console.error("Failed to copy YAML: ", err);
    }
  };

  const handleCopyJson = async () => {
    if (!rawJsonString) return;

    try {
      await navigator.clipboard.writeText(rawJsonString);
      setIsJsonCopied(true);
      setTimeout(() => setIsJsonCopied(false), 2000);
    } catch (err) {
      console.error("Failed to copy JSON: ", err);
    }
  };

  return (
    <div className="space-y-6">
      {/* Информация о задаче */}
      <Card>
        <CardHeader>
          <CardTitle>Информация о задаче</CardTitle>
        </CardHeader>
        <CardContent className="space-y-4 text-sm">
          <div className="grid grid-cols-2 gap-4 sm:grid-cols-4">
            <div>
              <p className="text-muted-foreground font-medium">ID</p>
              <p className="font-mono">{task.id}</p>
            </div>
            <div>
              <p className="text-muted-foreground font-medium">Статус</p>
              <Badge variant={task.status === "failed" ? "destructive" : "default"}>
                {task.status}
              </Badge>
            </div>
            <div>
              <p className="text-muted-foreground font-medium">Создана</p>
              <p>{formattedDate}</p>
            </div>
            <div>{rawJsonString !== "" ? <AddCourseDialog draft={rawJsonString} /> : ""}</div>
          </div>

          {task.failed_reason && (
            <div className="mt-4 p-3 bg-destructive/10 text-destructive rounded-md">
              <p className="font-semibold">Причина ошибки:</p>
              <p>{task.failed_reason}</p>
            </div>
          )}
        </CardContent>
      </Card>

      {parseError && (
        <Card className="border-destructive">
          <CardContent className="pt-6 text-destructive">
            Ошибка чтения JSON. Данные могут отображаться некорректно.
          </CardContent>
        </Card>
      )}

      {task.draft && (
        <div className="space-y-4">
          <h2 className="text-2xl font-bold tracking-tight">Содержимое (Draft)</h2>

          <Tabs defaultValue="json" className="w-full">
            <TabsList className="grid w-full grid-cols-3 mb-4">
              <TabsTrigger value="json">JSON</TabsTrigger>
              <TabsTrigger value="html">HTML</TabsTrigger>
              <TabsTrigger value="yaml">YAML</TabsTrigger>
            </TabsList>

            <TabsContent value="json">
              {parsedDraft && parsedDraft.length > 0 ? (
                <>
                  <div className="flex justify-end mb-4">
                    <Button onClick={handleCopyJson} variant="outline" className="gap-2">
                      {isJsonCopied ? (
                        <>
                          <Check className="w-4 h-4 text-green-600" />
                          <span className="text-green-600">Скопировано</span>
                        </>
                      ) : (
                        <>
                          <Copy className="w-4 h-4" />
                          <span>Скопировать JSON</span>
                        </>
                      )}
                    </Button>
                  </div>
                  <Card>
                    <CardContent className="pt-6">
                      <pre className="bg-muted p-4 rounded-md overflow-x-auto text-sm font-mono text-foreground whitespace-pre-wrap">
                        {rawJsonString}
                      </pre>
                    </CardContent>
                  </Card>
                </>
              ) : (
                <Card>
                  <CardContent className="pt-6 text-muted-foreground">
                    Нет данных для отображения или формат не поддерживается.
                  </CardContent>
                </Card>
              )}
            </TabsContent>

            <TabsContent value="html" className="space-y-4">
              {parsedDraft && parsedDraft.length > 0 ? (
                <>
                  <div className="flex justify-end mb-4">
                    <Button onClick={handleCopyHtml} variant="outline" className="gap-2">
                      {isCopied ? (
                        <>
                          <Check className="w-4 h-4 text-green-600" />
                          <span className="text-green-600">Скопировано</span>
                        </>
                      ) : (
                        <>
                          <Copy className="w-4 h-4" />
                          <span>Скопировать HTML</span>
                        </>
                      )}
                    </Button>
                  </div>

                  {parsedDraft.map((question) => (
                    <Card key={question.id} className="overflow-hidden">
                      <CardHeader className="bg-muted/50 pb-4">
                        <CardTitle className="text-lg">Вопрос {question.number}</CardTitle>
                      </CardHeader>

                      <CardContent className="pt-4 space-y-6">
                        <div className="space-y-4">
                          <p className="text-base font-medium leading-relaxed">{question.text}</p>
                          {question.questionImg && (
                            <div className="relative rounded-md overflow-hidden border inline-block">
                              {/* eslint-disable-next-line @next/next/no-img-element */}
                              <img
                                src={question.questionImg}
                                alt={`К вопросу ${question.number}`}
                                className="max-h-64 object-contain"
                              />
                            </div>
                          )}
                        </div>

                        <div className="space-y-3">
                          <h4 className="text-sm font-semibold text-muted-foreground uppercase tracking-wider">
                            Варианты ответов:
                          </h4>
                          <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                            {question.answers.map((answer) => (
                              <div
                                key={answer.id}
                                className={`flex flex-col gap-3 p-4 rounded-lg border transition-colors ${
                                  answer.isCorrect
                                    ? "border-green-500 bg-green-50 dark:bg-green-950/20"
                                    : "border-border bg-card"
                                }`}
                              >
                                <div className="flex items-start justify-between gap-2">
                                  <span className="text-sm font-medium">{answer.text}</span>
                                  {answer.isCorrect ? (
                                    <CheckCircle2 className="w-5 h-5 text-green-600 shrink-0" />
                                  ) : (
                                    <XCircle className="w-5 h-5 text-muted-foreground/30 shrink-0" />
                                  )}
                                </div>

                                {answer.answerImg && (
                                  <div className="mt-auto">
                                    {/* eslint-disable-next-line @next/next/no-img-element */}
                                    <img
                                      src={answer.answerImg}
                                      alt="К ответу"
                                      className="max-h-32 rounded border object-contain"
                                    />
                                  </div>
                                )}
                              </div>
                            ))}
                          </div>
                        </div>
                      </CardContent>
                    </Card>
                  ))}
                </>
              ) : (
                <Card>
                  <CardContent className="pt-6 text-muted-foreground">
                    Нет данных для отображения или формат не поддерживается.
                  </CardContent>
                </Card>
              )}
            </TabsContent>

            <TabsContent value="yaml" className="space-y-4">
              {parsedDraft && parsedDraft.length > 0 ? (
                <>
                  <div className="flex justify-end mb-4">
                    <Button onClick={handleCopyYaml} variant="outline" className="gap-2">
                      {isYamlCopied ? (
                        <>
                          <Check className="w-4 h-4 text-green-600" />
                          <span className="text-green-600">Скопировано</span>
                        </>
                      ) : (
                        <>
                          <Copy className="w-4 h-4" />
                          <span>Скопировать YAML</span>
                        </>
                      )}
                    </Button>
                  </div>
                  <Card>
                    <CardContent className="pt-6">
                      <pre className="bg-muted p-4 rounded-md overflow-x-auto text-sm font-mono text-foreground whitespace-pre-wrap">
                        {generatedYamlString}
                      </pre>
                    </CardContent>
                  </Card>
                </>
              ) : (
                <Card>
                  <CardContent className="pt-6 text-muted-foreground">
                    Нет данных для конвертации в YAML.
                  </CardContent>
                </Card>
              )}
            </TabsContent>
          </Tabs>
        </div>
      )}
    </div>
  );
}
