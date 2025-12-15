<?php

namespace App\Support\Trade;

final class ExportBundleDocKeys
{
    public const COMMERCIAL_INVOICE = 'commercial_invoice';
    public const PACKING_LIST = 'packing_list';
    public const NEGOTIATION_LETTER = 'negotiation_letter';
    public const BOE_ONE = 'boe_one';
    public const BOE_TWO = 'boe_two';

    public static function required(): array
    {
        return [
            self::COMMERCIAL_INVOICE,
            self::PACKING_LIST,
            self::NEGOTIATION_LETTER,
            self::BOE_ONE,
            self::BOE_TWO,
        ];
    }
}