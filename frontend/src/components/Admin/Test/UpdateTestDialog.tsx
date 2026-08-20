"use client";

import React, { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { z } from "zod";
import { CourseSelectOption } from "@/interfaces/course.interface";
import { fetchCoursesToSelectAction } from "@/actions/course";
import { toast } from "sonner";
import { Button } from "@/components/ui/button";
import { Controller, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Check, ChevronsUpDown, Plus } from "lucide-react";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { cn } from "@/lib/utils";
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from "@/components/ui/command";
import { updateTestAction } from "@/actions/test";

const schema = z.object({
  courseIds: z
    .array(z.string().uuid("Некорректный формат UUID"))
    .min(1, "Выберите хотя бы один курс"),
});

interface UpdateTestDialogProps {
  id: string;
  currentCourses: CourseSelectOption[];
}

type UpdateTestFormData = z.infer<typeof schema>;

export default function UpdateTestDialog({ id, currentCourses }: UpdateTestDialogProps) {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);

  const [courses, setCourses] = useState<CourseSelectOption[]>(currentCourses);
  const isCoursesLoaded = currentCourses.length > 0;

  const router = useRouter();

  useEffect(() => {
    if (open) {
      const initData = async () => {
        setLoading(true);
        try {
          const response = await fetchCoursesToSelectAction();
          if (response.ok && response.data) {
            setCourses(response.data);
          }
        } catch (error) {
          const err = error instanceof Error ? error : new Error("Ошибка при получении данных");
          toast.error(err.message);
        } finally {
          setLoading(false);
        }
      };
      void initData();
    }
  }, [open]);

  async function onSubmit(values: UpdateTestFormData) {
    const result = await updateTestAction({
      id: id,
      courseIds: values.courseIds,
    });

    if (!result.ok) {
      form.setError("root", { type: "server", message: result.error });
      return;
    }

    toast.success("Вопросы теста успешно обновлены!");
    form.reset();
    setOpen(false);
    router.refresh();
  }

  const form = useForm<UpdateTestFormData>({
    mode: "onSubmit",
    resolver: zodResolver(schema),
    defaultValues: {
      courseIds: currentCourses.map((course) => course.id),
    },
  });

  const submitButton = (
    <Button
      type="submit"
      form="update-test-form"
      disabled={form.formState.isSubmitting}
      className="cursor-pointer py-2"
    >
      {form.formState.isSubmitting ? "Загрузка..." : "Обновить"}
    </Button>
  );

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button />}>
        <Plus className="mr-2 h-4 w-4" /> Обновить
      </DialogTrigger>
      <DialogContent className="sm:max-w-3xl">
        <DialogTitle>Обновить вопросы теста</DialogTitle>
        <DialogDescription>Полное обновление вопросов теста.</DialogDescription>
        <form
          id="update-test-form"
          onSubmit={(e) => {
            void form.handleSubmit(onSubmit)(e);
          }}
          action=""
          method="PUT"
          className="grid gap-4 py-4"
        >
          <FieldGroup>
            <Controller
              name="courseIds"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid} className="flex flex-col gap-2">
                  <FieldLabel htmlFor="courseIds">Курсы</FieldLabel>
                  <Popover>
                    <PopoverTrigger
                      render={
                        <Button
                          id="courseIds"
                          variant="outline"
                          role="combobox"
                          className={cn(
                            "w-full justify-between",
                            !field.value?.length && "text-muted-foreground"
                          )}
                          disabled={loading}
                        >
                          {field.value?.length > 0
                            ? `Выбрано курсов: ${field.value.length}`
                            : "Выберите курсы..."}
                          <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                        </Button>
                      }
                    ></PopoverTrigger>
                    <PopoverContent className="w-[400px] p-0" align="start">
                      <Command>
                        <CommandInput placeholder="Поиск курса..." />
                        <CommandList>
                          <CommandEmpty>
                            {!isCoursesLoaded ? "Курсы не загружены..." : "Курсы не найдены."}
                          </CommandEmpty>
                          <CommandGroup>
                            {courses.map((course) => {
                              // Проверяем, выбран ли текущий курс
                              const isSelected = field.value?.includes(course.id);
                              return (
                                <CommandItem
                                  key={course.id}
                                  value={course.name} // Поиск будет работать по названию курса
                                  onSelect={() => {
                                    // Логика переключения (toggle)
                                    if (isSelected) {
                                      // Если уже выбран — удаляем из массива
                                      field.onChange(field.value.filter((id) => id !== course.id));
                                    } else {
                                      // Если не выбран — добавляем в массив
                                      field.onChange([...(field.value || []), course.id]);
                                    }
                                  }}
                                >
                                  <Check
                                    className={cn(
                                      "mr-2 h-4 w-4",
                                      isSelected ? "opacity-100" : "opacity-0"
                                    )}
                                  />
                                  {course.name}
                                </CommandItem>
                              );
                            })}
                          </CommandGroup>
                        </CommandList>
                      </Command>
                    </PopoverContent>
                  </Popover>
                  {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
                </Field>
              )}
            />
          </FieldGroup>
        </form>
        {form.formState.errors.root && (
          <div className="rounded-md bg-destructive/10 p-2 text-center text-sm font-medium text-destructive">
            {form.formState.errors.root.message}
          </div>
        )}
        <DialogFooter>{submitButton}</DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
