<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Order;

/**
 * Ticimax SiparisDurumu (WebSiparisDurumlari) enum values.
 * Used by SetSiparisDurum and SelectSiparis filter.
 */
final class OrderStatus
{
    public const ON_SIPARIS              = 0;
    public const ONAY_BEKLIYOR           = 1;
    public const ONAYLANDI               = 2;
    public const ODEME_BEKLIYOR          = 3;
    public const PAKETLENIYOR            = 4;
    public const TEDARIK_EDILIYOR        = 5;
    public const KARGOYA_VERILDI         = 6;
    public const TESLIM_EDILDI           = 7;
    public const IPTAL                   = 8;
    public const IADE                    = 9;
    public const SILINMIS                = 10;
    public const IADE_TALEP_ALINDI       = 11;
    public const IADE_ULASTI_ODEME       = 12;
    public const IADE_ODEME_YAPILDI      = 13;
    public const TESLIM_ONCESI_IPTAL     = 14;
    public const IPTAL_TALEBI            = 15;
    public const KISMI_IADE_TALEBI       = 16;
    public const KISMI_IADE_YAPILDI      = 17;
}
