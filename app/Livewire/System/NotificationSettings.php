<?php

namespace App\Livewire\System;

use Livewire\Component;
use App\Models\FcmToken;
use App\Services\FcmNotificationService;
use Illuminate\Support\Facades\Auth;

class NotificationSettings extends Component
{
    public $userTokens = [];
    public $message = '';
    public $statusType = 'success';

    public function mount()
    {
        $this->loadTokens();
    }

    public function loadTokens()
    {
        $userId = Auth::id();
        $this->userTokens = FcmToken::where('user_id', $userId)
            ->orderBy('last_active_at', 'desc')
            ->get()
            ->toArray();
    }

    public function deleteToken($id)
    {
        FcmToken::where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        $this->loadTokens();
        $this->message = 'Perangkat berhasil dihapus dari daftar notifikasi.';
        $this->statusType = 'success';
    }

    public function testNotification()
    {
        $userId = Auth::id();
        $tokens = FcmToken::tokensForUser($userId);

        if (empty($tokens)) {
            $this->message = 'Perangkat ini belum terdaftar. Pastikan notifikasi diizinkan di browser.';
            $this->statusType = 'error';
            return;
        }

        try {
            $fcm = app(FcmNotificationService::class);
            $fcm->sendToUser(
                userId: $userId,
                title: '🔔 Uji Coba Notifikasi Mandiri',
                body: 'Halo ' . Auth::user()->name . '! Notifikasi sistem Elvith bekerja optimal di HP/Laptop Anda.',
                clickUrl: route('system.notifications')
            );

            $this->message = 'Notifikasi uji coba berhasil dikirim ke perangkat Anda!';
            $this->statusType = 'success';
        } catch (\Throwable $e) {
            $this->message = 'Gagal mengirim: ' . $e->getMessage();
            $this->statusType = 'error';
        }
    }

    public function render()
    {
        return view('livewire.system.notification-settings')
            ->layout('layouts.app', ['title' => 'Pengaturan Notifikasi — Elvith']);
    }
}