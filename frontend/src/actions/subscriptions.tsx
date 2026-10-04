"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import {
  AssignSubscriptionPayload,
  ProfileSelectOption,
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

export async function fetchProfilesToSelectAction(): Promise<ApiResponse<ProfileSelectOption[]>> {
  try {
    const response = await apiFetch(API.profile.lookup(), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });

    return handleApiResponse<ProfileSelectOption[]>(response);
  } catch (error) {
    console.error("fetchCoursesToSelectAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function assignSubscriptionAction(
  payload: AssignSubscriptionPayload
): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.subscription.assign(), {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        userId: payload.userId,
        plan: payload.plan,
        periodStart: payload.periodStart,
        periodEnd: payload.periodEnd,
      }),
    });
    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("ActivateSubscriptionAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function removeSubscriptionAction(id: string): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.subscription.remove(id), {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });

    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("removeSubscriptionAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
