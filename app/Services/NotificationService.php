<?php

namespace App\Services;

use App\Models\Visit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send registration confirmation to patient.
     * Requirements: 18.7
     */
    public function sendRegistrationConfirmation(Visit $visit, string $channel = 'email'): bool
    {
        return match ($channel) {
            'whatsapp' => $this->sendWhatsApp($visit),
            'sms'      => $this->sendSms($visit),
            default    => $this->sendEmail($visit),
        };
    }

    private function sendEmail(Visit $visit): bool
    {
        $patient = $visit->patient;
        $email   = $patient->email ?? null;

        if (! $email) {
            Log::info('NotificationService: no email for patient, skipping.', ['patient_id' => $patient->id]);
            return false;
        }

        $message = $this->buildMessage($visit);

        try {
            Mail::raw($message, function ($mail) use ($email, $patient) {
                $mail->to($email)->subject('Konfirmasi Pendaftaran Online - '.$patient->nama_lengkap);
            });
            return true;
        } catch (\Throwable $e) {
            Log::error('NotificationService: failed to send email.', ['error' => $e->getMessage(), 'no_rawat' => $visit->no_rawat]);
            return false;
        }
    }

    private function sendWhatsApp(Visit $visit): bool
    {
        Log::info('NotificationService [WhatsApp stub]: would send message.', [
            'to'       => $visit->patient?->no_telepon,
            'no_rawat' => $visit->no_rawat,
            'message'  => $this->buildMessage($visit),
        ]);
        // TODO: integrate with WhatsApp provider
        return true;
    }

    private function sendSms(Visit $visit): bool
    {
        Log::info('NotificationService [SMS stub]: would send message.', [
            'to'       => $visit->patient?->no_telepon,
            'no_rawat' => $visit->no_rawat,
            'message'  => $this->buildMessage($visit),
        ]);
        // TODO: integrate with SMS provider
        return true;
    }

    private function buildMessage(Visit $visit): string
    {
        $patient     = $visit->patient;
        $queueNumber = $visit->queueEntry?->queue_number ?? '-';
        $poli        = $visit->poli?->nama_poli ?? '-';
        $tanggal     = $visit->tanggal_kunjungan?->format('d/m/Y') ?? '-';

        return "Konfirmasi Pendaftaran Online\n\nYth. {$patient->nama_lengkap},\n\nPendaftaran Anda telah berhasil.\n\nNo. Rawat     : {$visit->no_rawat}\nPoli          : {$poli}\nTanggal       : {$tanggal}\nNomor Antrian : {$queueNumber}\n\nHarap datang tepat waktu.\nTerima kasih.";
    }
}
