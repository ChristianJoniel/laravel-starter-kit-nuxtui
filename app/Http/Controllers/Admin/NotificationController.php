<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Return the latest 20 notifications and the unread count.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'notifications' => $user->notifications()->latest()->take(20)->get(),
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markAsRead(Request $request, string $notification): JsonResponse
    {
        $request->user()
            ->notifications()
            ->where('id', $notification)
            ->firstOrFail()
            ->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
