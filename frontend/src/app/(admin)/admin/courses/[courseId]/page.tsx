import { fetchCourseAction } from "@/actions/course";
import { CourseFull, ProductDTO, Question } from "@/interfaces/course.interface";
import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import CourseStatusBadge from "@/components/Admin/Domain/Course/CourseStatusBadge";
import { CheckCircle2, XCircle } from "lucide-react";
import { PUBLIC_ASSETS_URL } from "@/app/api";
import { Badge } from "@/components/ui/badge";
import RenameCourseDialog from "@/components/Admin/Course/RenameCourseDialog";
import UpdateQuestionsCourseDialog from "@/components/Admin/Course/UpdateQuestionsCourseDialog";
import QuestionSearchForm from "@/components/Admin/Course/QuestionSearchForm";
import QuestionFormTypeBadge from "@/components/Admin/Domain/QuestionFormTypeBadge";
import RenderCourseDialog from "@/components/Admin/Course/RenderCourseDialog";
import { Button } from "@/components/ui/button";

interface CourseOverviewPageProps {
  params: Promise<{ courseId: string }>;
  searchParams: Promise<{ q?: string }>;
}
export default async function CourseOverviewPage({
  params,
  searchParams,
}: CourseOverviewPageProps) {
  const { courseId } = await params;
  const { q } = await searchParams;

  const result = await fetchCourseAction(courseId);

  if (!result.ok || !result.data) {
    return null;
  }

  const course: CourseFull = result.data;
  const items = [{ title: "Курсы", href: "/admin/courses" }, { title: course.name }];
  const product: ProductDTO | null = course.product;
  const pdfUrl = product
    ? `${PUBLIC_ASSETS_URL}${process.env.NEXT_PUBLIC_COURSE_ANSWERS}${product.filePath}`
    : "#";

  const formattedDate = (date: Date | string) => {
    return new Date(date).toLocaleString("ru-RU", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    });
  };

  const filteredQuestions = q
    ? course.questions.filter((question) => question.text.toLowerCase().includes(q.toLowerCase()))
    : course.questions;

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
              <CardTitle>Курс: {course.name}</CardTitle>
              <div className="justify-self-end">
                <RenameCourseDialog
                  id={course.courseId}
                  name={course.name}
                  cipher={course.cipher}
                />
              </div>
            </div>
          </CardHeader>
          <CardContent className="space-y-4 text-sm">
            <div className="grid grid-cols-2 gap-4 sm:grid-cols-4">
              <div>
                <p className="text-muted-foreground font-medium">ID</p>
                <p className="font-mono">{course.courseId}</p>
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Шифр</p>
                <p className="font-mono">{course.cipher}</p>
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Дата создания</p>
                <p className="font-mono">{formattedDate(course.createdAt)}</p>
              </div>
              <div>
                <p className="text-muted-foreground font-medium">Статус</p>
                <p className="font-mono">
                  <CourseStatusBadge status={course.status} />
                </p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
      <div className="space-y-4">
        <UpdateQuestionsCourseDialog id={course.courseId} />
        <RenderCourseDialog id={course.courseId} />
        <QuestionSearchForm />
        {product && (
          <div className="space-y-6">
            <Card>
              <CardHeader>
                <div className="grid grid-cols-1 gap-4 items-center">
                  <CardTitle>Файл</CardTitle>
                  <p>Изменить данные файла можно непосредственно на странице продукта.</p>
                  <Button size="sm" className="w-fit">
                    <a href={pdfUrl} target="_blank" rel="noopener noreferrer">
                      Посмотреть PDF
                    </a>
                  </Button>
                </div>
              </CardHeader>
              <CardContent className="space-y-4 text-sm">
                <div className="grid grid-cols-3 gap-4 sm:grid-cols-3">
                  <div>
                    <p className="text-muted-foreground font-medium">Путь</p>
                    <p className="font-mono">{product.filePath}</p>
                  </div>
                  <div>
                    <p className="text-muted-foreground font-medium">Стоимость</p>
                    <p className="font-mono">
                      {product.amount} {product.currency}
                    </p>
                  </div>
                  <div>
                    <p className="text-muted-foreground font-medium">Дата создания</p>
                    <p className="font-mono">{formattedDate(product.addedAt)}</p>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        )}

        <div className="flex items-center gap-3">
          <h2 className="text-2xl font-bold tracking-tight">Вопросы курса</h2>
          {q && <Badge variant="secondary">Найдено: {filteredQuestions.length}</Badge>}
        </div>

        {filteredQuestions.length === 0 ? (
          <Card>
            <CardContent className="p-8 text-center text-muted-foreground">
              По запросу &quot;{q}&quot; вопросов не найдено.
            </CardContent>
          </Card>
        ) : (
          filteredQuestions.map((question: Question, idx: number) => (
            <Card key={question.id} className="overflow-hidden">
              <CardHeader>
                <CardTitle className="text-lg">Вопрос: {idx + 1}</CardTitle>
              </CardHeader>

              <CardContent className="pt-4 space-y-6">
                <div className="space-y-4">
                  <p className="text-muted-foreground font-medium">
                    Тип: <QuestionFormTypeBadge type={question.form} />
                  </p>
                  <p className="text-base font-medium leading-relaxed">{question.text}</p>
                  {question.questionImg && (
                    <div className="relative rounded-md overflow-hidden border inline-block">
                      {/* eslint-disable-next-line @next/next/no-img-element */}
                      <img
                        src={`${PUBLIC_ASSETS_URL}${process.env.NEXT_PUBLIC_QUESTION_IMAGES}${question.questionImg}`}
                        alt="Изображение вопроса"
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
                              src={`${PUBLIC_ASSETS_URL}${process.env.NEXT_PUBLIC_QUESTION_IMAGES}${answer.answerImg}`}
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
          ))
        )}
      </div>
    </div>
  );
}
