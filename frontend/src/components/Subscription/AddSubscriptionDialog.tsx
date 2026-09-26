"use client";

import React, { useState } from "react";
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
import { Check, CreditCard, Zap } from "lucide-react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { createPaymentAction } from "@/actions/payment";
import { CreatePaymentPayload } from "@/interfaces/payment.interface";
import { toast } from "sonner";

const SUBSCRIPTION_PLANS: Record<
  string,
  { name: string; description: string; popular: boolean; payload: CreatePaymentPayload }
> = {
  basic_5: {
    name: "Базовая (5 дней)",
    description: "Идеально для быстрой подготовки по одной области аттестации",
    popular: false,
    payload: {
      plan: "basic",
      durationDays: 5,
      amount: "490.00",
    },
  },
  basic_10: {
    name: "Базовая (10 дней)",
    description: "Выбирайте если вам требуется подготовиться по 2-3 областям аттестации",
    popular: true,
    payload: {
      plan: "basic",
      durationDays: 10,
      amount: "790.00",
    },
  },
};
const FEATURES = [
  "Полный доступ ко всем тестам и билетам",
  "Подробный разбор ошибок",
  "Безлимитное количество попыток",
];

export default function AddSubscriptionDialog() {
  const [open, setOpen] = useState<boolean>(false);

  const [selectedPlanId, setSelectedPlanId] = useState<string>("basic_10");

  const [isLoading, setIsLoading] = useState<boolean>(false);

  const handleSubscribe = async () => {
    setIsLoading(true);
    const planToPay = SUBSCRIPTION_PLANS[selectedPlanId].payload;

    const result = await createPaymentAction(planToPay);
    if (result.ok && result.data) {
      window.location.href = result.data.confirmationUrl;
      return;
    }

    toast.error("Произошла ошибка при формировании платежа!");
    setIsLoading(false);
    setOpen(false);
  };

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger render={<Button className="bg-blue-600 hover:bg-blue-700" />}>
          <CreditCard className="mr-2 h-4 w-4" /> Оформить подписку
      </DialogTrigger>

      <DialogContent className="sm:max-w-[700px]">
        <DialogHeader>
          <DialogTitle className="text-2xl">Выбор подписки</DialogTitle>
          <DialogDescription>
            Выберите подходящий период доступа к материалам платформы.
          </DialogDescription>
        </DialogHeader>

        <div className="grid gap-6 py-4">
          <div className="grid md:grid-cols-2 gap-4">
            {Object.entries(SUBSCRIPTION_PLANS).map(([planId, plan]) => {
              const isSelected = selectedPlanId === planId;

              return (
                <Card
                  key={planId}
                  onClick={() => setSelectedPlanId(planId)}
                  className={`relative cursor-pointer transition-all border-2 ${
                    isSelected
                      ? "border-blue-600 bg-blue-50/30 dark:bg-blue-900/10 shadow-md"
                      : "border-border hover:border-blue-600/50"
                  }`}
                >
                  {plan.popular && (
                    <div className="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded-full flex items-center gap-1 shadow-sm">
                      <Zap className="h-3 w-3" /> Выгодно
                    </div>
                  )}
                  <CardHeader className="pb-3">
                    <CardTitle className="text-lg flex justify-between items-center">
                      {plan.name}
                      <div
                        className={`h-5 w-5 rounded-full border flex items-center justify-center ${
                          isSelected ? "bg-blue-600 border-blue-600" : "border-muted-foreground"
                        }`}
                      >
                        {isSelected && <Check className="h-3 w-3 text-white" />}
                      </div>
                    </CardTitle>
                    <CardDescription>{plan.description}</CardDescription>
                  </CardHeader>
                  <CardContent>
                    <p className="text-3xl font-bold">{plan.payload.amount} ₽</p>
                  </CardContent>
                </Card>
              );
            })}
          </div>

          {/* Блок с описанием преимуществ */}
          <div className="bg-muted/40 p-4 rounded-lg">
            <h4 className="font-medium mb-3 text-sm">Что входит в базовую подписку:</h4>
            <ul className="grid sm:grid-cols-2 gap-2">
              {FEATURES.map((feature, index) => (
                <li key={index} className="flex items-center text-sm text-muted-foreground">
                  <Check className="h-4 w-4 mr-2 text-green-500 shrink-0" />
                  {feature}
                </li>
              ))}
            </ul>
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" onClick={() => setOpen(false)} disabled={isLoading}>
            Отмена
          </Button>
          <Button
            onClick={handleSubscribe}
            disabled={isLoading}
            className="bg-blue-600 hover:bg-blue-700 w-full sm:w-auto"
          >
            {isLoading ? "Обработка..." : "Перейти к оплате"}
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  );
}
