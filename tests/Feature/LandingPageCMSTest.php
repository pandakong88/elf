<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Modules\Core\Models\LandingPageContent;
use App\Livewire\System\LandingPageCMS;
use App\Livewire\Public\LandingPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class LandingPageCMSTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $guestUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed();

        $this->adminUser = User::where('email', 'admin@elvith.id')->firstOrFail();
        
        // Create a simple active user with no admin roles
        $this->guestUser = User::factory()->create([
            'email' => 'santri.parent@example.com',
            'username' => 'parent123',
            'is_active' => true,
        ]);
        $this->guestUser->assignRole('wali-santri');
    }

    public function test_guest_can_access_landing_page(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Al-Fithroh');
        $response->assertSee('Mencetak Generasi Agamis');
    }

    public function test_unauthorized_user_cannot_access_cms(): void
    {
        // Guests/wali-santri should get 403
        $response = $this->actingAs($this->guestUser)->get('/system/cms');
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_cms(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/system/cms');
        $response->assertStatus(200);
        $response->assertSeeLivewire(LandingPageCMS::class);
    }

    public function test_admin_can_update_landing_page_contents(): void
    {
        Livewire::actingAs($this->adminUser)
            ->test(LandingPageCMS::class)
            ->set('hero_title', 'Judul Baru Landing Page Al-Fithroh')
            ->set('hero_subtitle', 'Deskripsi sub-judul baru Al-Fithroh')
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('toast-show', type: 'success', message: 'Konten landing page berhasil diperbarui.');

        $this->assertDatabaseHas('landing_page_contents', [
            'key' => 'hero_title',
            'value' => 'Judul Baru Landing Page Al-Fithroh',
        ]);
    }
}
