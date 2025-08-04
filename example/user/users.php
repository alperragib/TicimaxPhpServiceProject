<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../config.php';

use AlperRagib\Ticimax\Ticimax;


$ticimax = new Ticimax(TICIMAX_MAIN_DOMAIN, TICIMAX_API_KEY);
$userService = $ticimax->userService();

echo "=== Save User Example ===\n";

echo "Adding new user:\n";

$newUserData = [
    'ID' => 0, // 0 for new user, existing ID for update
    'Isim' => 'John',
    'Soyisim' => 'Doe',
    'Mail' => 'john.doe@example.com',
    'Telefon' => '0212 555 12 34',
    'CepTelefonu' => '0555 123 45 67',
    'Sifre' => 'password123',
    'Aktif' => true,
    'Onay' => true,
    'MailIzin' => true,
    'SmsIzin' => true,
    'KVKKSozlesmeOnay' => true,
    'UyelikSozlesmeOnay' => true,
    'VKayitDil' => 'tr',
];

$userSettings = [
    'AlisverissizOdemeGuncelle' => false,
    'CepTelefonuGuncelle' => false,
    'CinsiyetGuncelle' => false,
    'DogumTarihiGuncelle' => false,
    'IlGuncelle' => false,
    'IlceGuncelle' => false,
    'IsimGuncelle' => false,
    'KVKKSozlesmeOnayGuncelle' => false,
    'KapidaOdemeYasaklaGuncelle' => false,
    'KrediLimitiGuncelle' => false,
    'MailGuncelle' => false,
    'MailIzinGuncelle' => false,
    'MeslekGuncelle' => false,
    'MusteriKoduGuncelle' => false,
    'SifreGuncelle' => false,
    'SifreKaydetmeTuru' => null,
    'SmsIzinGuncelle' => false,
    'TelefonGuncelle' => false,
    'UyeSifreyiKendiOlustursun' => false,
    'UyelikSozlesmeOnayGuncelle' => false,
    'UyelikTarihiGuncelle' => false,
    'UyelikTuruGuncelle' => false,
];

$saveUserResponse = $userService->saveUser($newUserData, $userSettings);

if ($saveUserResponse->isSuccess()) {
    echo $saveUserResponse->message;
}

echo "\n=== List Users ===\n";

$filters = [
    'Aktif'                      => 1,
    'AlisverisYapti'             => -1,
    'BakiyeGetir'                => null,
    'Cinsiyet'                   => -1,
    'DogumTarihi1'               => null,
    'DogumTarihi2'               => null,
    'DuzenlemeTarihi1'           => null,
    'DuzenlemeTarihi2'           => null,
    'IlID'                       => 0,
    'IlceID'                     => 0,
    'IzinGuncellemeTarihi1'      => null,
    'IzinGuncellemeTarihi2'      => null,
    'IzinGuncellemeTarihiBas'    => null,
    'IzinGuncellemeTarihiGetir'  => null,
    'IzinGuncellemeTarihiSon'    => null,
    'Mail'                       => '',
    'MailIzin'                   => -1,
    'Onay'                       => null,
    'SmsIzin'                    => -1,
    'SonGirisTarihi1'            => null,
    'SonGirisTarihi2'            => null,
    'Telefon'                    => '',
    'TelefonEsit'                => '',
    'UyeID'                      => 0,
    'UyelikTarihi1'              => null,
    'UyelikTarihi2'              => null,
];

$pagination = [
    'BaslangicIndex'  => 0,
    'KayitSayisi'     => 10,
    'SiralamaDegeri'  => 'ID',
    'SiralamaYonu'    => 'DESC',
];

$response = $userService->getUsers();
if ($response->isSuccess()) {
    foreach ($response->data as $user) {
        echo (
            ($user->Isim ?? '[No Isim]') .
            ' (ID: ' . ($user->ID ?? '[No ID]') .
            ', Mail: ' . ($user->Mail ?? '[No Mail]') .
            ")\n"
        );
    }
}
