<?php

namespace App\Livewire\System;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    /**
     * Mark a single notification as read.
     */
    public function markAsRead(string $notificationId)
    {
        if (!Auth::check()) return;

        $notification = Auth::user()->notifications()->where('id', $notificationId)->first();
        if ($notification && is_null($notification->read_at)) {
            $notification->markAsRead();
        }
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead()
    {
        if (!Auth::check()) return;

        Auth::user()->unreadNotifications->markAsRead();
    }

    /**
     * Open notification: mark as read and redirect to the target URL.
     */
    public function openNotification(string $notificationId)
    {
        if (!Auth::check()) return;

        $notification = Auth::user()->notifications()->where('id', $notificationId)->first();
        if (!$notification) return;

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $url = $notification->data['action_url'] ?? route('dashboard');

        return redirect()->to($url);
    }

    /**
     * Delete a single notification.
     */
    public function deleteNotification(string $notificationId)
    {
        if (!Auth::check()) return;

        Auth::user()->notifications()->where('id', $notificationId)->delete();
    }

    public function render()
    {
        $user = Auth::user();

        $unreadCount = $user ? $user->unreadNotifications()->count() : 0;
        $notifications = $user ? $user->notifications()->take(10)->get() : collect();

        return view('livewire.system.notification-bell', [
            'unreadCount'   => $unreadCount,
            'notifications' => $notifications,
        ]);
    }
}
