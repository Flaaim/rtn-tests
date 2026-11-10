"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import { ListRecipientsDTO } from "@/interfaces/recipient.interface";
import { apiFetch } from "@/lib/apiClient";
import { API } from "@/app/api";
import { handleApiResponse } from "@/lib/handleApiResponse";

export async function fetchRecipientsPagination(
  page: number,
  perPage: number
): Promise<ApiResponse<ListRecipientsDTO>> {
  try {
    const response = await apiFetch(API.recipient.getPaginated(page, perPage), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<ListRecipientsDTO>(response);
  } catch (error) {
    console.error("fetchRecipientsPagination Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
