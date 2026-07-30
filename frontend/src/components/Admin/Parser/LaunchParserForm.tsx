"use client";

import { useRouter } from "next/navigation";
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { Rocket } from "lucide-react";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { Input } from "@/components/ui/input";
import { ParserShort } from "@/interfaces/parser.interface";
import { Controller, useForm } from "react-hook-form";
import React from "react";
import { Button } from "@/components/ui/button";
import { z } from "zod";
import { zodResolver } from "@hookform/resolvers/zod";
import { toast } from "sonner";
import { launchParserAction } from "@/actions/parser";

const schema = z.object({
  branchId: z.string(),
  ticketId: z.string(),
});

type LaunchParserFormData = z.infer<typeof schema>;

export interface LaunchParserFormProps {
  parser: ParserShort;
}

export default function LaunchParserForm({ parser }: LaunchParserFormProps) {
  const router = useRouter();

  const form = useForm<LaunchParserFormData>({
    mode: "onSubmit",
    resolver: zodResolver(schema),
    defaultValues: {
      branchId: "",
      ticketId: "",
    },
  });
  async function onSubmit(values: LaunchParserFormData) {
    const result = await launchParserAction({
      parserId: parser.id,
      branchId: values.branchId,
      ticketId: values.ticketId,
    });

    if (!result.ok) {
      form.setError("root", { type: "server", message: result.error });
      return;
    }

    toast.success("Парсер запущен!");
    form.reset();
    router.refresh();
  }
  const submitButton = (
    <Button
      type="submit"
      form="launch-parser-form"
      disabled={form.formState.isSubmitting}
      className="w-full cursor-pointer py-2"
    >
      {form.formState.isSubmitting ? "Загрузка..." : "Запустить!"}
    </Button>
  );

  return (
    <Card className="shadow-sm sm:max-w-[800px]">
      <CardHeader className="flex flex-row items-center gap-3 pb-3">
        <div className="flex size-9 items-center justify-center rounded-lg bg-muted text-muted-foreground">
          <Rocket className="size-5" />
        </div>
        <div className="space-y-0.5">
          <CardTitle className="text-lg font-semibold">Парсер</CardTitle>
          <CardDescription>Текущий созданный парсер</CardDescription>
        </div>
      </CardHeader>
      <CardContent>
        <form
          id="launch-parser-form"
          onSubmit={(e) => {
            void form.handleSubmit(onSubmit)(e);
          }}
          method="POST"
          className="grid gap-4 py-4"
        >
          <FieldGroup className="grid gap-4 sm:grid-cols-1">
            <Field>
              <FieldLabel htmlFor="host">Хост</FieldLabel>
              <Input
                id="host"
                value={parser.host}
                disabled
                className="cursor-not-allowed bg-muted/50"
              />
            </Field>
          </FieldGroup>
          <FieldGroup className="grid gap-4 sm:grid-cols-1">
            <Controller
              name="branchId"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="branchId">branchId</FieldLabel>
                  <Input
                    {...field}
                    id="branchId"
                    value={field.value}
                    placeholder="91600"
                    aria-invalid={fieldState.invalid}
                  />
                  {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
                </Field>
              )}
            />
          </FieldGroup>
          <FieldGroup className="grid gap-4 sm:grid-cols-1">
            <Controller
              name="ticketId"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="host">ticketId</FieldLabel>
                  <Input
                    {...field}
                    id="ticketId"
                    value={field.value}
                    placeholder="480a59713e02411f88ff4a80ec1a6b17"
                    aria-invalid={fieldState.invalid}
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
      </CardContent>
      <CardFooter>{submitButton}</CardFooter>
    </Card>
  );
}
