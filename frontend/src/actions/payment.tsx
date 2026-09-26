"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import { apiFetch } from "@/lib/apiClient";
import { API } from "@/app/api";
import { CreatePaymentPayload, CreatePaymentResponse } from "@/interfaces/payment.interface";
import { handleApiResponse } from "@/lib/handleApiResponse";

export async function createPaymentAction(
  payload: CreatePaymentPayload
): Promise<ApiResponse<CreatePaymentResponse>> {
  try {
    const response = await apiFetch(API.payment.create(), {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        plan: payload.plan,
        amount: payload.amount,
        durationDays: payload.durationDays,
        returnUrl: String(process.env.YOOKASSA_RETURN_URL),
      }),
    });
    return handleApiResponse<CreatePaymentResponse>(response);
  } catch (error) {
    console.error("removeProfileAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
