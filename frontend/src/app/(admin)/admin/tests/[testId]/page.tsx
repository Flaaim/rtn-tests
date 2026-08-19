import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";
import { TestFull, Ticket } from "@/interfaces/test.interface";
import { fetchTestAction } from "@/actions/test";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import TestStatusBadge from "@/components/Admin/Test/Status/TestStatusBadge";
import { ChangeCipherTestDialog } from "@/components/Admin/Test/ChangeCipherTestDialog";
import RenameTestDialog from "@/components/Admin/Test/RenameTestDialog";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { Badge } from "@/components/ui/badge";
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from "@/components/ui/accordion";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { PUBLIC_ASSETS_URL } from "@/app/api";
import {Button} from "@base-ui/react";

interface TestOverviewPageProps {
  params: Promise<{ testId: string }>;
}

export default async function TestOverviewPage({ params }: TestOverviewPageProps) {
  const { testId } = await params;

  const result = await fetchTestAction(testId);

  if (!result.ok || !result.data) {
    return null;
  }

  const test: TestFull = result.data;

  const items = [{ title: "Тесты", href: "/admin/tests" }, { title: test.name }];

  const formattedDate = new Date(test.createdAt).toLocaleString("ru-RU", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={items} />
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Основная информация</h1>
      </div>
      <div className="space-y-6">
        <Card>
          <CardHeader>
            <div className="grid grid-cols-2 gap-4 items-center">
              <CardTitle>Курс: {test.name}</CardTitle>
              <div className="justify-self-end">
                <RenameTestDialog id={test.id} name={test.name} description={test.description} />
              </div>
            </div>
          </CardHeader>
          <CardContent className="space-y-4 text-sm">
            <p className="text-muted-foreground border-b pb-4">{test.description}</p>
            <div className="grid grid-cols-2 gap-4 sm:grid-cols-4">
              <div>
                <p className="text-muted-foreground font-medium">ID</p>
                <p className="font-mono">{test.id}</p>
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Шифр</p>
                <p className="font-mono">
                  {test.cipher} <ChangeCipherTestDialog id={test.id} cipher={test.cipher} />
                </p>
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Дата создания</p>
                <p className="font-mono">{formattedDate}</p>
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Статус</p>
                <p className="font-mono">
                  <TestStatusBadge status={test.status} />
                </p>
              </div>
            </div>

            <div className="grid grid-cols-2 gap-4 sm:grid-cols-4 items-end">
              <div>
                <p className="text-muted-foreground font-medium">Количество билетов</p>
                <p className="font-mono">{test.numberOfTickets}</p>
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Вопросов в билете</p>
                <p className="font-mono">{test.numberQuestionsInTicket}</p>
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Допустимо ошибок</p>
                <p className="font-mono">{test.allowedMistakes}</p>
              </div>
              <div className="sm:justify-self-end w-full sm:w-auto">
                <Button className="w-full sm:w-auto">Изменить настройки</Button>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <div className="space-y-6">
        <Tabs defaultValue="json" className="w-full">
          <TabsList className="grid w-full grid-cols-3 mb-4">
            <TabsTrigger value="tickets">Билеты</TabsTrigger>
            <TabsTrigger value="questions">Вопросы</TabsTrigger>
          </TabsList>

          <TabsContent value="tickets" className="space-y-4">
            {test.tickets && test.tickets.length > 0 ? (
              <Card>
                <CardContent className="pt-6">
                  <Accordion className="w-full">
                    {test.tickets.map((ticket: Ticket) => (
                      <AccordionItem key={ticket.number} value={`ticket-${ticket.number}`}>
                        <AccordionTrigger className="hover:no-underline">
                          <div className="flex items-center gap-3">
                            <span className="font-medium text-base">Билет № {ticket.number}</span>
                            <Badge variant="secondary">
                              Вопросов: {ticket.questions?.length || 0}
                            </Badge>
                          </div>
                        </AccordionTrigger>
                        <AccordionContent>
                          <Table>
                            <TableHeader>
                              <TableRow>
                                <TableHead>№</TableHead>
                                <TableHead>Вопрос</TableHead>
                              </TableRow>
                            </TableHeader>
                            <TableBody>
                              {ticket.questions?.map((q, idx) => (
                                <TableRow key={q.id}>
                                  <TableCell className="font-medium">{idx + 1}</TableCell>
                                  <TableCell className="font-medium">
                                    <p className="text-sm font-medium leading-relaxed text-wrap">
                                      {q.text}
                                    </p>
                                    {q.questionImg && (
                                      <div className="relative rounded-md overflow-hidden border inline-block">
                                        {/* eslint-disable-next-line @next/next/no-img-element */}
                                        <img
                                          src={`${PUBLIC_ASSETS_URL}${process.env.NEXT_PUBLIC_QUESTION_IMAGES}${q.questionImg}`}
                                          alt={`К вопросу`}
                                          className="max-h-32 object-contain"
                                        />
                                      </div>
                                    )}
                                  </TableCell>
                                </TableRow>
                              ))}
                            </TableBody>
                          </Table>
                        </AccordionContent>
                      </AccordionItem>
                    ))}
                  </Accordion>
                </CardContent>
              </Card>
            ) : (
              <div className="text-muted-foreground text-sm py-4">Нет данных для отображения.</div>
            )}
          </TabsContent>
        </Tabs>
      </div>
    </div>
  );
}
