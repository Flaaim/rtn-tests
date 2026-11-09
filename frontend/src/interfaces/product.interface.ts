export interface ListProductsDTO {
  items: ProductDTO[];
  totalCount: number;
  totalPages: number;
}

export interface ProductDTO {
  productId: string;
  cipher: string;
  amount: string;
  currency: string;
  addedAt: string;
}

export interface ProductFull {
  productId: string;
  cipher: string;
  courseId: string;
  amount: string;
  currency: string;
  addedAt: string;
  filePath: string;
}

export interface ChangeAmountPayload {
  id: string;
  amount: string;
}

export interface SendProductPayload {
  id: string;
  email: string;
}

export interface CreatePaymentProductPayload {
  id: string;
  email: string;
}

export interface CreatePaymentResponse {
  paymentId: string;
  confirmationUrl: string;
}
