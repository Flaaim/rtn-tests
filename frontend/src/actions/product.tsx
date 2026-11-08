"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import { ListProductsDTO } from "@/interfaces/product.interface";
import { apiFetch } from "@/lib/apiClient";
import { API } from "@/app/api";
import { handleApiResponse } from "@/lib/handleApiResponse";

export async function fetchProductsPaginationAction(
  page: number,
  perPage: number
): Promise<ApiResponse<ListProductsDTO>> {
  try {
    const response = await apiFetch(API.product.getPaginated(page, perPage), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<ListProductsDTO>(response);
  } catch (error) {
    console.error("fetchProductsPaginationAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
