<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Requests\Vouchers\GetVouchersRequest;
use App\Http\Resources\Vouchers\VoucherResource;
use App\Services\VoucherService;
use Illuminate\Http\Response;

class GetVouchersHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    public function __invoke(GetVouchersRequest $request): Response
    {
        $filters = $request->only(['serie', 'number', 'start_date', 'end_date']);
        $vouchers = $this->voucherService->getVouchers(
            $request->query('page', 1),
            $request->query('paginate', 15),
            $filters
        );
        return response([
            'data' => VoucherResource::collection($vouchers),
        ], 200);
    }
}
