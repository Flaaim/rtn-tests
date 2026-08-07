"use client";

import React, { useState } from "react";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Plus } from "lucide-react";
import { Controller, useForm } from "react-hook-form";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { useRouter } from "next/navigation";
import { toast } from "sonner";
import { addCourseAction } from "@/actions/course";

const schema = z.object({
  name: z.string(),
  cipher: z.string(),
});

interface AddCourseDialogProps {
  draft: string;
}

type AddCourseFormData = z.infer<typeof schema>;

export default function AddCourseDialog({ draft }: AddCourseDialogProps) {
  const [open, setOpen] = useState<boolean>(false);

  const router = useRouter();

  async function onSubmit(values: AddCourseFormData) {
    const result = await addCourseAction({
      name: values.name,
      cipher: values.cipher,
      draft: draft,
    });

    if (!result.ok) {
      form.setError("root", { type: "server", message: result.error });
      return;
    }

    toast.success("Курс успешно добавлен!");
    form.reset();
    setOpen(false);
    router.refresh();
  }

  const form = useForm<AddCourseFormData>({
    mode: "onSubmit",
    resolver: zodResolver(schema),
    defaultValues: {
      name: "",
      cipher: "",
    },
  });

  const submitButton = (
    <Button
      type="submit"
      form="add-course-form"
      disabled={form.formState.isSubmitting}
      className="w-full cursor-pointer py-2"
    >
      {form.formState.isSubmitting ? "Загрузка..." : "Добавить парсер"}
    </Button>
  );

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button />}>
        <Plus className="mr-2 h-4 w-4" /> Создать курс
      </DialogTrigger>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Создать новый курс</DialogTitle>
          <DialogDescription>Добавление нового курса.</DialogDescription>
        </DialogHeader>
        <form
          id="add-course-form"
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
                  <FieldLabel htmlFor="name">Название курса</FieldLabel>
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
            ></Controller>
          </FieldGroup>
          <FieldGroup>
            <Controller
              name="cipher"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="cipher">Шифр курса</FieldLabel>
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
