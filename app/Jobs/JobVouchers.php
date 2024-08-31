<?php

namespace App\Jobs;

use App\Events\Vouchers\VouchersCreated;
use App\Models\User;
use App\Services\VoucherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobVouchers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $xmlContents;
    protected User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(array $xmlContents, User $user)
    {
        $this->xmlContents = $xmlContents;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(VoucherService $voucherService)
    {
        $vouchers = [];
        $failedVouchers = [];

        foreach ($this->xmlContents as $xmlContent) {
            try {
                $voucher = $voucherService->storeVoucherFromXmlContent($xmlContent, $this->user);
                $vouchers[] = $voucher;
            } catch (\Exception $e) {
                $failedVouchers[] = [
                    'xmlContent' => $xmlContent,
                    'error' => $e->getMessage()
                ];
            }
        }

        VouchersCreated::dispatch($vouchers, $failedVouchers, $this->user);
    }
}
