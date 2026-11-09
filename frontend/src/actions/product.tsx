"use server";

import { ApiResponse } from "@/interfaces/response.interface";
import {
  ChangeAmountPayload,
  CreatePaymentProductPayload,
  CreatePaymentResponse,
  ListProductsDTO,
  ProductFull,
  ProductPaymentResult,
  SendProductPayload,
} from "@/interfaces/product.interface";
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

export async function changeAmountProductAction(
  payload: ChangeAmountPayload
): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.product.changeAmount(payload.id), {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        amount: payload.amount,
      }),
    });
    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("changeAmountProductAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function sendProductAction(payload: SendProductPayload): Promise<ApiResponse<void>> {
  try {
    const response = await apiFetch(API.product.send(payload.id), {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        email: payload.email,
      }),
    });
    return handleApiResponse<void>(response);
  } catch (error) {
    console.error("sendProductAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function createPaymentProductAction(
  payload: CreatePaymentProductPayload
): Promise<ApiResponse<CreatePaymentResponse>> {
  try {
    const response = await apiFetch(API.product.createPayment(), {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify({
        email: payload.email,
        productId: payload.id,
        returnUrl: String(process.env.YOOKASSA_RETURN_PRODUCT_URL),
      }),
    });
    return handleApiResponse<CreatePaymentResponse>(response);
  } catch (error) {
    console.error("createPaymentProductAction Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}

export async function fetchProductPaymentResult(
  paymentId: string
): Promise<ApiResponse<ProductPaymentResult>> {
  try {
    const response = await apiFetch(API.product.getPaymentResult(paymentId), {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
    });
    return handleApiResponse<ProductPaymentResult>(response);
  } catch (error) {
    console.error("fetchProductPaymentResult Fetch error:", error);
    return { ok: false, error: "Не удалось подключиться к серверу API." };
  }
}
