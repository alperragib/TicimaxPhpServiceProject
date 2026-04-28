<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use AlperRagib\Ticimax\Ticimax;
use AlperRagib\Ticimax\Service\Order\PaymentStatus;

$siparisId = (int) ($argv[1] ?? 805);

$ticimax = new Ticimax(TICIMAX_MAIN_DOMAIN, TICIMAX_API_KEY);
$orderService = $ticimax->orderService();

echo "=== verify setOrderPaymentStatus / getOrderPaymentId for SiparisID={$siparisId} ===\n\n";

// (a) getOrderPaymentId returns int
echo "[a] getOrderPaymentId({$siparisId}) -> ";
$pid = $orderService->getOrderPaymentId($siparisId);
var_dump($pid);
if ($pid === null) {
    fwrite(STDERR, "  FAIL: expected an int, got null. Aborting.\n");
    exit(1);
}

// (b) setOrderPaymentStatus(ONAYLANDI) returns success
echo "\n[b] setOrderPaymentStatus({$siparisId}, {$pid}, ONAYLANDI)\n";
$r = $orderService->setOrderPaymentStatus($siparisId, $pid, PaymentStatus::ONAYLANDI);
echo "  success: ";
var_dump($r->isSuccess());
echo "  message: " . ($r->getMessage() ?? '(null)') . "\n";

// (c) Manual: refresh Ticimax admin -> order's "Ödeme Durumu" should now be "Onaylandı"
echo "\n[c] [MANUAL] Refresh Ticimax admin for order {$siparisId} — payment row should show 'Onaylandı'.\n";

// (d) idempotency — same call again
echo "\n[d] setOrderPaymentStatus({$siparisId}, {$pid}, ONAYLANDI) — second call (idempotency check)\n";
$r2 = $orderService->setOrderPaymentStatus($siparisId, $pid, PaymentStatus::ONAYLANDI);
echo "  success: ";
var_dump($r2->isSuccess());
echo "  message: " . ($r2->getMessage() ?? '(null)') . "\n";

// (e) HATALI accepted
echo "\n[e] setOrderPaymentStatus({$siparisId}, {$pid}, HATALI)\n";
$r3 = $orderService->setOrderPaymentStatus($siparisId, $pid, PaymentStatus::HATALI);
echo "  success: ";
var_dump($r3->isSuccess());
echo "  message: " . ($r3->getMessage() ?? '(null)') . "\n";

// reset back so the test order is reusable
echo "\n[reset] setOrderPaymentStatus({$siparisId}, {$pid}, ONAY_BEKLIYOR)\n";
$reset = $orderService->setOrderPaymentStatus($siparisId, $pid, PaymentStatus::ONAY_BEKLIYOR);
echo "  success: ";
var_dump($reset->isSuccess());
echo "  message: " . ($reset->getMessage() ?? '(null)') . "\n";

// (f) bad enum value rejected
echo "\n[f] setOrderPaymentStatus({$siparisId}, {$pid}, 999) — should throw\n";
try {
    $orderService->setOrderPaymentStatus($siparisId, $pid, 999);
    echo "  FAIL: did not throw\n";
} catch (\InvalidArgumentException $e) {
    echo "  OK: InvalidArgumentException — " . $e->getMessage() . "\n";
}

echo "\n=== done ===\n";
