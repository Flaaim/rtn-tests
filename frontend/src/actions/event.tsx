"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import { ListEventsDTO } from "@/interfaces/event.interface";
import { apiFetch } from "@/lib/apiClient";
import { API } from "@/app/api";
import { handleApiResponse } from "@/lib/handleApiResponse";

export async function fetchEventsPagination(
  page: number,
  perPage: number,
  search?: string
): Promise<ApiResponse<ListEventsDTO>> {
  try {
    const response = await apiFetch(API.event.getPaginated(page, perPage, search), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<ListEventsDTO>(response);
  } catch (error) {
    console.error("fetchEventsPagination Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function fetchEventHistory(aggregateId: string): Promise<ApiResponse<ListEventsDTO>> {
  try {
    const response = await apiFetch(API.event.getHistory(aggregateId), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<ListEventsDTO>(response);
  } catch (error) {
    console.error("fetchEventHistory Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
