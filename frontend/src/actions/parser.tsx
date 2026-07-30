"use server";

import { AddParserPayload, LaunchParserPayload, ParserShort } from "@/interfaces/parser.interface";
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

export async function fetchParsersAction(): Promise<ApiResponse<ParserShort[]>> {
  try {
    const response = await apiFetch(API.parser.list(), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });

    return await handleApiResponse<ParserShort[]>(response);
  } catch (error) {
    console.error("fetchParsersAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function fetchParserAction(parserId: string): Promise<ApiResponse<ParserShort>> {
  try {
    const response = await apiFetch(API.parser.get(parserId), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });

    return await handleApiResponse<ParserShort>(response);
  } catch (error) {
    console.error("fetchParserAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function launchParserAction(payload: LaunchParserPayload): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.parser.launch(), {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        parserId: payload.parserId,
        branchId: payload.branchId,
        ticketId: payload.ticketId,
      }),
    });

    return await handleApiResponse(response);
  } catch (error) {
    console.error("launchParserAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
