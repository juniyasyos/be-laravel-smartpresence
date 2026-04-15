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
                'username'=>'AdminRapat',
                'email'=>'admin@mail.com',
                'password'=>'password',
                'role_id'=>2,
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
            ['id'=>1,'employee_type'=>'tenaga_medis'],
            ['id'=>2,'employee_type'=>'tenaga_kesehatan'],
            ['id'=>3,'employee_type'=>'tenaga_non_medis']
        ]);

        /*
        |--------------------------------------------------------------------------
        | WORK UNITS
        |--------------------------------------------------------------------------
        */
        DB::table('work_units')->insert([
            ['id'=>1,'work_unit'=>'IGD'],
            ['id'=>2,'work_unit'=>'Rawat Inap'],
            ['id'=>3,'work_unit'=>'Administrasi']
        ]);




        /*
        |--------------------------------------------------------------------------
        | EMPLOYEES
        |--------------------------------------------------------------------------
        */

        DB::table('employees')->insert([
            [
                'full_name'=>'Dr. Budi Santoso',
                'nip'=>'EMP001',
                'employee_type_id'=> 1,
                'work_unit_id'=> 1,
                'email'=>'budi@hospital.com',
                'phone'=>'08123456789',
                'is_active'=>true,
                'created_at'=>now(),
                'updated_at'=>now()
            ],
            [
                'full_name'=>'Siti Aminah',
                'nip'=>'EMP002',
                'employee_type_id'=> 2,
                'work_unit_id'=> 2,
                'email'=>'siti@hospital.com',
                'phone'=>'08123456788',
                'is_active'=>true,
                'created_at'=>now(),
                'updated_at'=>now()
            ],
            [
                'full_name'=>'Andi Pratama',
                'nip'=>'EMP003',
                'employee_type_id'=> 3,
                'work_unit_id'=> 3,
                'email'=>'andi@hospital.com',
                'phone'=>'08123456787',
                'is_active'=>true,
                'created_at'=>now(),
                'updated_at'=>now()
            ],
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
            [
                'meeting_id'=>1,
                'employee_id'=>3,
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


        /*
        |--------------------------------------------------------------------------
        | MEETING DOCUMENTS
        |--------------------------------------------------------------------------
        */

        DB::table('meeting_documents')->insert([
            [
                'meeting_id'=>1,
                'type'=>'minutes',
                'file_name'=>'notulen_rapat.pdf',
                'file_path'=>'documents/notulen_rapat.pdf',
                'file_size'=>120000,
                'mime_type'=>'application/pdf',

                'director_name'=>'Dr. Agus Setiawan',
                'director_position'=>'Direktur Rumah Sakit',
                'director_signed_at'=>Carbon::now(),

                'notulis_name'=>'Admin Rapat',
                'notulis_position'=>'Staff Administrasi',
                'notulis_signed_at'=>Carbon::now(),

                'uploaded_by'=>2,
                'created_at'=>now(),
                'updated_at'=>now()
            ]
        ]);
    }
}