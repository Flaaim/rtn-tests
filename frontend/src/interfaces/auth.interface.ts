export interface JoinData {
  email: string;
  password: string;
  confirm_password?: string;
}

export interface LoginData {
  email: string;
  password: string;
}
export interface AuthDTO {
  id: string;
  email: string;
  role: string;
  networks: NetworkItem[];
}

export interface NetworkItem {
  network: string;
  identity: string;
}

export interface ForceChangePasswordPayload {
  userId: string;
  password: string;
}
