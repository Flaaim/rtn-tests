"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import { PublicQuestionData } from "@/interfaces/question.interface";
import { apiFetch } from "@/lib/apiClient";
import { API } from "@/app/api";
import { handleApiResponse } from "@/lib/handleApiResponse";

export async function fetchPublicQuestionAction(
  id: string
): Promise<ApiResponse<PublicQuestionData>> {
  try {
    const response = await apiFetch(API.question.getQuestion(id), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<PublicQuestionData>(response);
  } catch (error) {
    console.error("fetchPublicQuestionAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function fetchQuestionIdsForSitemapAction(): Promise<ApiResponse<string[]>> {
  try {
    const response = await apiFetch(`/v1/seo/sitemap/questions`, {
      method: "GET",
      next: { revalidate: 86400 },
    });

    return await handleApiResponse<string[]>(response);
  } catch {
    return { ok: false, error: "Failed to fetch sitemap questions" };
  }
}
