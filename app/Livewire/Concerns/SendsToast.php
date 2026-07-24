<?php

namespace App\Livewire\Concerns;

/**
 * SendsToast Trait
 *
 * Adds elegant toast notification methods to Livewire components.
 * Toast events are caught by the global Alpine.js toastManager()
 * defined in layouts/app.blade.php.
 *
 * Usage:
 *   use App\Livewire\Concerns\SendsToast;
 *   class MyComponent extends Component {
 *       use SendsToast;
 *       ...
 *       $this->toastSuccess('Berhasil disimpan!');
 *       $this->toastError('Terjadi kesalahan.');
 *   }
 */
trait SendsToast
{
    /**
     * Dispatch a success toast notification.
     */
    protected function toastSuccess(string $message, ?string $title = null, int $duration = 4000): void
    {
        $this->dispatch('toast-show', type: 'success', message: $message, title: $title, duration: $duration);
    }

    /**
     * Dispatch an error toast notification.
     */
    protected function toastError(string $message, ?string $title = null, int $duration = 5000): void
    {
        $this->dispatch('toast-show', type: 'error', message: $message, title: $title, duration: $duration);
    }

    /**
     * Dispatch a warning toast notification.
     */
    protected function toastWarning(string $message, ?string $title = null, int $duration = 5000): void
    {
        $this->dispatch('toast-show', type: 'warning', message: $message, title: $title, duration: $duration);
    }

    /**
     * Dispatch an info toast notification.
     */
    protected function toastInfo(string $message, ?string $title = null, int $duration = 4000): void
    {
        $this->dispatch('toast-show', type: 'info', message: $message, title: $title, duration: $duration);
    }
}
