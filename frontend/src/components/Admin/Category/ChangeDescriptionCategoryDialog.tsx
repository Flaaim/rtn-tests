"use client";

import React, { useState } from "react";
import { useRouter } from "next/navigation";
import { toast } from "sonner";
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
import { Pencil } from "lucide-react";
import { Field, FieldError, FieldGroup } from "@/components/ui/field";
import { Textarea } from "@/components/ui/textarea";
import { changeDescriptionCategoryAction } from "@/actions/category";
import { z } from "zod";

const schema = z.object({
  description: z
    .string()
    .trim()
    .min(1, "Описание обязательно для заполнения")
    .max(255, "Описание слишком длинное!"),
});

type ChangeDescriptionFormData = z.infer<typeof schema>;

interface ChangeDescriptionCategoryProps {
  id: string;
  description: string;
}

export default function ChangeDescriptionCategoryDialog({
  id,
  description,
}: ChangeDescriptionCategoryProps) {
  const [open, setOpen] = useState<boolean>(false);

  const router = useRouter();

  async function onSubmit(values: ChangeDescriptionFormData) {
    const result = await changeDescriptionCategoryAction({
      id: id,
      description: values.description,
    });

    if (!result.ok) {
      form.setError("root", { type: "server", message: result.error });
      return;
    }

    toast.success("Описание категории успешно изменено.");
    form.reset(values);
    setOpen(false);
    router.refresh();
  }

  const form = useForm({
    mode: "onSubmit",
    resolver: zodResolver(schema),
    defaultValues: {
      description: description,
    },
  });
  const submitButton = (
    <Button
      type="submit"
      form="change-description-category-form"
      disabled={form.formState.isSubmitting}
      className="cursor-pointer py-2"
    >
      {form.formState.isSubmitting ? "Загрузка..." : "Изменить"}
    </Button>
  );

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button />}>
        <Pencil className=" h-4 w-4" />
      </DialogTrigger>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Изменить описание категории</DialogTitle>
          <DialogDescription>Изменение описания категории</DialogDescription>
        </DialogHeader>
        <form
          id="change-description-category-form"
          onSubmit={(e) => {
            void form.handleSubmit(onSubmit)(e);
          }}
          action=""
          method="PUT"
          className="grid gap-4 py-4"
        >
          <FieldGroup>
            <Controller
              name="description"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid} className="w-full">
                  <Textarea
                    {...field}
                    id="description"
                    placeholder="Описание категории"
                    aria-invalid={fieldState.invalid}
                    className="h-[40vh] max-h-[100px]"
                    value={field.value}
                  ></Textarea>
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
