"use client";

import React, { useState, useMemo } from "react";
import { useRouter } from "next/navigation";
import { z } from "zod";
import { CategoryDTO } from "@/interfaces/category.interface";
import { toast } from "sonner";
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
import { Button } from "@/components/ui/button";
import { Controller, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
import { changeCategoryTestAction } from "@/actions/test";

const schema = z.object({
  categoryId: z.string().uuid("Некорректный формат UUID"),
});

interface ChangeCategoryTestDialogProps {
  testId: string;
  currentCategory: { id: string; name: string };
  categories: CategoryDTO[];
}

type ChangeCategoryFormData = z.infer<typeof schema>;

function flattenCategories(
  categories: CategoryDTO[],
  level = 0
): { id: string; name: string; isLeaf: boolean }[] {
  let result: { id: string; name: string; isLeaf: boolean }[] = [];

  for (const cat of categories) {
    const hasChildren = cat.children && cat.children.length > 0;
    const prefix = level > 0 ? "— ".repeat(level) : "";

    result.push({
      id: cat.id,
      name: `${prefix}${cat.name}`,
      isLeaf: !hasChildren,
    });

    if (hasChildren) {
      result = result.concat(flattenCategories(cat.children || [], level + 1));
    }
  }

  return result;
}

export default function ChangeCategoryTestDialog({
  testId,
  currentCategory,
  categories = [],
}: ChangeCategoryTestDialogProps) {
  const [open, setOpen] = useState<boolean>(false);
  const router = useRouter();

  // Оптимизация: вычисляем плоский список только при изменении категорий или текущей категории
  const availableCategories = useMemo(() => {
    const flat = flattenCategories(categories);
    return flat.filter((cat) => cat.id !== currentCategory.id);
  }, [categories, currentCategory.id]);

  const form = useForm<ChangeCategoryFormData>({
    mode: "onSubmit",
    resolver: zodResolver(schema),
    defaultValues: {
      categoryId: currentCategory.id,
    },
  });

  async function onSubmit(values: ChangeCategoryFormData) {
    const result = await changeCategoryTestAction({
      id: testId,
      categoryId: values.categoryId,
    });

    if (!result.ok) {
      form.setError("root", { type: "server", message: result.error });
      return;
    }

    toast.success("Категория успешно изменена.");
    form.reset(values);
    setOpen(false);
    router.refresh();
  }

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger className="inline-flex items-center hover:opacity-70 transition-opacity">
        <Pencil size={12} className="mr-2" />
      </DialogTrigger>
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Изменить категорию</DialogTitle>
          <DialogDescription>Изменение категории теста</DialogDescription>
        </DialogHeader>
        <form
          id="change-test-category-form"
          onSubmit={form.handleSubmit(onSubmit)} // Убрал лишнюю функцию-обертку, handleSubmit сам справится
          className="grid gap-4 py-4"
        >
          <FieldGroup>
            <Controller
              name="categoryId"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid}>
                  <FieldLabel htmlFor="categoryId">Категория</FieldLabel>
                  <select
                    {...field}
                    id="categoryId"
                    className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                  >
                    {/* Исправлено: value вместо defaultValue */}
                    <option value={currentCategory.id}>{currentCategory.name}</option>
                    {availableCategories.map((cat) => (
                      <option
                        key={cat.id}
                        value={cat.id}
                        disabled={!cat.isLeaf}
                        className={!cat.isLeaf ? "font-bold text-muted-foreground bg-muted/20" : ""}
                      >
                        {cat.name}
                      </option>
                    ))}
                  </select>
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
        <DialogFooter>
          <Button
            type="submit"
            form="change-test-category-form"
            disabled={form.formState.isSubmitting}
            className="w-full sm:w-auto"
          >
            {form.formState.isSubmitting ? "Сохранение..." : "Изменить"}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
