import { fetchRecipientAction } from "@/actions/recipient";

interface RecipientOverviewPageProps {
  params: Promise<{ recipientId: string }>;
}

export default async function RecipientOverviewPage({ params }: RecipientOverviewPageProps) {
  const { recipientId } = await params;
  const result = await fetchRecipientAction(recipientId);
  if (!result.ok || !result.data) {
    return null;
  }
  console.log(result);
}
