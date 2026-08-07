export interface AddCoursePayload {
  name: string;
  cipher: string;
  draft: string;
}

export interface PaginatedCourses {
  items: CourseItem[];
  totalCount: number;
  totalPages: number;
}

export interface CourseItem {
  courseId: string;
  status: string;
  name: string;
  cipher: string;
  createdAt: string;
}
