"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import {
  SubscriptionAdminDTO,
  SubscriptionAdminListDTO,
  SubscriptionListDTO,
} from "@/interfaces/subscription.interface";
import { apiFetch } from "@/lib/apiClient";
import { API } from "@/app/api";
import { handleApiResponse } from "@/lib/handleApiResponse";

export async function fetchSubscriptionsPaginatedByUser(
  page: number,
  perPage: number
): Promise<ApiResponse<SubscriptionListDTO>> {
  try {
    const response = await apiFetch(API.subscription.getPaginatedByUser(page, perPage), {
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

export async function fetchSubscriptionsPaginated(
  page: number,
  perPage: number,
  search?: string
): Promise<ApiResponse<SubscriptionAdminListDTO>> {
  try {
    const response = await apiFetch(API.subscription.getPaginated(page, perPage, search), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<SubscriptionAdminListDTO>(response);
  } catch (error) {
    console.error("fetchSubscriptionsPaginatedByUser Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function fetchSubscriptionAction(
  subscriptionId: string
): Promise<ApiResponse<SubscriptionAdminDTO>> {
  try {
    const response = await apiFetch(API.subscription.get(subscriptionId), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<SubscriptionAdminDTO>(response);
  } catch (error) {
    console.error("fetchSubscriptionAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
