export interface ListEventsDTO {
  items: EventDTO[];
  totalCount: number;
  totalPages: number;
}

export interface EventDTO {
  eventId: number;
  type: string;
  payload: string;
  createdAt: string;
  aggregateId: string;
}
