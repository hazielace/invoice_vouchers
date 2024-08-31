<?php

namespace App\Http\Controllers\Vouchers\Voucher;

use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
class DeleteVoucherHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
        
    }

    public function __invoke(Request $request, string $id): Response
    {
        try {
            $result = $this->voucherService->deleteVoucher($id);


            $responseContent = [
                'data' => $result,
            ];

            return new Response(
                json_encode($responseContent), 
                200, 
                ['Content-Type' => 'application/json']
            );
        } catch (\Exception $e) {
            $errorContent = [
                'data' => [
                    'success' => false,
                    'message' => $e->getMessage(),
                ],
            ];

            return new Response(
                json_encode($errorContent),
                404,
                ['Content-Type' => 'application/json']
            );
        }
    }
}
