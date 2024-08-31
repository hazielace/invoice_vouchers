<?php

namespace App\Http\Controllers\Vouchers\Voucher;

use App\Http\Requests\Vouchers\GetVouchersRequest;
use App\Http\Resources\Vouchers\VoucherResource;
use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class GetVoucherHandler
{
    private VoucherService $voucherService;

    public function __construct(VoucherService $voucherService)
    {
    }

    public function __invoke(): JsonResponse
    {
        $totals = $this->voucherService->getTotalAmounts();

        return response()->json([
            'total_soles' => $totals['total_soles'],
            'total_dollars' => $totals['total_dollars'],
        ], 200);
    }
    
}
