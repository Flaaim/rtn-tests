"use client";

import React, { useState } from "react";
import { useRouter } from "next/navigation";
import { Controller, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { z } from "zod";
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
import { Save } from "lucide-react";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { renderCourseAction } from "@/actions/course";
import { toast } from "sonner";

const schema = z.object({
  amount: z.string().regex(/^\d+\.\d{2}$/, {
    message: "Сумма должна быть в формате 0.00 (две цифры после точки)",
  }),
});

type RenderCourseFormData = z.infer<typeof schema>;

interface RenderCourseDialogProps {
  id: string;
}

export default function RenderCourseDialog({ id }: RenderCourseDialogProps) {
  const [open, setOpen] = useState<boolean>(false);

  const router = useRouter();

  async function onSubmit(values: RenderCourseFormData) {
    const result = await renderCourseAction({
      id: id,
      amount: values.amount,
    });

    if (!result.ok) {
      form.setError("root", { type: "server", message: result.error });
      return;
    }

    toast.success("Курс успешно сохранен в файл!");
    form.reset(values);
    setOpen(false);
    router.refresh();
  }
  const form = useForm({
    mode: "onSubmit",
    resolver: zodResolver(schema),
    defaultValues: {
      amount: "",
    },
  });

  const submitButton = (
    <Button
      type="submit"
      form="render-course-form"
      disabled={form.formState.isSubmitting}
      className="w-full cursor-pointer py-2"
    >
      {form.formState.isSubmitting ? "Загрузка..." : "Рендер"}
    </Button>
  );

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button />}>
        <Save className="mr-2 h-4 w-4" /> Сохранить в файл
      </DialogTrigger>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Сохранить курс в PDF файл</DialogTitle>
          <DialogDescription>Сохранение курса в PDF файл на сервере.</DialogDescription>
        </DialogHeader>
        <form
          id="render-course-form"
          onSubmit={(e) => {
            void form.handleSubmit(onSubmit)(e);
          }}
          action=""
          method="POST"
          className="grid gap-4 py-4"
        >
          <FieldGroup>
            <Controller
              name="amount"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="amount">Стоимость курса</FieldLabel>
                  <Input
                    {...field}
                    id="amount"
                    value={field.value}
                    placeholder=""
                    aria-invalid={fieldState.invalid}
                  />
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
