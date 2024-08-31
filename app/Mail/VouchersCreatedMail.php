<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VouchersCreatedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public array $vouchers;
    public array $failedVouchers;
    public User $user;

    public function __construct(array $vouchers, array $failedVouchers,User $user)
    {
        $this->vouchers = $vouchers;
        $this->failedVouchers = $failedVouchers;
        $this->user = $user;
    }

    public function build(): self
    {
        return $this->view('emails.comprobante')
            ->with(['comprobantes' => $this->vouchers,'failedVouchers' => $this->failedVouchers, 'user' => $this->user]);
    }
}
