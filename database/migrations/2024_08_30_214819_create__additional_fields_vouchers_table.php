<?php

use App\Models\Voucher;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('series')->nullable();
            $table->string('number')->nullable();
            $table->string('voucher_type')->nullable();
            $table->string('currency')->nullable();
        });

        // Actualiza vouchers existentes
        Voucher::chunk(100, function ($vouchers) {
            foreach ($vouchers as $voucher) {
                try {
                    $xml = new \SimpleXMLElement($voucher->xml_content);
                    
                    $series = (string) ($xml->xpath('//cbc:ID')[0] ?? '');
                    $number = (string) ($xml->xpath('//cbc:ID')[0] ?? '');
                    $voucherType = (string) ($xml->xpath('//cbc:InvoiceTypeCode')[0] ?? '');
                    $currency = (string) ($xml->xpath('//cbc:DocumentCurrencyCode')[0] ?? '');

                    $voucher->series = $series;
                    $voucher->number = $number;
                    $voucher->voucher_type = $voucherType;
                    $voucher->currency = $currency;
                    $voucher->save();
                } catch (\Exception $e) {
                    Log::error('Error updating voucher: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['series', 'number', 'voucher_type', 'currency']);
        });
    }
};
