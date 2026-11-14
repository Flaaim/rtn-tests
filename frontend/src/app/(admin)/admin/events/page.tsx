import {fetchEventsPagination} from "@/actions/event";

interface AdminEventsPageProps {
  searchParams: Promise<{ page?: string; perPage?: string }>;
}


export default async function AdminRecipientsPage({ searchParams }: AdminEventsPageProps) {
  const currentPage = Number((await searchParams).page) || 1;
  const perPage = Number((await searchParams).perPage) || 15;
  const result = await fetchEventsPagination(currentPage, perPage);

  console.log(result);
}
