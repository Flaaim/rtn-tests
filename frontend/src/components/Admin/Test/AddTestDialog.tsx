"use client";

import { z } from "zod";
import React, { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { Controller, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { Button } from "@/components/ui/button";
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
import { Input } from "@/components/ui/input";
import { Textarea } from "@/components/ui/textarea";
import { toast } from "sonner";
import { fetchCoursesToSelectAction } from "@/actions/course";
import { CourseSelectOption } from "@/interfaces/course.interface";
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
import { addTestAction } from "@/actions/test";

const schema = z
  .object({
    name: z.string().trim().min(1, "Имя обязательно для заполнения"),
    cipher: z.string().trim().min(1, "Шифр обязателен для заполнения"),
    description: z.string().trim().min(1, "Описание обязательно для заполнения"),

    numberOfTickets: z.number().int().positive("Должно быть больше 0"),
    numberQuestionsInTicket: z.number().int().positive("Должно быть больше 0"),
    allowedMistakes: z.number().int().positive("Должно быть больше 0"),

    courseIds: z
      .array(z.string().uuid("Некорректный формат UUID"))
      .min(1, "Выберите хотя бы один курс"),
  })
  .refine((data) => data.allowedMistakes <= data.numberQuestionsInTicket, {
    message: "Количество разрешенных ошибок не может превышать количество вопросов в билете.",
    path: ["allowedMistakes"],
  });

type AddTestFormData = z.infer<typeof schema>;

export default function AddTestDialog() {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);

  const [courses, setCourses] = useState<CourseSelectOption[]>([]);
  const isCoursesLoaded = courses.length > 0;
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

  async function onSubmit(values: AddTestFormData) {
    const result = await addTestAction({
      name: values.name,
      cipher: values.cipher,
      description: values.description,
      numberOfTickets: values.numberOfTickets,
      numberQuestionsInTicket: values.numberQuestionsInTicket,
      allowedMistakes: values.allowedMistakes,
      courseIds: values.courseIds,
    });

    if (!result.ok) {
      form.setError("root", { type: "server", message: result.error });
      return;
    }

    toast.success("Тест успешно добавлен!");
    form.reset();
    setOpen(false);
    router.refresh();
  }

  const form = useForm<AddTestFormData>({
    mode: "onSubmit",
    resolver: zodResolver(schema),
    defaultValues: {
      name: "",
      cipher: "",
      description: "",
      numberOfTickets: "" as unknown as number,
      numberQuestionsInTicket: "" as unknown as number,
      allowedMistakes: "" as unknown as number,
      courseIds: [],
    },
  });

  const submitButton = (
    <Button
      type="submit"
      form="add-test-form"
      disabled={form.formState.isSubmitting}
      className="cursor-pointer py-2"
    >
      {form.formState.isSubmitting ? "Загрузка..." : "Создать тест"}
    </Button>
  );

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button />}>
        <Plus className="mr-2 h-4 w-4" /> Создать тест
      </DialogTrigger>
      <DialogContent className="sm:max-w-3xl">
        <DialogTitle>Создать новый тест</DialogTitle>
        <DialogDescription>Добавление нового теста.</DialogDescription>
        <form
          id="add-test-form"
          onSubmit={(e) => {
            void form.handleSubmit(onSubmit)(e);
          }}
          action=""
          method="POST"
          className="grid gap-4 py-4"
        >
          <FieldGroup>
            <Controller
              name="name"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="name">Название теста</FieldLabel>
                  <Input
                    {...field}
                    id="name"
                    value={field.value}
                    placeholder=""
                    aria-invalid={fieldState.invalid}
                  />
                  {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
                </Field>
              )}
            />
          </FieldGroup>
          <FieldGroup>
            <Controller
              name="cipher"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="cipher">Шифр теста</FieldLabel>
                  <Input
                    {...field}
                    id="cipher"
                    value={field.value}
                    placeholder=""
                    aria-invalid={fieldState.invalid}
                  />
                  {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
                </Field>
              )}
            />
          </FieldGroup>
          <FieldGroup>
            <Controller
              name="description"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid} className="w-full">
                  <Textarea
                    {...field}
                    id="description"
                    placeholder="Описание теста"
                    aria-invalid={fieldState.invalid}
                    className="h-[40vh] max-h-[100px]"
                    value={field.value}
                  ></Textarea>
                </Field>
              )}
            />
          </FieldGroup>

          <FieldGroup>
            <Controller
              name="numberOfTickets"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="numberOfTickets">Количество билетов</FieldLabel>
                  <Input
                    {...field}
                    id="numberOfTickets"
                    type="number"
                    value={field.value ?? ""}
                    placeholder=""
                    aria-invalid={fieldState.invalid}
                    onChange={(e) => {
                      const val = e.target.value;
                      field.onChange(val === "" ? undefined : Number(val));
                    }}
                  />
                  {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
                </Field>
              )}
            />
          </FieldGroup>

          <FieldGroup>
            <Controller
              name="numberQuestionsInTicket"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="numberQuestionsInTicket">
                    Количество вопросов в билете
                  </FieldLabel>
                  <Input
                    {...field}
                    id="numberQuestionsInTicket"
                    value={field.value ?? ""}
                    placeholder=""
                    aria-invalid={fieldState.invalid}
                    type="number"
                    onChange={(e) => {
                      const val = e.target.value;
                      field.onChange(val === "" ? undefined : Number(val));
                    }}
                  />
                  {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
                </Field>
              )}
            />
          </FieldGroup>

          <FieldGroup>
            <Controller
              name="allowedMistakes"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="allowedMistakes">Допустимое количество ошибок</FieldLabel>
                  <Input
                    {...field}
                    id="allowedMistakes"
                    value={field.value ?? ""}
                    placeholder=""
                    aria-invalid={fieldState.invalid}
                    type="number"
                    onChange={(e) => {
                      const val = e.target.value;
                      field.onChange(val === "" ? undefined : Number(val));
                    }}
                  />
                  {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
                </Field>
              )}
            />
          </FieldGroup>

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
