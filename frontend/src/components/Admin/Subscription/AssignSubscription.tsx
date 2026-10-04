"use client";

import { z } from "zod";
import { ProfileSelectOption } from "@/interfaces/subscription.interface";
import React, { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { assignSubscriptionAction, fetchProfilesToSelectAction } from "@/actions/subscriptions";
import { toast } from "sonner";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogTitle,
  DialogTrigger,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import { Check, ChevronsUpDown, Plus, CalendarIcon } from "lucide-react";
import { Controller, useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { Field, FieldError, FieldGroup, FieldLabel } from "@/components/ui/field";
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
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { format } from "date-fns";
import { ru } from "date-fns/locale";
import { Calendar } from "@/components/ui/calendar";

const schema = z.object({
  userId: z.string().uuid(),
  plan: z.enum(["basic", "trial"]),
  period: z.object({
    from: z.date(),
    to: z.date(),
  }),
});

type AssignSubscriptionFormData = z.infer<typeof schema>;

export default function AssignSubscription() {
  const [open, setOpen] = useState<boolean>(false);
  const [loading, setLoading] = useState<boolean>(false);
  const [profiles, setProfiles] = useState<ProfileSelectOption[]>([]);

  const isProfilesLoaded = profiles.length > 0;
  const [openPopover, setOpenPopover] = useState<boolean>(false);

  const router = useRouter();

  useEffect(() => {
    if (open) {
      const initData = async () => {
        setLoading(true);
        try {
          const response = await fetchProfilesToSelectAction();
          if (response.ok && response.data) {
            setProfiles(response.data);
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

  async function onSubmit(values: AssignSubscriptionFormData) {
    const result = await assignSubscriptionAction({
      userId: values.userId,
      plan: values.plan,
      periodStart: format(values.period.from, "yyyy-MM-dd"),
      periodEnd: format(values.period.to, "yyyy-MM-dd"),
    });
    if (!result.ok) {
      form.setError("root", { type: "server", message: result.error });
      return;
    }

    toast.success("Подписка добавлена!");
    form.reset();
    setOpen(false);
    router.refresh();
  }

  const form = useForm<AssignSubscriptionFormData>({
    mode: "onSubmit",
    resolver: zodResolver(schema),
    defaultValues: {
      userId: "",
      plan: undefined as unknown as "basic" | "trial",
      period: undefined,
    },
  });

  const submitButton = (
    <Button
      type="submit"
      form="assign-subscription-form"
      disabled={form.formState.isSubmitting}
      className="cursor-pointer py-2"
    >
      {form.formState.isSubmitting ? "Загрузка..." : "Добавить"}
    </Button>
  );

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button />}>
        <Plus className="mr-2 h-4 w-4" /> Добавить подписку
      </DialogTrigger>
      <DialogContent className="sm:max-w-3xl">
        <DialogTitle>Добавить подписку пользователю</DialogTitle>
        <DialogDescription>Добавление подписки для пользователя сайта</DialogDescription>
        <form
          id="assign-subscription-form"
          onSubmit={(e) => {
            void form.handleSubmit(onSubmit)(e);
          }}
          action=""
          method="POST"
          className="grid gap-4 py-4"
        >
          <FieldGroup>
            <Controller
              name="userId"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid} className="flex flex-col gap-2">
                  <FieldLabel htmlFor="userId">Пользователь</FieldLabel>
                  <Popover open={openPopover} onOpenChange={setOpenPopover}>
                    <PopoverTrigger
                      render={
                        <Button
                          id="userId"
                          variant="outline"
                          role="combobox"
                          aria-expanded={openPopover}
                          className={cn(
                            "w-full justify-between",
                            !field.value?.length && "text-muted-foreground"
                          )}
                          disabled={loading}
                        >
                          {field.value
                            ? `Выбран ${profiles.find((p) => p.id === field.value)?.email || field.value}`
                            : `Выберите пользователя`}
                          <ChevronsUpDown className="ml-2 h-4 w-4 shrink-0 opacity-50" />
                        </Button>
                      }
                    ></PopoverTrigger>
                    <PopoverContent className="w-[400px] p-0" align="start">
                      <Command>
                        <CommandInput placeholder="Поиск пользователя..." />
                        <CommandList>
                          <CommandEmpty>
                            {!isProfilesLoaded ? "Профили не загружены..." : "Профили не найдены."}
                          </CommandEmpty>
                          <CommandGroup>
                            {profiles.map((profile: ProfileSelectOption) => (
                              <CommandItem
                                key={profile.id}
                                value={profile.email}
                                onSelect={() => {
                                  field.onChange(profile.id);
                                  setOpenPopover(false);
                                }}
                              >
                                <Check
                                  className={cn(
                                    "mr-2 h-4 w-4",
                                    field.value === profile.id ? "opacity-100" : "opacity-0"
                                  )}
                                />
                                {profile.email}
                              </CommandItem>
                            ))}
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
          <FieldGroup>
            <Controller
              name="plan"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid} className="flex flex-col gap-2">
                  <FieldLabel htmlFor="plan">План подписки</FieldLabel>
                  <Select onValueChange={field.onChange} defaultValue={field.value}>
                    <SelectTrigger id="plan" className="w-full">
                      <SelectValue placeholder="Выберите план..." />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="basic">Базовый (Basic)</SelectItem>
                      <SelectItem value="trial">Пробный (Trial)</SelectItem>
                    </SelectContent>
                  </Select>
                  {fieldState.invalid && <FieldError errors={[fieldState.error]} />}
                </Field>
              )}
            />
          </FieldGroup>

          <FieldGroup>
            <Controller
              name="period"
              control={form.control}
              render={({ field, fieldState }) => (
                <Field data-invalid={fieldState.invalid} className="flex flex-col gap-2">
                  <FieldLabel htmlFor="period">Период подписки</FieldLabel>
                  <Popover>
                    <PopoverTrigger
                      render={
                        <Button
                          variant="outline"
                          id="date-picker-range"
                          className={cn(
                            "w-full justify-start text-left font-normal",
                            !field.value && "text-muted-foreground"
                          )}
                        >
                          <CalendarIcon data-icon="inline-start" />
                          {field.value?.from ? (
                            field.value?.to ? (
                              <>
                                {format(field.value.from, "dd.MM.yyyy")} -{" "}
                                {format(field.value.to, "dd.MM.yyyy")}
                              </>
                            ) : (
                              format(field.value.from, "dd.MM.yyyy")
                            )
                          ) : (
                            <span>Выберите период</span>
                          )}
                        </Button>
                      }
                    />
                    {field.value
                      ? `Выбран ${format(field.value.from, "dd.MM.yyyy")} -
                          ${format(field.value.to, "dd.MM.yyyy")}`
                      : `Выберите период`}
                    <PopoverContent className="w-auto p-0" align="start">
                      <Calendar
                        mode="range"
                        defaultMonth={field.value?.from || new Date()}
                        selected={field.value}
                        onSelect={field.onChange}
                        numberOfMonths={2}
                        locale={ru}
                      />
                    </PopoverContent>
                  </Popover>
                  {(fieldState.error || form.formState.errors.period?.from) && (
                    <FieldError
                      errors={[
                        { message: fieldState.error?.message || "Укажите корректный период" },
                      ]}
                    />
                  )}
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
