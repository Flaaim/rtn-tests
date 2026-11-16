"use client";

import { useEffect, useState } from "react";
import { EventDTO } from "@/interfaces/event.interface";
import { fetchEventHistory } from "@/actions/event";

interface TimelineProps {
  aggregateId: string;
}

export function EventHistoryTimeline({ aggregateId }: TimelineProps) {
  const [events, setEvents] = useState<EventDTO[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const initializeHistory = async () => {
      const result = await fetchEventHistory(aggregateId);

      if (!result.ok || !result.data) {
        setEvents([]);
        return;
      }
      setEvents(result.data.items);
      setLoading(false);
    };
    void initializeHistory();
  }, [aggregateId]);

  if (loading) return <div className="p-4 text-gray-500">Загрузка истории...</div>;
  if (events.length === 0) return <div className="p-4 text-gray-500">История событий пуста.</div>;

  return (
    <div className="relative border-l border-gray-200 dark:border-gray-700 ml-4 mt-4">
      {events.map((event: EventDTO) => {
        const payloadData = JSON.parse(event.payload);

        const eventName = event.type.split("\\").pop();

        return (
          <div key={event.eventId} className="mb-10 ml-6">
            <span className="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -left-3 ring-8 ring-white dark:ring-slate-900 dark:bg-blue-900">
              <div className="w-2 h-2 bg-blue-500 rounded-full"></div>
            </span>
            <h3 className="flex items-center mb-1 text-lg font-semibold text-gray-900 dark:text-white">
              {eventName}
              <span className="bg-blue-100 text-blue-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 ml-3">
                ID: {event.aggregateId}
              </span>
            </h3>

            {/* Дата и время */}
            <time className="block mb-2 text-sm font-normal leading-none text-gray-400 dark:text-gray-500">
              {new Date(event.createdAt).toLocaleString("ru-RU")}
            </time>

            {/* Блок с JSON Payload */}
            <div className="mt-3 bg-gray-50 dark:bg-slate-950 p-4 rounded-lg border border-gray-200 dark:border-gray-800 overflow-x-auto">
              <pre className="text-xs text-gray-800 dark:text-emerald-400 font-mono">
                {JSON.stringify(payloadData, null, 2)}
              </pre>
            </div>
          </div>
        );
      })}
    </div>
  );
}
