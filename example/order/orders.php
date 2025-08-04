<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use AlperRagib\Ticimax\Ticimax;
use AlperRagib\Ticimax\Model\BaseModel;


$ticimax = new Ticimax(TICIMAX_MAIN_DOMAIN, TICIMAX_API_KEY);
$orderService = $ticimax->orderService();


echo "\n--- List Orders ---\n";

$filters = [
    'DurumTarihiBas'              => null,
    'DurumTarihiSon'              => null,
    'DuzenlemeTarihiBas'          => null,
    'DuzenlemeTarihiSon'          => null,
    'EFaturaURL'                  => null,
    'EntegrasyonAktarildi'        => -1,
    'EntegrasyonParams'           => [
        'AlanDeger'               => '',
        'Deger'                   => '',
        'EntegrasyonKodu'         => '',
        'EntegrasyonParamsAktif'  => false,
        'TabloAlan'               => '',
        'Tanim'                   => ''
    ],
    'FaturaNo'                    => '',
    'IptalEdilmisUrunler'         => true,
    'KampanyaGetir'               => false,
    'KargoEntegrasyonTakipDurumu' => null,
    'KargoFirmaID'                => -1,
    'OdemeDurumu'                 => -1,
    'OdemeGetir'                  => null,
    'OdemeTamamlandi'             => null,
    'OdemeTipi'                   => -1,
    'PaketlemeDurumu'             => null,
    'PazaryeriIhracat'            => null,
    'SiparisDurumu'               => -1,
    'SiparisID'                   => -1,
    'SiparisKaynagi'              => '',
    'SiparisKodu'                 => '',
    'SiparisNo'                   => '',
    'SiparisTarihiBas'            => null,
    'SiparisTarihiSon'            => null,
    'StrPaketlemeDurumu'          => '',
    'StrSiparisDurumu'            => '',
    'StrSiparisID'                => '',
    'TedarikciID'                 => -1,
    'TeslimatGunuBas'             => null,
    'TeslimatGunuSon'             => null,
    'TeslimatMagazaID'            => null,
    'UrunGetir'                   => null,
    'UyeID'                       => 0,
    'UyeTelefon'                  => '',
];

$pagination = [
    'BaslangicIndex'  => 0,
    'KayitSayisi'     => 15,
    'SiralamaDegeri'  => 'ID',
    'SiralamaYonu'    => 'DESC',
];

$response = $orderService->getOrders($filters, $pagination);

if ($response->isSuccess()) {
    foreach ($response->data as $order) {
        echo (
            ($order->AdiSoyadi ?? '[No AdiSoyadi]') .
            ' (ID: ' . ($order->ID ?? '[No ID]') .
            ' (Tutar: ' . ($order->Tutar ?? '[No Tutar]') .
            ")\n"
        );

        foreach ($order->Urunler as $urun) {
            $urun = new BaseModel($urun);
            echo (
                ($urun->UrunAdi ?? '[No UrunAdi]') .
                ' (ID: ' . ($urun->ID ?? '[No ID]') .
                ' (Tutar: ' . ($urun->DurumAd ?? '[No DurumAd]') .
                ' (Tutar: ' . ($urun->Tutar ?? '[No Tutar]') .
                ")\n"
            );
        }
    }
}


echo "\n--- Create Orders ---\n";

$order = [
    'BNPLNo'                      => null,
    'FaturaAdres'                 => null,
    'FaturaAdresId'               => 1,
    'HediyeCeki'                  => null,
    'HediyePaketiTutari'          => null,
    'HediyePaketiVar'             => null,
    'IndirimTutari'               => 0.0,
    'IsMarketplace'               => null,
    'KargoAdresId'                => 1,
    'KargoFirmaId'                => 2,
    'KargoGondericiBilgi'         => null,
    'KargoKatkiPayi'              => null,
    'KargoTutari'                 => 0.0,
    'KargoyaSonVerilmeTarihi'     => null,
    'KdvOraniniSiparisUrundenAl'  => null,
    'MailGonder'                  => null,
    'MaliyetiSiparisUrundenAl'    => null,
    'MarketPlaceOdemeAlindi'      => null,
    'MarketplaceKampanyaKodu'     => null,
    'MarketplaceParams'           => null,
    'MusteriKargoTeslimTarihi'    => null,
    'Odeme'                       => [
        'BankaKomisyonu' => 0.0,
        'HavaleHesapID' => null,
        'KapidaOdemeTutari' => 0.0,
        'OdemeDurumu' => 1,
        'OdemeIndirimi' => 0.0,
        'OdemeNotu' => 'Test siparişi',
        'OdemeSecenekID' => 1,
        'OdemeTipi' => 1,
        'TaksitSayisi' => 1,
        'Tarih' => date('c'),
        'Tutar' => 100.00
    ],
    'OdemeVadeTarihi'             => null,
    'Odemeler'                    => null,
    'OzelAlan1'                   => null,
    'OzelAlan2'                   => null,
    'OzelAlan3'                   => null,
    'ParaBirimi'                  => 'TRY',
    'PazaryeriButikId'            => null,
    'PazaryeriIhracat'            => null,
    'ReferansNo'                  => null,
    'SiparisKaynagi'              => 'Ticimax Web Service',
    'SiparisNo'                   => null,
    'SiparisNotu'                 => 'This is a test order.',
    'SmsGonder'                   => null,
    'TeslimatAdres'               => null,
    'TeslimatSaati'               => null,
    'TeslimatTarihi'              => null,
    'UrunTutari'                  => 100.00,
    'UrunTutariKdv'               => 20.00,
    'Urunler'                     => [
        [
            'Adet' => 1,
            'KdvOrani' => 20,
            'KdvTutari' => 20.00,
            'Maliyet' => 100.00,
            'Tutar' => 100.00,
            'UrunID' => 1
        ]
    ],
    'UyeAdi'                      => null,
    'UyeCep'                      => null,
    'UyeId'                       => 1050,
    'UyeKazanimAktif'             => null,
    'UyeMail'                     => null,
];

$response = $orderService->createOrder($order);
print($response->getMessage());
print_r($response->data);
