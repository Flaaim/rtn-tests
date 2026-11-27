"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import { ListRecipientsDTO, RecipientFull } from "@/interfaces/recipient.interface";
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
export async function removeRecipientAction(id: string): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.recipient.remove(id), {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("removeRecipientAction error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function fetchRecipientAction(id: string): Promise<ApiResponse<RecipientFull>> {
  try {
    const response = await apiFetch(API.recipient.get(id), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<RecipientFull>(response);
  } catch (error) {
    console.error("fetchRecipient error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
