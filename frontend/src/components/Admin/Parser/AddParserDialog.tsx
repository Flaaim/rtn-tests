"use client";

import React, { useState } from "react";

import { Plus } from "lucide-react";
import { zodResolver } from "@hookform/resolvers/zod";
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
import { useRouter } from "next/navigation";
import { Controller, useForm } from "react-hook-form";
import { z } from "zod";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { toast } from "sonner";
import { addParserAction } from "@/actions/parser";

const schema = z.object({
  host: z.string().url({ message: "Invalid URL provided" }),
  login: z.string(),
  password: z.string(),
});

type AddParserFormData = z.infer<typeof schema>;

export default function AddParserDialog() {
  const [open, setOpen] = useState<boolean>(false);

  const router = useRouter();
  async function onSubmit(values: AddParserFormData) {
    const result = await addParserAction({
      host: values.host,
      login: values.login,
      password: values.password,
    });

    if (!result.ok) {
      form.setError("root", { type: "server", message: result.error });
      return;
    }

    toast.success("Парсер успешно добавлен!");
    form.reset();
    setOpen(false);
    router.refresh();
  }

  const form = useForm<AddParserFormData>({
    mode: "onBlur",
    resolver: zodResolver(schema),
    defaultValues: {
      host: "",
      login: "UGwQiLx3nFLYTdKAhCuX5g==",
      password: "pb2Ov1bKcE3IU7gJuB8cvg==",
    },
  });

  const submitButton = (
    <Button
      type="submit"
      form="add-parser-form"
      disabled={form.formState.isSubmitting}
      className="w-full cursor-pointer py-2"
    >
      {form.formState.isSubmitting ? "Загрузка..." : "Добавить парсер"}
    </Button>
  );

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button />}>
        <Plus className="mr-2 h-4 w-4" /> Добавить
      </DialogTrigger>
      <DialogContent className="sm:max-w-[800px]">
        <DialogHeader>
          <DialogTitle>Новый парсер</DialogTitle>
          <DialogDescription>Добавление нового парсера.</DialogDescription>
        </DialogHeader>
        <form
          id="add-parser-form"
          onSubmit={(e) => {
            void form.handleSubmit(onSubmit)(e);
          }}
          method="POST"
          className="grid gap-4 py-4"
        >
          <FieldGroup>
            <Controller
              name="host"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="host">Хост для парсинга</FieldLabel>
                  <Input
                    {...field}
                    id="host"
                    value={field.value}
                    placeholder="https://olimpoks.hydroschool.ru"
                    aria-invalid={fieldState.invalid}
                  />
                  {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
                </Field>
              )}
            ></Controller>
          </FieldGroup>

          <FieldGroup>
            <Controller
              name="login"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="login">Логин</FieldLabel>
                  <Input
                    {...field}
                    id="login"
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
              name="password"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="password">Пароль</FieldLabel>
                  <Input
                    {...field}
                    id="password"
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
