"use client";

import { z } from "zod";
import React, { useState } from "react";
import { useRouter } from "next/navigation";
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
import { Pencil } from "lucide-react";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Controller, useForm } from "react-hook-form";
import { Input } from "@/components/ui/input";
import { zodResolver } from "@hookform/resolvers/zod";
import { toast } from "sonner";
import { changeCipherTestAction } from "@/actions/test";

const schema = z.object({
  cipher: z.string(),
});

type ChangeCipherFormData = z.infer<typeof schema>;

interface ChangeCipherTestDialogProps {
  id: string;
  cipher: string;
}

export function ChangeCipherTestDialog({ id, cipher }: ChangeCipherTestDialogProps) {
  const [open, setOpen] = useState<boolean>(false);

  const router = useRouter();

  async function onSubmit(values: ChangeCipherFormData) {
    const result = await changeCipherTestAction({
      id: id,
      cipher: values.cipher,
    });

    if (!result.ok) {
      form.setError("root", { type: "server", message: result.error });
      return;
    }

    toast.success("Шифр успешно изменен.");
    form.reset(values);
    setOpen(false);
    router.refresh();
  }
  const form = useForm({
    mode: "onSubmit",
    resolver: zodResolver(schema),
    defaultValues: {
      cipher: cipher,
    },
  });
  const submitButton = (
    <Button
      type="submit"
      form="change-cipher-test-form"
      disabled={form.formState.isSubmitting}
      className="w-full cursor-pointer py-2"
    >
      {form.formState.isSubmitting ? "Загрузка..." : "Изменить"}
    </Button>
  );
  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger>
        <Pencil size={12} className="mr-1 " />
      </DialogTrigger>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Изменить шифр</DialogTitle>
          <DialogDescription>Изменение шифра теста</DialogDescription>
        </DialogHeader>
        <form
          id="change-cipher-test-form"
          onSubmit={(e) => {
            void form.handleSubmit(onSubmit)(e);
          }}
          action=""
          method="PUT"
          className="grid gap-4 py-4"
        >
          <FieldGroup>
            <Controller
              name="cipher"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="name">Шифр</FieldLabel>
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
