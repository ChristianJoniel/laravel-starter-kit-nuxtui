export type NotificationData = {
    title: string;
    body: string;
    icon: string;
    action_url: string;
};

export type AppNotification = {
    id: string;
    type: string;
    data: NotificationData;
    read_at: string | null;
    created_at: string;
    updated_at: string;
};
