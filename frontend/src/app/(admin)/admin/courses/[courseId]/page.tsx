import {fetchCourseAction} from "@/actions/course";

interface CourseOverviewPageProps {
  params: Promise<{ courseId: string }>;
}

export default async function CourseOverviewPage({ params }: CourseOverviewPageProps) {
  const { courseId } = await params;

  const result = await fetchCourseAction(courseId);
}
