<?php

namespace App\Services;

use App\Events\Vouchers\VouchersCreated;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;

class VoucherService
{
    public function getVouchers(?int $page, ?int $paginate, array $filters = []): LengthAwarePaginator
    {
        $query = Voucher::with(['lines', 'user']);
        if (!empty($filters['serie'])) {
            $query->where('series', $filters['serie']);
        }
        if (!empty($filters['number'])) {
            $query->where('number', $filters['number']);
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [
                $filters['start_date'] . ' 00:00:00',
                $filters['end_date'] . ' 23:59:59',
            ]);
        }
        return $query->paginate(perPage: $paginate, page: $page);
    }

    /**
     * @param string[] $xmlContents
     * @param User $user
     * @return Voucher[]
     */
    public function storeVouchersFromXmlContents(array $xmlContents, User $user): array
    {
        $vouchers = [];
        $failedVouchers = [];

        foreach ($xmlContents as $xmlContent) {

            try {
                $vouchers[] = $this->storeVoucherFromXmlContent($xmlContent, $user);
            } catch (\Exception $e) {
                $failedVouchers[] = [
                    'xml_content' => $xmlContent,
                    'error' => $e->getMessage(),
                ];
            }
        }

        VouchersCreated::dispatch($vouchers, $failedVouchers, $user);

        return $vouchers;
    }

    public function storeVoucherFromXmlContent(string $xmlContent, User $user): Voucher
    {
        $xml = new SimpleXMLElement($xmlContent);
        // var_dump($xml);
        $issuerName = (string) ($xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyName/cbc:Name')[0] ?? '');
        $issuerDocumentType = (string) ($xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0] ?? '');
        $issuerDocumentNumber = (string) ($xml->xpath('//cac:AccountingSupplierParty/cac:Party/cac:PartyIdentification/cbc:ID')[0] ?? '');

        $receiverName = (string) ($xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName')[0] ?? '');
        $receiverDocumentType = (string) ($xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID/@schemeID')[0] ?? '');
        $receiverDocumentNumber = (string) ($xml->xpath('//cac:AccountingCustomerParty/cac:Party/cac:PartyIdentification/cbc:ID')[0] ?? '');

        $totalAmount = (string) ($xml->xpath('//cac:LegalMonetaryTotal/cbc:TaxInclusiveAmount')[0] ?? '');

        $series = (string) ($xml->xpath('//cbc:ID')[0] ?? '');
        $number = (string) ($xml->xpath('//cbc:ID')[0] ?? '');
        $voucherType = (string) ($xml->xpath('//cbc:InvoiceTypeCode')[0] ?? '');
        $currency = (string) ($xml->xpath('//cbc:DocumentCurrencyCode')[0] ?? '');

        $voucher = new Voucher([
            'issuer_name' => $issuerName,
            'issuer_document_type' => $issuerDocumentType,
            'issuer_document_number' => $issuerDocumentNumber,
            'receiver_name' => $receiverName,
            'receiver_document_type' => $receiverDocumentType,
            'receiver_document_number' => $receiverDocumentNumber,
            'total_amount' => $totalAmount,
            'xml_content' => $xmlContent,
            'user_id' => $user->id,
            'series' => $series,
            'number' => $number,
            'voucher_type' => $voucherType,
            'currency' => $currency,
        ]);
        $voucher->save();

        foreach ($xml->xpath('//cac:InvoiceLine') as $invoiceLine) {
            $name = (string) ($invoiceLine->xpath('cac:Item/cbc:Description')[0] ?? '');
            $quantity = (float) ($invoiceLine->xpath('cbc:InvoicedQuantity')[0] ?? '');
            $unitPrice = (float) ($invoiceLine->xpath('cac:Price/cbc:PriceAmount')[0] ?? '');

            $voucherLine = new VoucherLine([
                'name' => $name,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'voucher_id' => $voucher->id,
            ]);

            $voucherLine->save();
        }

        return $voucher;
    }

    public function getTotalAmounts(): array
    {
        $totals = Voucher::select(
            DB::raw("SUM(CASE WHEN currency = 'PEN' THEN total_amount  ELSE 0 END) as total_soles"),
            DB::raw("SUM(CASE WHEN currency = 'USD' THEN total_amount  ELSE 0 END) as total_dollars")
        )->first();

        return [
            'total_soles' => $totals->total_soles,
            'total_dollars' => $totals->total_dollars,
        ];
    }

    public function deleteVoucher(string $id): array
    {
        $voucher = Voucher::find($id);
        if (!$voucher) {
            throw new ModelNotFoundException('Voucher no encontrado');
        }
        $voucher->delete();

        return [
            'success' => true,
            'message' => 'Voucher successfully deleted',
        ];
    }
}
