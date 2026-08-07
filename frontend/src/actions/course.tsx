"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import { apiFetch } from "@/lib/apiClient";
import { API } from "@/app/api";
import { handleApiResponse } from "@/lib/handleApiResponse";
import { AddCoursePayload } from "@/interfaces/course.interface";

export async function addCourseAction(payload: AddCoursePayload): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.course.add(), {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        name: payload.name,
        cipher: payload.cipher,
        draft: payload.draft,
      }),
    });

    return await handleApiResponse<void>(response);
  } catch (error) {
    console.error("addCourseAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
