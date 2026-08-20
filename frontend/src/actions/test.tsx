"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import {
  AddTestPayload,
  ChangeCipherTestPayload,
  PaginatedTests,
  RenameTestPayload,
  TestFull,
  UpdateSettingsTestPayload,
} from "@/interfaces/test.interface";
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

export async function removeTestAction(id: string): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.test.remove(id), {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });

    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("removeTestAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function fetchTestAction(id: string): Promise<ApiResponse<TestFull>> {
  try {
    const response = await apiFetch(API.test.get(id), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });

    return handleApiResponse<TestFull>(response);
  } catch (error) {
    console.error("fetchTestAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function changeCipherTestAction(
  payload: ChangeCipherTestPayload
): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.test.changeCipher(payload.id), {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        cipher: payload.cipher,
      }),
    });

    return handleApiResponse(response);
  } catch (error) {
    console.error("changeCipherTestAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function renameTestAction(payload: RenameTestPayload): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.test.rename(payload.id), {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        name: payload.name,
        description: payload.description,
      }),
    });

    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("renameTestAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function updateSettingsTestAction(
  payload: UpdateSettingsTestPayload
): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.test.updateSettings(payload.id), {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        numberOfTickets: payload.numberOfTickets,
        numberQuestionsInTicket: payload.numberQuestionsInTicket,
        allowedMistakes: payload.allowedMistakes,
      }),
    });

    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("updateSettingsTestAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
