"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import { PaginatedTests } from "@/interfaces/test.interface";
import { apiFetch } from "@/lib/apiClient";
import { API } from "@/app/api";
import { handleApiResponse } from "@/lib/handleApiResponse";

export async function fetchTestsPaginatedAction(
  page: number,
  perPage: number,
  search?: string
): Promise<ApiResponse<PaginatedTests>> {
  try {
    const response = await apiFetch(API.test.getPaginated(page, perPage, search), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });

    return handleApiResponse<PaginatedTests>(response);
  } catch (error) {
    console.error("fetchTestsPaginatedAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
