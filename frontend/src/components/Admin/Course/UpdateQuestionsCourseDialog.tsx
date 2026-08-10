"use client";

import { z } from "zod";
import React, { useState } from "react";
import { useRouter } from "next/navigation";
import { Controller, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { Button } from "@/components/ui/button";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Upload } from "lucide-react";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Textarea } from "@/components/ui/textarea";
import { toast } from "sonner";
import { updateQuestionsCourseAction } from "@/actions/course";

const AnswerSchema = z.object({
  id: z.string().uuid().or(z.string()),
  text: z.string(),
  isCorrect: z.boolean(),
  answerImg: z.string().or(z.literal("")),
});

const QuestionSchema = z.object({
  id: z.string(),
  number: z.number().int().positive(), // целое положительное число
  text: z.string(),
  questionImg: z.string().url().or(z.literal("")), // валидный URL или пустая строка
  answers: z.array(AnswerSchema), // массив ответов
});

export const QuestionsArraySchema = z.array(QuestionSchema);

export type Question = z.infer<typeof QuestionSchema>;
export type Answer = z.infer<typeof AnswerSchema>;

const schema = z.object({
  rawJson: z
    .string()
    .min(1, "Поле не может быть пустым")
    .transform((str, ctx) => {
      try {
        return JSON.parse(str);
      } catch {
        ctx.addIssue({
          code: "custom",
          message: "Некорректный формат JSON (синтаксическая ошибка)",
        });
        return z.NEVER;
      }
    })
    .pipe(QuestionsArraySchema),
});

type UpdateQuestionsCourseData = z.infer<typeof schema>;
export interface UpdateQuestionsCourseDialogProps {
  id: string;
}
export default function UpdateQuestionsCourseDialog({ id }: UpdateQuestionsCourseDialogProps) {
  const [open, setOpen] = useState<boolean>(false);

  const router = useRouter();

  async function onSubmit(values: UpdateQuestionsCourseData) {
    const jsonString = JSON.stringify(values.rawJson);

    const result = await updateQuestionsCourseAction({
      rawJson: jsonString,
      id: id,
    });

    if (!result.ok) {
      form.setError("root", { type: "server", message: result.error });
      return;
    }

    toast.success("Вопросы курса успешно обновлены!");
    form.reset();
    setOpen(false);
    router.refresh();
  }
  const handleFormatJson = () => {
    const currentVal = form.getValues("rawJson");
    if (!currentVal) return;

    try {
      const parsed = JSON.parse(currentVal);
      const formatted = JSON.stringify(parsed, null, 2);
      form.setValue("rawJson", formatted, { shouldValidate: true });
    } catch {
      form.trigger("rawJson");
    }
  };
  const form = useForm({
    mode: "onSubmit",
    resolver: zodResolver(schema),
    defaultValues: {
      rawJson: "",
    },
  });

  const submitButton = (
    <Button
      type="submit"
      form="update-questions-course-form"
      disabled={form.formState.isSubmitting}
      className="w-full cursor-pointer py-2"
    >
      {form.formState.isSubmitting ? "Загрузка..." : "Обновить"}
    </Button>
  );

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button />}>
        <Upload className="mr-2 h-4 w-4" /> Обновить вопросы курса
      </DialogTrigger>
      <DialogContent className="sm:max-w-3xl">
        <DialogHeader>
          <DialogTitle>Обновить вопросы курса</DialogTitle>
          <DialogDescription>Все вопросы курса будут обновлены на новые</DialogDescription>
        </DialogHeader>
        <form
          id="update-questions-course-form"
          onSubmit={(e) => {
            void form.handleSubmit(onSubmit)(e);
          }}
          action=""
          method="PUT"
          className="grid gap-4 py-4"
        >
          <FieldGroup>
            <Controller
              name="rawJson"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid} className="w-full">
                  <div className="flex items-center justify-between mb-1.5">
                    <FieldLabel htmlFor="rawJson" className="font-medium text-sm block">
                      Json строка с вопросами курса.
                    </FieldLabel>
                    <button
                      type="button"
                      onClick={handleFormatJson}
                      className="text-xs text-muted-foreground hover:text-foreground transition-colors underline underline-offset-2"
                    >
                      Форматировать JSON
                    </button>
                  </div>

                  <Textarea
                    {...field}
                    id="rawJson"
                    placeholder='[{"id": "...", "number": 1, "text": "..."}]'
                    aria-invalid={fieldState.invalid}
                    onPaste={() => {
                      setTimeout(() => {
                        handleFormatJson();
                      }, 50);
                    }}
                    className="h-[60vh] max-h-[600px] overflow-y-auto font-mono text-xs leading-relaxed resize-y p-3 w-full"
                  ></Textarea>
                  {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
                </Field>
              )}
            ></Controller>
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
