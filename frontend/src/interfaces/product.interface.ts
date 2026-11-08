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
