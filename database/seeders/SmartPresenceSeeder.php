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
                'username'=>'SuperAdmin',
                'email'=>'superadmin@mail.com',
                'password'=>'password',
                'role_id'=>1,
                'is_active'=>true,
                'created_at'=>now(),
                'updated_at'=>now()
            ],
            [
                'username'=>'Admin',
                'email'=>'admin@mail.com',
                'password'=>'password',
                'role_id'=>2,
                'is_active'=>true,
                'created_at'=>now(),
                'updated_at'=>now()
            ],
            [
                'username'=>'Sekretaris',
                'email'=>'sekretaris@mail.com',
                'password'=>'password',
                'role_id'=>3,
                'is_active'=>true,
                'created_at'=>now(),
                'updated_at'=>now()
            ]
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
            ['id'=>20,'employee_type'=>'TTK']

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