<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\WorkUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WorkUnitApiTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();
        
        config(['iam.app_env' => 'local']);
        
        DB::table('roles')->insert(['id' => 1, 'role' => 'super_admin']);
        $adminRole = Role::find(1);
        
        $this->adminUser = User::factory()->hasAttached($adminRole)->create([
            'status' => 'active',
        ]);
        
        $this->token = $this->adminUser->createToken('auth_token')->plainTextToken;
    }

    public function test_can_fetch_work_units()
    {
        WorkUnit::create(['unit_name' => 'Puskesmas A']);
        WorkUnit::create(['unit_name' => 'Puskesmas B']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/work-units-manage');

        $response->assertStatus(200)
                 ->assertJsonPath('data.data.0.unit_name', 'Puskesmas A')
                 ->assertJsonPath('data.total', 2);
    }

    public function test_can_create_work_unit()
    {
        $payload = ['unit_name' => 'Dinas Kesehatan'];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->postJson('/api/work-unit', $payload);

        $response->assertStatus(201)
                 ->assertJsonPath('data.unit_name', 'Dinas Kesehatan');

        $this->assertDatabaseHas('work_units', ['unit_name' => 'Dinas Kesehatan']);
    }

    public function test_can_show_work_unit()
    {
        $unit = WorkUnit::create(['unit_name' => 'Puskesmas C']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->getJson('/api/work-unit/' . $unit->id);

        $response->assertStatus(200)
                 ->assertJsonPath('data.unit_name', 'Puskesmas C');
    }

    public function test_can_update_work_unit()
    {
        $unit = WorkUnit::create(['unit_name' => 'Old Unit']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->patchJson('/api/work-unit/' . $unit->id, [
            'unit_name' => 'New Unit'
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.unit_name', 'New Unit');

        $this->assertDatabaseHas('work_units', ['unit_name' => 'New Unit']);
    }

    public function test_can_delete_work_unit()
    {
        $unit = WorkUnit::create(['unit_name' => 'To Delete']);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->deleteJson('/api/work-unit/' . $unit->id);

        $response->assertStatus(200);

        $this->assertSoftDeleted('work_units', ['id' => $unit->id]);
    }
}
