<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SmartPresenceSeeder extends Seeder
{
    public function run(): void
    {

        /*
        |--------------------------------------------------------------------------
        | ROLES
        |--------------------------------------------------------------------------
        */

        DB::table('roles')->insert([
            ['id'=>1,'role'=>'super_admin'],
            ['id'=>2,'role'=>'admin'],
            ['id'=>3,'role'=>'sekretatris']
        ]);


        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */

        DB::table('users')->insert([
            [
                'name'=>'SuperAdmin',
                'email'=>'superadmin@mail.com',
                'password'=>\Illuminate\Support\Facades\Hash::make('password'),
                'role_id'=>1,
                'status'=>'active',
                'created_at'=>now(),
                'updated_at'=>now()
            ],
            [
                'name'=>'Admin',
                'email'=>'admin@mail.com',
                'password'=>\Illuminate\Support\Facades\Hash::make('password'),
                'role_id'=>2,
                'status'=>'active',
                'created_at'=>now(),
                'updated_at'=>now()
            ],
            [
                'name'=>'Sekretaris',
                'email'=>'sekretaris@mail.com',
                'password'=>\Illuminate\Support\Facades\Hash::make('password'),
                'role_id'=>3,
                'status'=>'active',
                'created_at'=>now(),
                'updated_at'=>now()
            ],

    // SPI
    [
        'name'=>'admin_spi',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('BintangSPI27'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_spi',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('PelangiSPI82'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Direksi
    [
        'name'=>'admin_direksi',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('MentariDireksi14'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_direksi',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('SamudraDireksi69'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Pemasaran
    [
        'name'=>'admin_pemasaran',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('RajawaliPemasaran31'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_pemasaran',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('MawarPemasaran88'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // IGD
    [
        'name'=>'admin_igd',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('KompasIGD25'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_igd',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('LenteraIGD73'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Pelayanan Medis
    [
        'name'=>'admin_pelayananmedis',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('AnggrekMedis42'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_pelayananmedis',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('CakrawalaMedis91'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Casemix
    [
        'name'=>'admin_casemix',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('DelimaCasemix36'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_casemix',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('KenangaCasemix57'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Rawat Jalan
    [
        'name'=>'admin_rawatjalan',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('MerpatiRawat18'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_rawatjalan',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('NusantaraRawat84'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // K3
    [
        'name'=>'admin_k3',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('GarudaK358'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_k3',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('SenjaK312'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Penunjang Medis
    [
        'name'=>'admin_penunjangmedis',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('BerlianPenunjang44'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_penunjangmedis',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('EmbunPenunjang76'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Keperawatan
    [
        'name'=>'admin_keperawatan',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('HarapanPerawat23'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_keperawatan',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('BahariPerawat67'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // PKRS
    [
        'name'=>'admin_pkrs',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('MahkotaPKRS39'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_pkrs',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('MelatiPKRS83'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // PPI
    [
        'name'=>'admin_ppi',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('PelitaPPI41'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_ppi',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('KencanaPPI79'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // HD
    [
        'name'=>'admin_hd',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('SakuraHD11'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_hd',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('LaksanaHD63'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Teratai
    [
        'name'=>'admin_teratai',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('TerataiEmas24'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_teratai',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('TerataiBiru85'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Anturium
    [
        'name'=>'admin_anturium',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('AnturiumHijau47'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_anturium',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('AnturiumUngu92'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Rosalina
    [
        'name'=>'admin_rosalina',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('RosalinaMerah53'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_rosalina',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('RosalinaPutih16'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // ICU
    [
        'name'=>'admin_icu',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('JagatICU72'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_icu',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('LangitICU29'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Alamanda
    [
        'name'=>'admin_alamanda',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('AlamandaKuning61'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_alamanda',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('AlamandaJingga34'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Perinatologi
    [
        'name'=>'admin_perinatologi',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('KasihPerina48'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_perinatologi',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('BundaPerina95'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // OK
    [
        'name'=>'admin_ok',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('PusakaOK13'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_ok',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('MustikaOK81'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Mutu
    [
        'name'=>'admin_mutu',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('MutiaraMutu26'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_mutu',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('PratamaMutu74'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Lotus
    [
        'name'=>'admin_lotus',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('LotusPerak38'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_lotus',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('LotusKristal65'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Tulip
    [
        'name'=>'admin_tulip',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('TulipCerah43'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_tulip',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('TulipDamai87'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Farmasi
    [
        'name'=>'admin_farmasi',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('FarmasiSehat21'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_farmasi',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('FarmasiPrima68'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // VK
    [
        'name'=>'admin_vk',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('BahagiaVK54'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_vk',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('SentosaVK19'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Laboratorium
    [
        'name'=>'admin_laboratorium',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('LabCemerlang46'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_laboratorium',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('LabGemilang77'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Rekam Medis
    [
        'name'=>'admin_rekammedis',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('ArsipMedis28'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_rekammedis',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('DataMedis86'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Radiologi
    [
        'name'=>'admin_radiologi',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('SinarRadiologi35'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_radiologi',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('GammaRadiologi71'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Gizi
    [
        'name'=>'admin_gizi',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('NutrisiGizi22'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_gizi',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('VitaminGizi64'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Umum RT
    [
        'name'=>'admin_umumrt',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('MandiriRT17'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_umumrt',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('SejahteraRT93'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Kepegawaian
    [
        'name'=>'admin_umumkepegawaian',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('PegawaiHebat51'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_umumkepegawaian',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('PegawaiUnggul78'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // TPP
    [
        'name'=>'admin_tpp',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('HarmoniTPP32'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_tpp',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('PrestasiTPP89'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // IPP
    [
        'name'=>'admin_ipp',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('PelangganSetia27'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_ipp',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('LayananPrima84'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Keuangan
    [
        'name'=>'admin_keuangan',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('SaldoKeuangan56'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_keuangan',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('NeracaKeuangan15'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Akuntansi
    [
        'name'=>'admin_akuntansi',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('JurnalAkuntansi62'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_akuntansi',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('LedgerAkuntansi37'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Perpajakan
    [
        'name'=>'admin_perpajakan',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('PajakTertib49'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_perpajakan',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('PajakAman94'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Sekretariat
    [
        'name'=>'admin_sekretariat',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('SekretariatMaju33'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_sekretariat',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('SekretariatHebat82'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Kasir
    [
        'name'=>'admin_kasir',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('KasirCerdas52'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_kasir',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('KasirCepat18'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Transportasi
    [
        'name'=>'admin_transportasi',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('TransportLancar45'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_transportasi',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('TransportAman97'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Kebersihan
    [
        'name'=>'admin_kebersihan',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('BersihKinclong24'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_kebersihan',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('RapiWangi73'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // CSSD
    [
        'name'=>'admin_cssd',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('SterilCSSD58'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_cssd',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('HigienisCSSD14'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Akupunktur
    [
        'name'=>'admin_akunpuktur',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('JarumSehat66'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_akunpuktur',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('TerapiNyaman31'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Diklat
    [
        'name'=>'admin_kepegawaiandiklat',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('DiklatUnggul42'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_kepegawaiandiklat',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('BelajarMaju88'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // TIK
    [
        'name'=>'admin_tik',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('DigitalTIK57'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_tik',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('TeknologiTIK23'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // TI
    [
        'name'=>'admin_ti',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('ServerTI69'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_ti',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('JaringanTI12'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Laundry
    [
        'name'=>'admin_laundry',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('LaundryBersih47'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_laundry',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('LaundryHarum91'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],

    // Keamanan
    [
        'name'=>'admin_keamanan',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('KeamananSiaga36'),
        'role_id'=>2,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
    [
        'name'=>'sekre_keamanan',
        'email'=>'',
        'password'=>\Illuminate\Support\Facades\Hash::make('PenjagaAman75'),
        'role_id'=>3,
        'status'=>'active',
        'created_at'=>now(),
        'updated_at'=>now()
    ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | EMPLOYEE TYPES
        |--------------------------------------------------------------------------
        */
        DB::table('employee_types')->insert([
            ['id'=>1,'employee_type'=>'Dokter Umum'],
            ['id'=>2,'employee_type'=>'Dokter Gigi'],
            ['id'=>3,'employee_type'=>'Dokter Spesialis'],
            ['id'=>4,'employee_type'=>'Dokter Gigi Spesialis'],
            ['id'=>5,'employee_type'=>'Perawat'],
            ['id'=>6,'employee_type'=>'Bidan'],
            ['id'=>7,'employee_type'=>'Apoteker'],
            ['id'=>8,'employee_type'=>'Tenaga Teknis Kefarmasian'],
            ['id'=>9,'employee_type'=>'Analis Kesehatan'],
            ['id'=>10,'employee_type'=>'Perekam Medis'],
            ['id'=>11,'employee_type'=>'Radiografer'],
            ['id'=>12,'employee_type'=>'Ahli Gizi'],
            ['id'=>13,'employee_type'=>'Sanitarian'],
            ['id'=>14,'employee_type'=>'ATEM'],
            ['id'=>15,'employee_type'=>'Refraksionis Optisien'],
            ['id'=>16,'employee_type'=>'Fisioterapis'],
            ['id'=>17,'employee_type'=>'Psikolog'],
            ['id'=>18,'employee_type'=>'Direksi'],
            ['id'=>19,'employee_type'=>'Non-Kesehatan'],
            ['id'=>20,'employee_type'=>'TTK'],
            ['id'=>21,'employee_type'=>'Konsultan'],
            ['id'=>22,'employee_type'=>'Tamu'],
           

        ]);

        /*
        |--------------------------------------------------------------------------
        | WORK UNITS
        |--------------------------------------------------------------------------
        */
        DB::table('work_units')->insert([
            ['id'=>1,'work_unit'=>'SPI'],
            ['id'=>2,'work_unit'=>'Direksi'],
            ['id'=>3,'work_unit'=>'Pemasaran'],
            ['id'=>4,'work_unit'=>'IGD'],
            ['id'=>5,'work_unit'=>'Pelayanan Medis'],
            ['id'=>6,'work_unit'=>'Casemix'],
            ['id'=>7,'work_unit'=>'Rawat Jalan'],
            ['id'=>8,'work_unit'=>'K3'],
            ['id'=>9,'work_unit'=>'Penunjang Medis'],
            ['id'=>10,'work_unit'=>'Keperawatan'],
            ['id'=>11,'work_unit'=>'PKRS'],
            ['id'=>12,'work_unit'=>'PPI'],
            ['id'=>13,'work_unit'=>'HD'],
            ['id'=>14,'work_unit'=>'Teratai'],
            ['id'=>15,'work_unit'=>'Anturium'],
            ['id'=>16,'work_unit'=>'Rosalina'],
            ['id'=>17,'work_unit'=>'ICU'],
            ['id'=>18,'work_unit'=>'Alamanda'],
            ['id'=>19,'work_unit'=>'Perinatologi'],
            ['id'=>20,'work_unit'=>'OK'],
            ['id'=>21,'work_unit'=>'Mutu'],
            ['id'=>22,'work_unit'=>'Lotus'],
            ['id'=>23,'work_unit'=>'Tulip'],
            ['id'=>24,'work_unit'=>'Farmasi'],
            ['id'=>25,'work_unit'=>'VK'],
            ['id'=>26,'work_unit'=>'Laboratorium'],
            ['id'=>27,'work_unit'=>'Rekam Medis'],
            ['id'=>28,'work_unit'=>'Radiologi'],
            ['id'=>29,'work_unit'=>'Gizi'],
            ['id'=>30,'work_unit'=>'Umum RT'],
            ['id'=>31,'work_unit'=>'Umum Kepegawaian'],
            ['id'=>32,'work_unit'=>'TPP'],
            ['id'=>33,'work_unit'=>'Informasi & Pengelolaan Pelanggan'],
            ['id'=>34,'work_unit'=>'Keuangan'],
            ['id'=>35,'work_unit'=>'Akuntansi'],
            ['id'=>36,'work_unit'=>'Perpajakan'],
            ['id'=>37,'work_unit'=>'Sekretariat'],
            ['id'=>38,'work_unit'=>'Kasir'],
            ['id'=>39,'work_unit'=>'Transportasi'],
            ['id'=>40,'work_unit'=>'Kebersihan'],
            ['id'=>41,'work_unit'=>'CSSD'],
            ['id'=>42,'work_unit'=>'Akunpuktur'],
            ['id'=>43,'work_unit'=>'Kepegawaian Diklat'],
            ['id'=>44,'work_unit'=>'Informasi & TIK'],
            ['id'=>45,'work_unit'=>'TI'],
            ['id'=>46,'work_unit'=>'Laundry'],
            ['id'=>47,'work_unit'=>'Keamanan'],
        ]);




        /*
        |--------------------------------------------------------------------------
        | EMPLOYEES
        |--------------------------------------------------------------------------
        */

        DB::table('employees')->insert([
            [
                'full_name'=>'dr. H. M. Arief Heriawan, Sp.B',
                'nip'=>'0411.02218',
                'employee_type_id'=> 3,
                'work_unit_id'=> null,
                'email'=>null,
                'phone'=>null,
                'is_active'=>true,
                'created_at'=>now(),
                'updated_at'=>now()
            ],
            [
                'full_name'=>'dr. Yuli Hermansyah, Sp.PD',
                'nip'=>'0411.02219',
                'employee_type_id'=> 3,
                'work_unit_id'=> null,
                'email'=>null,
                'phone'=>null,
                'is_active'=>true,
                'created_at'=>now(),
                'updated_at'=>now()
            ]
        ]);
        /*
        |--------------------------------------------------------------------------
        | MEETING ROOMS
        |--------------------------------------------------------------------------
        */

        DB::table('meeting_rooms')->insert([
            [
                'name'=>'Ruang Rapat Utama',
                'location'=>'Lantai 1',
                'capacity'=>20,
                'is_active'=>true,
                'created_at'=>now(),
                'updated_at'=>now()
            ],
            [
                'name'=>'Ruang Meeting 2',
                'location'=>'Lantai 2',
                'capacity'=>15,
                'is_active'=>true,
                'created_at'=>now(),
                'updated_at'=>now()
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | MEETINGS
        |--------------------------------------------------------------------------
        */

        DB::table('meetings')->insert([
            [
                'title'=>'Rapat Evaluasi Bulanan',
                'room_id'=>1,
                'start_time'=>Carbon::now()->addDay(),
                'end_time'=>Carbon::now()->addDay()->addHour(),
                'status'=>'menunggu',
                'created_by'=>2,
                'created_at'=>now(),
                'updated_at'=>now()
            ]
        ]);


        /*
        |--------------------------------------------------------------------------
        | MEETING PARTICIPANTS
        |--------------------------------------------------------------------------
        */

        DB::table('meeting_participants')->insert([
            [
                'meeting_id'=>1,
                'employee_id'=>1,
                'created_at'=>now()
            ],
            [
                'meeting_id'=>1,
                'employee_id'=>2,
                'created_at'=>now()
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | ATTENDANCES
        |--------------------------------------------------------------------------
        */

        DB::table('attendances')->insert([
            [
                'meeting_id'=>1,
                'employee_id'=>1,
                'check_in_time'=>Carbon::now(),
                'status'=>'hadir',
                'verified_by'=>2,
                'notes'=>null,
                'created_at'=>now(),
                'updated_at'=>now()
            ],
            [
                'meeting_id'=>1,
                'employee_id'=>2,
                'check_in_time'=>Carbon::now(),
                'status'=>'hadir',
                'verified_by'=>2,
                'notes'=>null,
                'created_at'=>now(),
                'updated_at'=>now()
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | MEETING ASSIGNMENTS
        |--------------------------------------------------------------------------
        */

        DB::table('meeting_assignments')->insert([
            [
                'meeting_id'=>1,
                'user_id'=>2,
                'assigned_by'=>1,
                'created_at'=>now()
            ]
        ]);

    }
}