<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Order;

/**
 * Ticimax OdemeDurumu enum values.
 * Used in WebSiparisSaveOdeme.OdemeDurumu when creating an order.
 */
final class PaymentStatus
{
    public const ONAY_BEKLIYOR = 0;
    public const ONAYLANDI     = 1;
    public const HATALI        = 2;
    public const IADE_EDILMIS  = 3;
    public const IPTAL_EDILMIS = 4;
}
