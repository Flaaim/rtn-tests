import { fetchCoursesPaginatedAction } from "@/actions/course";
import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";
import { AlertCircle } from "lucide-react";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { CourseItem } from "@/interfaces/course.interface";
import { Badge } from "@/components/ui/badge";
import Pagination from "@/components/Pagination/Pagination";
import CourseSearch from "@/components/Admin/Course/CourseSearch";

const STATUS_CONFIG: Record<string, { label: string; className: string }> = {
  created: {
    label: "Создан",
    className: "bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-50",
  },
  processing: {
    label: "В обработке",
    className: "bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-50",
  },
};
interface AdminCoursesPageProps {
  searchParams: Promise<{ page?: string; perPage?: string; q?: string }>;
}

export default async function AdminCoursesPage({ searchParams }: AdminCoursesPageProps) {
  const currentPage = Number((await searchParams).page) || 1;
  const perPage = Number((await searchParams).perPage) || 15;
  const search = String((await searchParams).q || "");

  const result = await fetchCoursesPaginatedAction(currentPage, perPage, search);

  if (!result.ok || !result.data) {
    return (
      <div className="space-y-6">
        <AdminBreadcrumbs items={[{ title: "Курсы" }]} />
        <div className="flex items-center justify-between">
          <h1 className="text-3xl font-bold">Курсы</h1>
        </div>
        <div className="mx-auto max-w-4xl p-4 md:p-8">
          <div className="flex min-h-[40vh] flex-col items-center justify-center space-y-4 text-center">
            <div className="flex size-16 items-center justify-center rounded-full bg-destructive/10 text-destructive">
              <AlertCircle className="size-8" />
            </div>
            <h2 className="text-xl font-semibold">Не удалось загрузить курсы</h2>
            <p className="text-sm text-muted-foreground">
              {result.error ?? "Произошла непредвиденная ошибка"}
            </p>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={[{ title: "Курсы" }]} />
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Курсы</h1>
      </div>
      <CourseSearch />
      <div className="rounded-md border bg-white">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Название</TableHead>
              <TableHead>Шифр</TableHead>
              <TableHead>Создан</TableHead>
              <TableHead>Статус</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {result.data.items.length === 0 ? (
              <TableRow>
                <TableCell colSpan={5} className="text-muted-foreground">
                  Курсы отсутсвуют...
                </TableCell>
              </TableRow>
            ) : (
              result.data.items.map((course: CourseItem) => (
                <TableRow key={course.courseId}>
                  <TableCell className="font-medium">{course.name}</TableCell>
                  <TableCell className="font-medium">{course.cipher}</TableCell>
                  <TableCell className="font-medium">
                    {new Date(course.createdAt).toLocaleDateString("ru-RU")}
                  </TableCell>
                  <TableCell className="font-medium">
                    {(() => {
                      const config = STATUS_CONFIG[course.status];
                      if (config) {
                        return (
                          <Badge variant="outline" className={config.className}>
                            {config.label}
                          </Badge>
                        );
                      }

                      return <Badge variant="outline">{course.status}</Badge>;
                    })()}
                  </TableCell>
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>
        <Pagination
          currentPage={currentPage}
          totalPages={result.data.totalCount}
          baseUrl="/admin/courses"
        />
      </div>
    </div>
  );
}
