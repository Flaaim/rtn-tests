"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import {
  AddCategoryPayload,
  CategoryDTO,
  CategoryFull,
  ChangeDescriptionPayload,
  MoveCategoryPayload,
  RenameCategoryPayload,
} from "@/interfaces/category.interface";
import { apiFetch } from "@/lib/apiClient";
import { API } from "@/app/api";
import { handleApiResponse } from "@/lib/handleApiResponse";

export async function fetchCategoryTreeAction(): Promise<ApiResponse<CategoryDTO[]>> {
  try {
    const response = await apiFetch(API.category.getAll(), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<CategoryDTO[]>(response);
  } catch (error) {
    console.error("fetchCategoryTreeAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function fetchPublicCategoryTreeAction(): Promise<ApiResponse<CategoryDTO[]>> {
  try {
    const response = await apiFetch(API.category.getAll(), {
      method: "GET",
      next: { revalidate: 3600 },
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<CategoryDTO[]>(response);
  } catch (error) {
    console.error("fetchCategoryTreeAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function fetchCategoryAction(categoryId: string): Promise<ApiResponse<CategoryFull>> {
  try {
    const response = await apiFetch(API.category.get(categoryId), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<CategoryFull>(response);
  } catch (error) {
    console.error("fetchCategoryAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function addCategoryAction(payload: AddCategoryPayload): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.category.add(), {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        name: payload.name,
        description: payload.description,
        parentId: payload.parentId,
      }),
    });
    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("addCategoryAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function renameCategoryAction(
  payload: RenameCategoryPayload
): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.category.rename(payload.id), {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        name: payload.name,
      }),
    });
    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("renameCategoryAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function moveCategoryAction(payload: MoveCategoryPayload): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.category.move(payload.id), {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        parentId: payload.parentId,
      }),
    });
    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("moveCategoryAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function removeCategoryAction(id: string): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.category.remove(id), {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });

    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("removeCategoryAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
export async function changeDescriptionCategoryAction(
  payload: ChangeDescriptionPayload
): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.category.changeDescription(payload.id), {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        description: payload.description,
      }),
    });

    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("changeDescriptionCategoryAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
