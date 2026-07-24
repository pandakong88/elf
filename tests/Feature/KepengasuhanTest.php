<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Core\Models\MasterData;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Person;
use App\Modules\Core\Models\Position;
use App\Modules\Core\Models\WorkflowStep;
use App\Modules\Core\Models\WorkflowTemplate;
use App\Modules\Kepengasuhan\Models\Dormitory;
use App\Modules\Kepengasuhan\Models\Room;
use App\Modules\Kepengasuhan\Models\RoomAssignment;
use App\Modules\Kepengasuhan\Models\Perizinan;
use App\Modules\Kepengasuhan\Models\Violation;
use App\Modules\Kepengasuhan\Models\Activity;
use App\Modules\Kepengasuhan\Models\ActivityAttendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KepengasuhanTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private string $authToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->adminUser = User::where('email', 'admin@elvith.id')->firstOrFail();
        $this->authToken = auth('api')->login($this->adminUser);
    }

    public function test_room_assignment_capacity_limit(): void
    {
        $dormitory = Dormitory::firstOrFail();
        
        // Buat kamar dengan kapasitas 1 orang
        $room = Room::create([
            'id'           => \Illuminate\Support\Str::uuid()->toString(),
            'dormitory_id' => $dormitory->id,
            'name'         => 'Kamar Uji Kapasitas',
            'capacity'     => 1,
            'is_active'    => true,
        ]);

        $santri = Person::where('gender', $dormitory->gender)->get();
        $this->assertGreaterThanOrEqual(2, $santri->count());

        $headers = ['Authorization' => 'Bearer ' . $this->authToken];

        // Assign santri pertama -> Sukses
        $response1 = $this->postJson("/api/v1/kepengasuhan/rooms/{$room->id}/assign", [
            'person_id'  => $santri[0]->id,
            'valid_from' => now()->toDateString(),
        ], $headers);

        $response1->assertStatus(201);

        // Assign santri kedua -> Gagal (Penuh)
        $response2 = $this->postJson("/api/v1/kepengasuhan/rooms/{$room->id}/assign", [
            'person_id'  => $santri[1]->id,
            'valid_from' => now()->toDateString(),
        ], $headers);

        $response2->assertStatus(422)
            ->assertJson([
                'message' => "Kamar '{$room->name}' sudah penuh (kapasitas: 1)."
            ]);
    }

    public function test_perizinan_workflow_initiation_and_blocks(): void
    {
        $santri = Person::where('gender', 'L')->firstOrFail();
        $putraOrg = Organization::where('slug', 'kepengasuhan-putra')->firstOrFail();
        $type = MasterData::where('category', 'jenis_izin')->firstOrFail();
        $template = WorkflowTemplate::where('organization_id', $putraOrg->id)->where('entity_type', 'perizinan')->firstOrFail();

        $headers = ['Authorization' => 'Bearer ' . $this->authToken];

        // 1. Ajukan izin pertama -> Sukses (status pending)
        $response = $this->postJson('/api/v1/kepengasuhan/perizinan', [
            'person_id'            => $santri->id,
            'organization_id'      => $putraOrg->id,
            'permission_type_id'   => $type->id,
            'reason'               => 'Sakit butuh rawat jalan',
            'start_date'           => now()->toIso8601String(),
            'end_date'             => now()->addDays(2)->toIso8601String(),
            'workflow_template_id' => $template->id,
            'prevent_duplicate'    => true,
        ], $headers);

        $response->assertStatus(201);
        $perizinanId = $response->json('data.id');

        // Cek database
        $perizinan = Perizinan::findOrFail($perizinanId);
        $this->assertEquals('pending', $perizinan->status);

        // 2. Coba checkout saat masih pending -> Gagal
        $checkoutResponse1 = $this->postJson("/api/v1/kepengasuhan/perizinan/{$perizinanId}/checkout", [], $headers);
        $checkoutResponse1->assertStatus(422)
            ->assertJson([
                'message' => 'Izin belum disetujui (workflow masih pending/in progress).'
            ]);

        // 3. Setujui langkah workflow (step 1 & 2) agar status izin menjadi approved
        $instance = $perizinan->workflowInstance;
        $musyrif = Person::where('name', '!=', 'Administrator')->firstOrFail(); // actor pendukung
        
        $engine = app(\App\Modules\Shared\Workflow\WorkflowEngine::class);
        $engine->advance($instance, $this->adminUser->person); // step 1 approved
        $engine->advance($instance, $this->adminUser->person); // step 2 approved -> workflow status: approved

        // 4. Checkout setelah disetujui -> Sukses (status out)
        $checkoutResponse2 = $this->postJson("/api/v1/kepengasuhan/perizinan/{$perizinanId}/checkout", [], $headers);
        $checkoutResponse2->assertStatus(200);
        $this->assertEquals('out', $checkoutResponse2->json('data.status'));

        // 5. Ajukan izin baru saat status 'out' -> Gagal (Duplikasi izin)
        $duplicateResponse = $this->postJson('/api/v1/kepengasuhan/perizinan', [
            'person_id'            => $santri->id,
            'organization_id'      => $putraOrg->id,
            'permission_type_id'   => $type->id,
            'reason'               => 'Izin kedua',
            'start_date'           => now()->toIso8601String(),
            'end_date'             => now()->addDays(2)->toIso8601String(),
            'workflow_template_id' => $template->id,
            'prevent_duplicate'    => true,
        ], $headers);

        $duplicateResponse->assertStatus(422)
            ->assertJson([
                'message' => "Santri '{$santri->name}' sedang berada di luar pondok dengan izin aktif."
            ]);

        // 6. Checkin kembali -> Sukses (status returned/late)
        $checkinResponse = $this->postJson("/api/v1/kepengasuhan/perizinan/{$perizinanId}/checkin", [
            'notes' => 'Kembali sehat',
        ], $headers);

        $checkinResponse->assertStatus(200);
        $this->assertContains($checkinResponse->json('data.status'), ['returned', 'late']);
    }

    public function test_violations_and_resolution(): void
    {
        $santri = Person::where('gender', 'L')->firstOrFail();
        $putraOrg = Organization::where('slug', 'kepengasuhan-putra')->firstOrFail();
        $type = MasterData::where('category', 'jenis_pelanggaran')->firstOrFail();

        $headers = ['Authorization' => 'Bearer ' . $this->authToken];

        // 1. Lapor pelanggaran
        $response = $this->postJson('/api/v1/kepengasuhan/violations', [
            'person_id'         => $santri->id,
            'organization_id'   => $putraOrg->id,
            'violation_type_id' => $type->id,
            'description'       => 'Terlambat shalat subuh berjamaah',
            'severity'          => 'ringan',
            'points'            => 10,
        ], $headers);

        $response->assertStatus(201);
        $violationId = $response->json('data.id');

        // Cek poin kumulatif
        $service = app(\App\Modules\Kepengasuhan\Services\ViolationService::class);
        $this->assertEquals(10, $service->getCumulativePoints($santri->id));

        // 2. Selesaikan pelanggaran (resolve)
        $resolveResponse = $this->postJson("/api/v1/kepengasuhan/violations/{$violationId}/resolve", [
            'punishment_applied' => 'Membaca Al-Qur\'an 1 Juz di masjid',
        ], $headers);

        $resolveResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'resolved')
            ->assertJsonPath('data.punishment', 'Membaca Al-Qur\'an 1 Juz di masjid');

        // Poin kumulatif harus reset menjadi 0 karena statusnya 'resolved'
        $this->assertEquals(0, $service->getCumulativePoints($santri->id));
    }

    public function test_activity_creation_and_batch_attendance(): void
    {
        $putraOrg = Organization::where('slug', 'kepengasuhan-putra')->firstOrFail();
        $type = MasterData::where('category', 'jenis_kegiatan')->firstOrFail();
        
        $santri = Person::where('gender', 'L')->take(3)->get();
        $this->assertEquals(3, $santri->count());

        $headers = ['Authorization' => 'Bearer ' . $this->authToken];

        // 1. Buat kegiatan
        $activityResponse = $this->postJson('/api/v1/kepengasuhan/activities', [
            'organization_id'  => $putraOrg->id,
            'activity_type_id' => $type->id,
            'name'             => 'Kajian Kitab Bulanan',
            'date'             => now()->toDateString(),
            'description'      => 'Kajian rutin kepengasuhan putra',
        ], $headers);

        $activityResponse->assertStatus(201);
        $activityId = $activityResponse->json('data.id');

        // 2. Input bulk absensi
        $attendanceResponse = $this->postJson("/api/v1/kepengasuhan/activities/{$activityId}/attendance", [
            'attendances' => [
                ['person_id' => $santri[0]->id, 'status' => 'hadir', 'notes' => 'Tepat waktu'],
                ['person_id' => $santri[1]->id, 'status' => 'izin', 'notes' => 'Sakit di uks'],
                ['person_id' => $santri[2]->id, 'status' => 'hadir', 'notes' => 'Tepat waktu'],
            ],
        ], $headers);

        $attendanceResponse->assertStatus(200)
            ->assertJson(['message' => 'Absensi berhasil dicatat.']);

        // Cek database
        $this->assertEquals(3, ActivityAttendance::where('activity_id', $activityId)->count());
        $this->assertEquals(2, ActivityAttendance::where('activity_id', $activityId)->where('status', 'hadir')->count());
        $this->assertEquals(1, ActivityAttendance::where('activity_id', $activityId)->where('status', 'izin')->count());
    }
}
