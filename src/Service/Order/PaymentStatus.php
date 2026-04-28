<?php

declare(strict_types=1);

namespace AlperRagib\Ticimax\Service\Order;

use InvalidArgumentException;

/**
 * Ticimax OdemeDurumu (WebOdemeDurumlari) values.
 *
 * Used in two places:
 *  - WebSiparisSaveOdeme.OdemeDurumu when creating an order (integer accepted).
 *  - SetSiparisOdemeDurum.OdemeDurum when mutating an existing payment record's
 *    status. The latter is an xs:string enum on the WSDL, so the integer
 *    constants are translated to the PascalCase value (e.g. ONAYLANDI →
 *    "Onaylandi") via nameFor(). PascalCase strings are accepted and passed
 *    through.
 */
final class PaymentStatus
{
    public const ONAY_BEKLIYOR = 0;
    public const ONAYLANDI     = 1;
    public const HATALI        = 2;
    public const IADE_EDILMIS  = 3;
    public const IPTAL_EDILMIS = 4;

    /**
     * Integer code → PascalCase enum name expected by SetSiparisOdemeDurum.
     */
    private const NAMES = [
        self::ONAY_BEKLIYOR => 'OnayBekliyor',
        self::ONAYLANDI     => 'Onaylandi',
        self::HATALI        => 'Hatali',
        self::IADE_EDILMIS  => 'IadeEdilmis',
        self::IPTAL_EDILMIS => 'IptalEdilmis',
    ];

    /**
     * Convert an integer status (or pass-through PascalCase string) into the
     * PascalCase value WebOdemeDurumlari expects.
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
            throw new InvalidArgumentException("Unknown PaymentStatus value: {$status}");
        }
        return self::NAMES[$status];
    }
}
