import { fetchTaskAction } from "@/actions/task";
import AdminBreadcrumbs from "@/components/Admin/AdminBreadcrumbs";
import TaskForm from "@/components/Admin/Task/TaskForm";

interface TaskOverviewPageProps {
  params: Promise<{ taskId: string }>;
}

export default async function TaskOverviewPage({ params }: TaskOverviewPageProps) {
  const { taskId } = await params;

  const result = await fetchTaskAction(taskId);

  if (!result.ok || !result.data) {
    return null;
  }

  const task = result.data;
  const items = [{ title: "Задачи", href: "/admin/parsers/tasks" }, { title: task.id }];

  return (
    <div className="space-y-6">
      <AdminBreadcrumbs items={items} />
      <div className="flex items-center justify-between">
        <h1 className="text-3xl font-bold">Задача: {task.id}</h1>
      </div>
      <TaskForm task={task} />
    </div>
  );
}
