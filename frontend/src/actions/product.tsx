"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import { ListProductsDTO, ProductFull } from "@/interfaces/product.interface";
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

export async function removeProductAction(id: string): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.product.remove(id), {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("removeProductAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function fetchProductAction(id: string): Promise<ApiResponse<ProductFull>> {
  try {
    const response = await apiFetch(API.product.get(id), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<ProductFull>(response);
  } catch (error) {
    console.error("fetchProductAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
