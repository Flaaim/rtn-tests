"use server";

import { AddParserPayload } from "@/interfaces/parser.interface";
import { ApiResponse } from "@/interfaces/response.interface";
import { apiFetch } from "@/lib/apiClient";
import { API } from "@/app/api";
import { handleApiResponse } from "@/lib/handleApiResponse";

export async function addParserAction(payload: AddParserPayload): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.parser.add(), {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        host: payload.host,
        login: payload.login,
        password: payload.password,
      }),
    });

    return await handleApiResponse(response);
  } catch (error) {
    console.error("addParserAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
