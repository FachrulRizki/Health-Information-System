<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExportReadyNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $filename,
        private readonly string $filepath,
    ) {}

    /**
     * Deliver via database so the user can see it on next page load.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message'  => "File ekspor \"{$this->filename}\" siap diunduh.",
            'filename' => $this->filename,
            'filepath' => $this->filepath,
            'download_url' => route('report.export.download', ['filename' => $this->filename]),
        ];
    }
}
