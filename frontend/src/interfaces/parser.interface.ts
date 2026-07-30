export interface AddParserPayload {
  host: string;
  login: string;
  password: string;
}

export interface LaunchParserPayload {
  parserId: string;
  branchId: string;
  ticketId: string;
}

export interface ParserShort {
  id: string;
  host: string;
}
