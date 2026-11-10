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
  failedReason: string;
}
