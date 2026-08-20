"use client";

import React, { useState } from "react";
import { useRouter } from "next/navigation";
import { z } from "zod";
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
import { Settings } from "lucide-react";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Controller, useForm } from "react-hook-form";
import { Input } from "@/components/ui/input";
import { zodResolver } from "@hookform/resolvers/zod";
import { updateSettingsTestAction } from "@/actions/test";
import { toast } from "sonner";
import { Settings as SettingsInterface } from "@/interfaces/test.interface";

const schema = z
  .object({
    numberOfTickets: z.number().int().positive("Должно быть больше 0"),
    numberQuestionsInTicket: z.number().int().positive("Должно быть больше 0"),
    allowedMistakes: z.number().int().positive("Должно быть больше 0"),
  })
  .refine((data) => data.allowedMistakes <= data.numberQuestionsInTicket, {
    message: "Количество разрешенных ошибок не может превышать количество вопросов в билете.",
    path: ["allowedMistakes"],
  });

type UpdateSettingsTestFormData = z.infer<typeof schema>;

interface UpdateSettingsTestDialogProps {
  id: string;
  settings: SettingsInterface;
}

export default function UpdateSettingsTestDialog({ id, settings }: UpdateSettingsTestDialogProps) {
  const [open, setOpen] = useState<boolean>(false);

  const router = useRouter();
  async function onSubmit(values: UpdateSettingsTestFormData) {
    const result = await updateSettingsTestAction({
      id: id,
      numberOfTickets: values.numberOfTickets,
      numberQuestionsInTicket: values.numberQuestionsInTicket,
      allowedMistakes: values.allowedMistakes,
    });

    if (!result.ok) {
      form.setError("root", { type: "server", message: result.error });
      return;
    }

    toast.success("Настройки теста успешно изменены.");
    form.reset(values);
    setOpen(false);
    router.refresh();
  }

  const form = useForm({
    mode: "onSubmit",
    resolver: zodResolver(schema),
    defaultValues: {
      numberOfTickets: settings.numberOfTickets,
      numberQuestionsInTicket: settings.numberQuestionsInTicket,
      allowedMistakes: settings.allowedMistakes,
    },
  });

  const submitButton = (
    <Button
      type="submit"
      form="update-settings-test-form"
      disabled={form.formState.isSubmitting}
      className="w-full cursor-pointer py-2"
    >
      {form.formState.isSubmitting ? "Загрузка..." : "Изменить"}
    </Button>
  );

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button />}>
        <Settings className="mr-2 h-4 w-4" />
      </DialogTrigger>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Изменить настройки теста</DialogTitle>
          <DialogDescription>Изменение настроек теста</DialogDescription>
        </DialogHeader>
        <form
          id="update-settings-test-form"
          onSubmit={(e) => {
            void form.handleSubmit(onSubmit)(e);
          }}
          action=""
          method="PUT"
          className="grid gap-4 py-4"
        >
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
