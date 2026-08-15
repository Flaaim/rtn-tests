"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import { AddTestPayload, PaginatedTests } from "@/interfaces/test.interface";
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

export async function addTestAction(payload: AddTestPayload): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.test.add(), {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        name: payload.name,
        cipher: payload.cipher,
        description: payload.description,
        numberOfTickets: payload.numberOfTickets,
        numberQuestionsInTicket: payload.numberQuestionsInTicket,
        allowedMistakes: payload.allowedMistakes,
        courseIds: payload.courseIds,
      }),
    });

    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("addTestAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function updateStatusAction(
  id: string,
  nextChecked: boolean
): Promise<ApiResponse<void>> {
  try {
    const url = nextChecked ? API.test.activate(id) : API.test.deactivate(id);

    const response = await apiFetch(url, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("updateStatusAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
