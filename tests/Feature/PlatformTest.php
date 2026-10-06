<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PlatformTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_home_page_loads(): void
    {
        $this->get('/')->assertOk()->assertSee('BUP IEEE Student Branch');
    }

    public function test_guest_is_redirected_from_member_dashboard(): void
    {
        $this->get(route('member.dashboard'))->assertRedirect(route('login'));
    }

    public function test_member_cannot_open_admin_dashboard(): void
    {
        $member = User::factory()->create();
        $this->actingAs($member)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_cannot_open_super_admin_routes(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('super.admins.index'))->assertForbidden();
        $this->actingAs($admin)->post(route('super.admins.store'), [
            'name' => 'Sneaky',
            'email' => 'sneaky@example.com',
            'password' => 'ChangeMe!2026',
            'password_confirmation' => 'ChangeMe!2026',
            'status' => 'active',
            'role' => 'super_admin',
        ])->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'sneaky@example.com']);
    }

    public function test_super_admin_creates_an_admin_and_cannot_assign_super_admin(): void
    {
        $super = User::factory()->superAdmin()->create();

        $this->actingAs($super)->post(route('super.admins.store'), [
            'name' => 'New Admin',
            'email' => 'new-admin@example.com',
            'password' => 'ChangeMe!2026',
            'password_confirmation' => 'ChangeMe!2026',
            'status' => 'active',
            'role' => 'super_admin',
        ])->assertRedirect();

        $created = User::query()->where('email', 'new-admin@example.com')->first();
        $this->assertNotNull($created);
        $this->assertTrue($created->isAdmin());
        $this->assertFalse($created->isSuperAdmin());
        $this->assertTrue(Hash::check('ChangeMe!2026', $created->password));
    }

    public function test_member_cannot_change_another_members_profile(): void
    {
        $actor = User::factory()->create(['name' => 'Actor']);
        $actor->profile()->create(['directory_visible' => true]);
        $other = User::factory()->create(['name' => 'Other Person']);
        $other->profile()->create(['directory_visible' => true]);

        $this->actingAs($actor)->put(route('member.profile.update'), [
            'name' => 'Actor Updated',
            'user_id' => $other->id,
            'role' => 'super_admin',
            'status' => 'active',
            'ieee_membership_status' => 'student',
            'directory_visible' => '1',
            'show_social' => '1',
        ])->assertRedirect();

        $this->assertSame('Actor Updated', $actor->fresh()->name);
        $this->assertSame('Other Person', $other->fresh()->name);
        $this->assertTrue($actor->fresh()->isMember());
    }

    public function test_duplicate_event_registration_is_rejected(): void
    {
        $event = Event::query()->create([
            'title' => 'Open Workshop',
            'slug' => 'open-workshop',
            'description' => 'A workshop.',
            'starts_at' => now()->addDays(3),
            'ends_at' => now()->addDays(3)->addHours(2),
            'category' => 'workshop',
            'registration_enabled' => true,
            'registration_deadline' => now()->addDays(2),
            'status' => 'published',
        ]);

        $payload = [
            'name' => 'Student',
            'email' => 'student@example.com',
            'ieee_membership_status' => 'none',
            'human_answer' => '4',
        ];

        $this->withSession(['human_check' => 4])->post(route('events.register', $event), $payload)->assertRedirect();
        $this->assertDatabaseCount('event_registrations', 1);

        $this->withSession(['human_check' => 4])
            ->from(route('events.show', $event))
            ->post(route('events.register', $event), $payload)
            ->assertRedirect(route('events.show', $event))
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('event_registrations', 1);
    }

    public function test_malicious_profile_upload_is_rejected(): void
    {
        Storage::fake('public');
        $member = User::factory()->create();
        $member->profile()->create([]);

        $file = UploadedFile::fake()->createWithContent('photo.jpg', '<?php echo "nope";');

        $this->actingAs($member)->put(route('member.profile.update'), [
            'name' => $member->name,
            'ieee_membership_status' => 'none',
            'directory_visible' => '1',
            'photo' => $file,
        ])->assertSessionHasErrors();

        $this->assertNull($member->profile()->first()->photo);
    }

    public function test_pending_member_cannot_open_dashboard(): void
    {
        $member = User::factory()->pending()->create();
        $this->actingAs($member)->get(route('member.dashboard'))->assertRedirect(route('account.pending'));
    }

    public function test_suspended_admin_is_signed_out(): void
    {
        $admin = User::factory()->admin()->suspended()->create();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_contact_message_is_stored(): void
    {
        $this->withSession(['human_check' => 9])->post(route('contact.store'), [
            'name' => 'Visitor',
            'email' => 'visitor@example.com',
            'subject' => 'Hello',
            'message' => 'Is the workshop on campus?',
            'human_answer' => '9',
        ])->assertRedirect(route('contact'));

        $this->assertDatabaseHas('contact_messages', ['email' => 'visitor@example.com', 'status' => 'unread']);
    }

    public function test_missing_event_returns_not_found(): void
    {
        $this->get('/events/does-not-exist')->assertNotFound();
    }

    public function test_roles_exist(): void
    {
        $this->assertSame(3, Role::query()->count());
    }
}
