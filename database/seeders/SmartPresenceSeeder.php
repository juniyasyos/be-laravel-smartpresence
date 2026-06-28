<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;

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

        $usersData = [
            ['name' => 'Super Admin', 'nip' => '0000.00001', 'email' => 'super@mail.com', 'password' => Hash::make('password'), 'roles' => [1], 'status' => 'active'],
            ['name' => 'Admin Utama', 'nip' => '0000.00002', 'email' => 'admin@mail.com', 'password' => Hash::make('password'), 'roles' => [2], 'status' => 'active'],
            ['name' => 'Sekretaris', 'nip' => '0000.00003', 'email' => 'sekretaris@mail.com', 'password' => Hash::make('password'), 'roles' => [3], 'status' => 'active'],
            ['name' => 'Budi Santoso', 'nip' => '0000.00004', 'email' => 'budi@mail.com', 'password' => Hash::make('password'), 'roles' => [2], 'status' => 'active'],
            ['name' => 'Andi Setiawan', 'nip' => '0000.00005', 'email' => 'andi@mail.com', 'password' => Hash::make('password'), 'roles' => [2], 'status' => 'active'],
            ['name' => 'Citra Dewi', 'nip' => '0000.00006', 'email' => 'citra@mail.com', 'password' => Hash::make('password'), 'roles' => [3], 'status' => 'active'],
            ['name' => 'Dian Pertiwi', 'nip' => '0000.00007', 'email' => 'dian@mail.com', 'password' => Hash::make('password'), 'roles' => [3], 'status' => 'active'],
            ['name' => 'Eko Prasetyo', 'nip' => '0000.00008', 'email' => 'eko@mail.com', 'password' => Hash::make('password'), 'roles' => [2], 'status' => 'active'],
            ['name' => 'Fajar Nugroho', 'nip' => '0000.00009', 'email' => 'fajar@mail.com', 'password' => Hash::make('password'), 'roles' => [2], 'status' => 'active'],
            ['name' => 'Gita Gutawa', 'nip' => '0000.00010', 'email' => 'gita@mail.com', 'password' => Hash::make('password'), 'roles' => [3], 'status' => 'active'],
        ];

        foreach ($usersData as $userData) {
            $roles = $userData['roles'];
            unset($userData['roles']);
            $user = User::create($userData);
            $user->roles()->attach($roles);
        }


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
            ['id'=>1,'unit_name'=>'SPI','slug'=>\Illuminate\Support\Str::slug('SPI')],
            ['id'=>2,'unit_name'=>'Direksi','slug'=>\Illuminate\Support\Str::slug('Direksi')],
            ['id'=>3,'unit_name'=>'Pemasaran','slug'=>\Illuminate\Support\Str::slug('Pemasaran')],
            ['id'=>4,'unit_name'=>'IGD','slug'=>\Illuminate\Support\Str::slug('IGD')],
            ['id'=>5,'unit_name'=>'Pelayanan Medis','slug'=>\Illuminate\Support\Str::slug('Pelayanan Medis')],
            ['id'=>6,'unit_name'=>'Casemix','slug'=>\Illuminate\Support\Str::slug('Casemix')],
            ['id'=>7,'unit_name'=>'Rawat Jalan','slug'=>\Illuminate\Support\Str::slug('Rawat Jalan')],
            ['id'=>8,'unit_name'=>'K3','slug'=>\Illuminate\Support\Str::slug('K3')],
            ['id'=>9,'unit_name'=>'Penunjang Medis','slug'=>\Illuminate\Support\Str::slug('Penunjang Medis')],
            ['id'=>10,'unit_name'=>'Keperawatan','slug'=>\Illuminate\Support\Str::slug('Keperawatan')],
            ['id'=>11,'unit_name'=>'PKRS','slug'=>\Illuminate\Support\Str::slug('PKRS')],
            ['id'=>12,'unit_name'=>'PPI','slug'=>\Illuminate\Support\Str::slug('PPI')],
            ['id'=>13,'unit_name'=>'HD','slug'=>\Illuminate\Support\Str::slug('HD')],
            ['id'=>14,'unit_name'=>'Teratai','slug'=>\Illuminate\Support\Str::slug('Teratai')],
            ['id'=>15,'unit_name'=>'Anturium','slug'=>\Illuminate\Support\Str::slug('Anturium')],
            ['id'=>16,'unit_name'=>'Rosalina','slug'=>\Illuminate\Support\Str::slug('Rosalina')],
            ['id'=>17,'unit_name'=>'ICU','slug'=>\Illuminate\Support\Str::slug('ICU')],
            ['id'=>18,'unit_name'=>'Alamanda','slug'=>\Illuminate\Support\Str::slug('Alamanda')],
            ['id'=>19,'unit_name'=>'Perinatologi','slug'=>\Illuminate\Support\Str::slug('Perinatologi')],
            ['id'=>20,'unit_name'=>'OK','slug'=>\Illuminate\Support\Str::slug('OK')],
            ['id'=>21,'unit_name'=>'Mutu','slug'=>\Illuminate\Support\Str::slug('Mutu')],
            ['id'=>22,'unit_name'=>'Lotus','slug'=>\Illuminate\Support\Str::slug('Lotus')],
            ['id'=>23,'unit_name'=>'Tulip','slug'=>\Illuminate\Support\Str::slug('Tulip')],
            ['id'=>24,'unit_name'=>'Farmasi','slug'=>\Illuminate\Support\Str::slug('Farmasi')],
            ['id'=>25,'unit_name'=>'VK','slug'=>\Illuminate\Support\Str::slug('VK')],
            ['id'=>26,'unit_name'=>'Laboratorium','slug'=>\Illuminate\Support\Str::slug('Laboratorium')],
            ['id'=>27,'unit_name'=>'Rekam Medis','slug'=>\Illuminate\Support\Str::slug('Rekam Medis')],
            ['id'=>28,'unit_name'=>'Radiologi','slug'=>\Illuminate\Support\Str::slug('Radiologi')],
            ['id'=>29,'unit_name'=>'Gizi','slug'=>\Illuminate\Support\Str::slug('Gizi')],
            ['id'=>30,'unit_name'=>'Umum RT','slug'=>\Illuminate\Support\Str::slug('Umum RT')],
            ['id'=>31,'unit_name'=>'Umum Kepegawaian','slug'=>\Illuminate\Support\Str::slug('Umum Kepegawaian')],
            ['id'=>32,'unit_name'=>'TPP','slug'=>\Illuminate\Support\Str::slug('TPP')],
            ['id'=>33,'unit_name'=>'Informasi & Pengelolaan Pelanggan','slug'=>\Illuminate\Support\Str::slug('Informasi & Pengelolaan Pelanggan')],
            ['id'=>34,'unit_name'=>'Keuangan','slug'=>\Illuminate\Support\Str::slug('Keuangan')],
            ['id'=>35,'unit_name'=>'Akuntansi','slug'=>\Illuminate\Support\Str::slug('Akuntansi')],
            ['id'=>36,'unit_name'=>'Perpajakan','slug'=>\Illuminate\Support\Str::slug('Perpajakan')],
            ['id'=>37,'unit_name'=>'Sekretariat','slug'=>\Illuminate\Support\Str::slug('Sekretariat')],
            ['id'=>38,'unit_name'=>'Kasir','slug'=>\Illuminate\Support\Str::slug('Kasir')],
            ['id'=>39,'unit_name'=>'Transportasi','slug'=>\Illuminate\Support\Str::slug('Transportasi')],
            ['id'=>40,'unit_name'=>'Kebersihan','slug'=>\Illuminate\Support\Str::slug('Kebersihan')],
            ['id'=>41,'unit_name'=>'CSSD','slug'=>\Illuminate\Support\Str::slug('CSSD')],
            ['id'=>42,'unit_name'=>'Akunpuktur','slug'=>\Illuminate\Support\Str::slug('Akunpuktur')],
            ['id'=>43,'unit_name'=>'Kepegawaian Diklat','slug'=>\Illuminate\Support\Str::slug('Kepegawaian Diklat')],
            ['id'=>44,'unit_name'=>'Informasi & TIK','slug'=>\Illuminate\Support\Str::slug('Informasi & TIK')],
            ['id'=>45,'unit_name'=>'TI','slug'=>\Illuminate\Support\Str::slug('TI')],
            ['id'=>46,'unit_name'=>'Laundry','slug'=>\Illuminate\Support\Str::slug('Laundry')],
            ['id'=>47,'unit_name'=>'Keamanan','slug'=>\Illuminate\Support\Str::slug('Keamanan')],
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