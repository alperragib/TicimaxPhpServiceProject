<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Order;

/**
 * Ticimax OdemeTipi enum values.
 * Used in WebSiparisSaveOdeme.OdemeTipi when creating an order.
 */
final class PaymentType
{
    public const KREDI_KARTI            = 0;
    public const HAVALE                 = 1;
    public const KAPIDA_ODEME_NAKIT     = 2;
    public const KAPIDA_ODEME_KK        = 3;
    public const MOBIL_ODEME            = 4;
    public const BKM_EXPRESS            = 5;
    public const PAYPAL                 = 6;
    public const CARI                   = 7;
    public const MAIL_ORDER             = 8;
    public const IPARA                  = 9;
    public const NAKIT                  = 10;
    public const PAYUONECLICK           = 11;
    public const CARI_KREDI             = 12;
    public const GARANTIPAY             = 13;
    public const PAYU_BKMEXPRESS        = 14;
    public const NESTPAY                = 15;
    public const PAYCELL                = 16;
    public const IYZIPAY                = 17;
    public const HOPI                   = 18;
    public const PAYBYME                = 19;
    public const HEDIYE_CEKI            = 20;
    public const PAYGURUMOBIL           = 21;
    public const PAYNET                 = 22;
    public const TELR                   = 23;
    public const COMPAY                 = 24;
    public const PAYTR                  = 25;
    public const MAXIMUM_MOBIL          = 26;
    public const MAGAZADA_ODE           = 27;
}
