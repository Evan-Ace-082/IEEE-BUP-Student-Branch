<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Event;
use App\Models\LeadershipProfile;
use App\Models\PageContent;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LeadershipAndContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_about_page_shows_leadership_placeholders_without_invented_people(): void
    {
        $response = $this->get(route('about'));

        $response->assertOk();
        $response->assertSee('About the branch');
        $response->assertSee('Faculty & Club Leadership');
        $response->assertSee('Chairman / Faculty Advisor');
        $response->assertSee('Moderator');
        $response->assertSee('Co-Moderator');
        $response->assertSee('To be announced');
        $response->assertDontSee('Chairman</h3>', false);
        $this->assertSame(3, substr_count($response->getContent(), 'To be announced'));
    }

    public function test_homepage_and_public_navigation_do_not_gain_a_leadership_item(): void
    {
        $home = $this->get(route('home'));
        $home->assertOk();
        $home->assertDontSee('Faculty & Club Leadership');
        $home->assertDontSee('Faculty leadership');

        $this->get(route('about'))
            ->assertDontSee('href="'.route('admin.leadership.index').'"', false);
    }

    public function test_member_cannot_manage_leadership_profiles(): void
    {
        $member = User::factory()->create();

        $this->actingAs($member)->get(route('admin.leadership.index'))->assertForbidden();
        $this->actingAs($member)->post(route('admin.leadership.store'), $this->profilePayload())->assertForbidden();
        $this->assertDatabaseCount('leadership_profiles', 0);
    }

    public function test_admin_can_publish_reorder_hide_contact_and_delete_a_profile(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.leadership.store'), $this->profilePayload([
            'role' => 'moderator',
            'name' => 'Profile Alpha',
            'sort_order' => 2,
            'is_published' => '0',
            'email' => 'alpha@example.com',
            'show_email' => '1',
        ]))->assertRedirect();

        $profile = LeadershipProfile::query()->where('name', 'Profile Alpha')->first();
        $this->assertNotNull($profile);
        $this->assertFalse($profile->is_published);

        $this->get(route('about'))->assertDontSee('Profile Alpha');

        $this->actingAs($admin)->put(route('admin.leadership.update', $profile), $this->profilePayload([
            'role' => 'chairman',
            'name' => 'Profile Alpha',
            'designation' => 'Confirmed designation',
            'sort_order' => 1,
            'is_published' => '1',
            'email' => 'alpha@example.com',
            'phone' => '01700000000',
            'show_email' => '0',
            'show_phone' => '1',
        ]))->assertRedirect();

        $this->actingAs($admin)->post(route('admin.leadership.store'), $this->profilePayload([
            'role' => 'faculty_advisor',
            'name' => 'Profile Beta',
            'sort_order' => 5,
            'is_published' => '1',
        ]))->assertRedirect();

        $about = $this->get(route('about'));
        $about->assertSee('Profile Alpha');
        $about->assertSee('Profile Beta');
        $about->assertSee('Confirmed designation');
        $about->assertSee('01700000000');
        $about->assertDontSee('alpha@example.com');
        $about->assertDontSee('Chairman / Faculty Advisor');
        $this->assertLessThan(
            strpos($about->getContent(), 'Profile Beta'),
            strpos($about->getContent(), 'Profile Alpha')
        );

        $this->actingAs($admin)->delete(route('admin.leadership.destroy', $profile))->assertRedirect(route('admin.leadership.index'));
        $this->assertDatabaseMissing('leadership_profiles', ['name' => 'Profile Alpha']);
        $this->get(route('about'))->assertDontSee('Profile Alpha')->assertSee('Chairman');
    }

    public function test_admin_photo_upload_replaces_and_rejects_a_script(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.leadership.store'), $this->profilePayload([
            'photo' => UploadedFile::fake()->image('portrait.jpg', 40, 40),
        ]))->assertRedirect();

        $profile = LeadershipProfile::query()->first();
        $this->assertNotNull($profile->photo);
        Storage::disk('public')->assertExists($profile->photo);

        $this->actingAs($admin)->put(route('admin.leadership.update', $profile), $this->profilePayload([
            'photo' => UploadedFile::fake()->create('portrait.php', 20, 'image/jpeg'),
        ]))->assertSessionHasErrors('photo');

        $profile->refresh();
        $this->assertStringEndsNotWith('.php', $profile->photo);
    }

    public function test_admin_can_edit_about_text_and_existing_events_and_announcements(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->put(route('admin.content.update'), [
            'bodies' => [
                'about.branch' => 'Updated branch description for the test.',
                'about.leadership' => 'Updated leadership introduction.',
            ],
        ])->assertRedirect();

        $this->assertDatabaseHas('page_contents', [
            'key' => 'about.branch',
            'body' => 'Updated branch description for the test.',
        ]);
        $this->assertSame('Updated branch description for the test.', PageContent::query()->where('key', 'about.branch')->value('body'));

        $this->get(route('about'))
            ->assertSee('Updated branch description for the test.')
            ->assertSee('Updated leadership introduction.');

        $this->actingAs($admin)->post(route('admin.events.store'), [
            'title' => 'Existing event editor check',
            'description' => 'Confirms the current event form still saves.',
            'start_date' => '2026-11-02',
            'end_date' => '2026-11-02',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'venue' => '',
            'speaker' => '',
            'organizer' => '',
            'category' => 'workshop',
            'status' => 'published',
            'registration_deadline' => '',
        ])->assertRedirect();

        $event = Event::query()->where('title', 'Existing event editor check')->first();
        $this->assertNotNull($event);
        $this->assertSame('published', $event->status);

        $this->actingAs($admin)->put(route('admin.events.update', $event), [
            'title' => 'Existing event editor check',
            'description' => 'Confirms the current event form still saves.',
            'start_date' => '2026-11-02',
            'end_date' => '2026-11-02',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'venue' => 'Campus hall',
            'speaker' => '',
            'organizer' => '',
            'category' => 'seminar',
            'status' => 'draft',
            'registration_deadline' => '',
        ])->assertRedirect();
        $this->assertSame('draft', $event->fresh()->status);

        $this->actingAs($admin)->post(route('admin.announcements.store'), [
            'title' => 'Existing announcement editor check',
            'content' => 'Confirms the current announcement form still saves.',
            'category' => 'news',
            'status' => 'published',
        ])->assertRedirect();

        $announcement = Announcement::query()->where('title', 'Existing announcement editor check')->first();
        $this->assertNotNull($announcement);
        $this->assertSame('published', $announcement->status);

        $this->actingAs($admin)->put(route('admin.announcements.update', $announcement), [
            'title' => 'Existing announcement editor check',
            'content' => 'Updated announcement body.',
            'category' => 'news',
            'status' => 'archived',
        ])->assertRedirect();
        $this->assertSame('archived', $announcement->fresh()->status);
    }

    private function profilePayload(array $overrides = []): array
    {
        return array_merge([
            'role' => 'moderator',
            'name' => 'Profile Alpha',
            'designation' => '',
            'bio' => 'A short biography supplied by the branch.',
            'email' => '',
            'phone' => '',
            'sort_order' => 1,
            'is_published' => '1',
        ], $overrides);
    }
}
