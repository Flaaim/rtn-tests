export interface ListRecipientsDTO {
  items: RecipientDTO[];
  totalCount: number;
  totalPages: number;
}

export interface RecipientDTO {
  recipientId: string;
  status: string;
  email: string;
  createdAt: string;
  failedReason?: string | null;
}

export interface RecipientFull {
  recipientId: string;
  status: string;
  email: string;
  createdAt: string;
  failedReason?: string | null;
  sendAt?: string | null;
  product: ProductDTO;
}

interface ProductDTO {
  productId: string;
  name: string;
  cipher: string;
}
