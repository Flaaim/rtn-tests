"use client";

import { TaskShort } from "@/interfaces/task.interface";
import { useState } from "react";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { Checkbox } from "@/components/ui/checkbox";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { FileCode, Trash } from "lucide-react";
import { Button } from "@/components/ui/button";
import { toast } from "sonner";
import { deleteTasksAction } from "@/actions/task";

interface TasksTableProps {
  tasks: TaskShort[];
}

export function TasksTable({ tasks }: TasksTableProps) {
  const [selectedIds, setSelectedIds] = useState<Set<string>>(new Set());
  const [isDeleting, setIsDeleting] = useState<boolean>(false);

  const isAllSelected = tasks.length > 0 && selectedIds.size === tasks.length;
  const router = useRouter();

  const toggleSelectAll = () => {
    if (isAllSelected) {
      setSelectedIds(new Set());
    } else {
      setSelectedIds(new Set(tasks.map((r) => r.id)));
    }
  };

  const toggleRecord = (id: string) => {
    setSelectedIds((prev) => {
      const next = new Set(prev);
      if (next.has(id)) {
        next.delete(id);
      } else {
        next.add(id);
      }
      return next;
    });
  };

  const handleDelete = async () => {
    if (selectedIds.size === 0) {
      toast.warning("Выберите хотя бы одну запись для удаления.");
      return;
    }

    setIsDeleting(true);
    try {
      const result = await deleteTasksAction(Array.from(selectedIds));
      if (!result.ok) {
        toast.error(result.error ?? "Не удалось удалить данные.");
        return;
      }
      toast.success("Данные парсинга успешно удалены!");
      router.refresh();
    } catch (error) {
      console.error("Error:", error);
    } finally {
      setIsDeleting(false);
    }
  };

  return (
    <div className="rounded-md border bg-white">
      <Card>
        <CardHeader className="flex flex-col gap-4 pb-4 sm:flex-row sm:items-center sm:justify-between">
          <div>
            <CardTitle className="flex items-center gap-2 text-lg font-semibold">
              <FileCode className="size-5 text-primary" />
              Список спарсенных данных
            </CardTitle>
            <CardDescription>Выделите данные для быстрого их удаления</CardDescription>
            <div className="flex items-center gap-3 mt-3">
              <span className="text-xs font-medium text-muted-foreground">
                Выбрано: {selectedIds.size} из {tasks.length}
              </span>
              <Button
                onClick={() => {
                  void handleDelete();
                }}
                disabled={selectedIds.size === 0 || isDeleting}
                className="flex min-w-[170px] cursor-pointer items-center gap-2"
              >
                {isDeleting ? (
                  <>
                    <svg className="size-4 animate-spin" fill="none" viewBox="0 0 24 24">
                      <circle
                        className="opacity-25"
                        cx="12"
                        cy="12"
                        r="10"
                        stroke="currentColor"
                        strokeWidth="4"
                      />
                      <path
                        className="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                      />
                    </svg>
                    Удаление...
                  </>
                ) : (
                  <>
                    <Trash className="size-4" />
                    Удалить
                  </>
                )}
              </Button>
            </div>
          </div>
        </CardHeader>
        <CardContent>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead className="w-12 text-center">
                  <Checkbox
                    checked={isAllSelected}
                    onCheckedChange={toggleSelectAll}
                    aria-label="Выбрать все записи"
                  />
                </TableHead>
                <TableHead>Номер задачи</TableHead>
                <TableHead>Номер парсера</TableHead>
                <TableHead>Создан</TableHead>
                <TableHead>Статус</TableHead>
                <TableHead>Ошибки</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {tasks.map((task: TaskShort) => {
                const isSelected = selectedIds.has(task.id);

                return (
                  <TableRow key={task.id}>
                    <TableCell className="text-center">
                      <Checkbox
                        checked={isSelected}
                        onCheckedChange={() => {
                          toggleRecord(task.id);
                        }}
                        aria-label={`Выбрать запись ${task.id}`}
                      />
                    </TableCell>
                    <TableCell className="font-medium">
                      <Link href={`/admin/parsers/tasks/${task.id}`} className="hover:underline">
                        {task.id}
                      </Link>
                    </TableCell>
                    <TableCell className="font-medium">{task.parserId}</TableCell>
                    <TableCell className="font-medium">
                      {new Date(task.created).toLocaleDateString("ru-RU")}
                    </TableCell>
                    <TableCell className="font-medium">{task.status}</TableCell>
                    <TableCell className="font-medium">{task.failedReason}</TableCell>
                  </TableRow>
                );
              })}
            </TableBody>
          </Table>
        </CardContent>
      </Card>
    </div>
  );
}
