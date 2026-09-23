export interface CategoryDTO {
  id: string;
  name: string;
  slug: string;
  description: string;
  parentId: string | null;
  children?: CategoryDTO[];
}

export interface AddCategoryPayload {
  name: string;
  description: string;
  parentId: string | null;
}
export interface RenameCategoryPayload {
  id: string;
  name: string;
}

export interface ChangeDescriptionPayload {
  id: string;
  description: string;
}

export interface MoveCategoryPayload {
  id: string;
  parentId: string | null;
}
export interface CategoryFull {
  id: string;
  name: string;
  description: string;
  slug: string;
  parentId: string | null;
  children: CategoryFull[] | [];
}
