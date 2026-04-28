<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Order;

use InvalidArgumentException;

/**
 * Ticimax SiparisDurumu (WebSiparisDurumlari) values.
 *
 * Important: the SiparisServis WSDL declares WebSiparisDurumlari as an
 * xs:string enum, so SetSiparisDurum expects values like "OdemeBekliyor"
 * or "Onaylandi" — NOT the integer codes from the older PDF documentation.
 * The integer codes are still accepted by Ticimax's filter endpoints
 * (SelectSiparis), so we keep both representations: pass the integer
 * constants as $status values and the service translates them to the
 * required PascalCase string when calling SetSiparisDurum.
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
    public const TESLIM_EDILEMEDI        = 18;

    /**
     * Integer code → PascalCase enum name expected by SetSiparisDurum.
     */
    private const NAMES = [
        self::ON_SIPARIS              => 'OnSiparis',
        self::ONAY_BEKLIYOR           => 'OnayBekliyor',
        self::ONAYLANDI               => 'Onaylandi',
        self::ODEME_BEKLIYOR          => 'OdemeBekliyor',
        self::PAKETLENIYOR            => 'Paketleniyor',
        self::TEDARIK_EDILIYOR        => 'TedarikEdiliyor',
        self::KARGOYA_VERILDI         => 'KargoyaVerildi',
        self::TESLIM_EDILDI           => 'TeslimEdildi',
        self::IPTAL                   => 'Iptal',
        self::IADE                    => 'Iade',
        self::SILINMIS                => 'Silinmis',
        self::IADE_TALEP_ALINDI       => 'IadeTalepAlindi',
        self::IADE_ULASTI_ODEME       => 'IadeUlastiOdemeYapilacak',
        self::IADE_ODEME_YAPILDI      => 'IadeOdemeYapildi',
        self::TESLIM_ONCESI_IPTAL     => 'TeslimOncesiIptal',
        self::IPTAL_TALEBI            => 'IptalTalebi',
        self::KISMI_IADE_TALEBI       => 'KismiIadeTalebi',
        self::KISMI_IADE_YAPILDI      => 'KismiIadeYapildi',
        self::TESLIM_EDILEMEDI        => 'TeslimEdilemedi',
    ];

    /**
     * Convert an integer status (or pass-through PascalCase string) into the
     * PascalCase value WebSiparisDurumlari expects.
     *
     * @param int|string $status
     * @return string
     */
    public static function nameFor($status): string
    {
        if (is_string($status)) {
            return $status;
        }
        if (!isset(self::NAMES[$status])) {
            throw new InvalidArgumentException("Unknown OrderStatus value: {$status}");
        }
        return self::NAMES[$status];
    }
}
