<?php

namespace App\Listeners;

use App\Events\Vouchers\VouchersCreated;
use App\Mail\VouchersCreatedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendVoucherAddedNotification implements ShouldQueue
{
    public function handle(VouchersCreated $event): void
    {

        $vouchers = $event->vouchers;
        $failedVouchers = $event->failedVouchers;
        $user = $event->user;

        // Log successful and failed vouchers
        Log::info('Vouchers subido correctamente:', $vouchers);
        if (!empty($failedVouchers)) {
            Log::error('Falla al subir vouchers:', $failedVouchers);
        }

        // $mail = new VouchersCreatedMail($event->vouchers, $event->user);
        // Mail::to($event->user->email)->send($mail);

        $mail = new VouchersCreatedMail($vouchers, $failedVouchers, $user);
        Mail::to($user->email)->send($mail);
    }
}
