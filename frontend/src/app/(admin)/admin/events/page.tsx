import { fetchEventsPagination } from "@/actions/event";
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
import { EventDTO, ListEventsDTO } from "@/interfaces/event.interface";
import Link from "next/link";
import Pagination from "@/components/Pagination/Pagination";
import EventSearch from "@/components/Admin/Event/EventSearch";

interface AdminEventsPageProps {
  searchParams: Promise<{ page?: string; perPage?: string; q?: string }>;
}

export default async function AdminRecipientsPage({ searchParams }: AdminEventsPageProps) {
  const currentPage = Number((await searchParams).page) || 1;
  const perPage = Number((await searchParams).perPage) || 15;
  const search = String((await searchParams).q || "");

  const result = await fetchEventsPagination(currentPage, perPage, search);

  const formattedDate = (date: Date | string) => {
    return new Date(date).toLocaleString("ru-RU", {
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    });
  };

  if (!result.ok || !result.data) {
    return (
      <div className="space-y-6">
        <AdminBreadcrumbs items={[{ title: "События" }]} />
        <div className="flex items-center justify-between">
          <h1 className="text-3xl font-bold">События</h1>
        </div>
        <div className="mx-auto max-w-4xl p-4 md:p-8">
          <div className="flex min-h-[40vh] flex-col items-center justify-center space-y-4 text-center">
            <div className="flex size-16 items-center justify-center rounded-full bg-destructive/10 text-destructive">
              <AlertCircle className="size-8" />
            </div>
            <h2 className="text-xl font-semibold">Не удалось загрузить события</h2>
            <p className="text-sm text-muted-foreground">
              {result.error ?? "Произошла непредвиденная ошибка"}
            </p>
          </div>
        </div>
      </div>
    );
  }

  const paginatedEvents: ListEventsDTO = result.data;
  const events: EventDTO[] = paginatedEvents.items;

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={[{ title: "События" }]} />
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">События</h1>
      </div>
      <EventSearch />
      <div className="rounded-md border bg-white">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Id</TableHead>
              <TableHead>AggregateId</TableHead>
              <TableHead>Тип</TableHead>
              <TableHead>Payload</TableHead>
              <TableHead>Создано</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {events.length === 0 ? (
              <TableRow>
                <TableCell colSpan={5} className="text-muted-foreground">
                  События отсутсвуют...
                </TableCell>
              </TableRow>
            ) : (
              events.map((event: EventDTO) => (
                <TableRow key={event.eventId}>
                  <TableCell className="font-medium">
                    <Link href={`/admin/events/${event.eventId}`} className="hover:underline">
                      {event.eventId}
                    </Link>
                  </TableCell>
                  <TableCell className="font-medium">{event.aggregateId}</TableCell>
                  <TableCell className="font-medium min-w-0 max-w-[300px] truncate">
                    {event.type}
                  </TableCell>
                  <TableCell className="font-medium min-w-0 max-w-[300px] truncate">
                    {event.payload}
                  </TableCell>
                  <TableCell className="font-medium">{formattedDate(event.createdAt)}</TableCell>
                </TableRow>
              ))
            )}
          </TableBody>
        </Table>
      </div>
      <Pagination
        currentPage={currentPage}
        totalPages={result.data.totalPages}
        baseUrl="/admin/events"
      />
    </div>
  );
}
