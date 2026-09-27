"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import { SubscriptionListDTO } from "@/interfaces/subscription.interface";
import { apiFetch } from "@/lib/apiClient";
import { API } from "@/app/api";
import { handleApiResponse } from "@/lib/handleApiResponse";

export default async function fetchSubscriptionsPaginatedByUser(
  page: number,
  perPage: number
): Promise<ApiResponse<SubscriptionListDTO>> {
  try {
    const response = await apiFetch(API.subscription.getPaginated(page, perPage), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<SubscriptionListDTO>(response);
  } catch (error) {
    console.error("fetchSubscriptionsPaginatedByUser Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
