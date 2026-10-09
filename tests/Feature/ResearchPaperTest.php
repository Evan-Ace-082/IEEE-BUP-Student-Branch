<?php

namespace Tests\Feature;

use App\Models\ResearchPaper;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResearchPaperTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_public_research_page_has_no_invented_papers_and_keeps_existing_navigation(): void
    {
        $response = $this->get(route('research-papers.index'));

        $response->assertOk();
        $response->assertSee('Research Papers');
        $response->assertSee('Submit Research Paper');
        $response->assertSee('No research papers have been published yet.');
        $response->assertSee('About Us');
        $response->assertSee('Resources');

        $home = $this->get(route('home'));
        $home->assertOk();
        $home->assertSee('Research Papers');
        $home->assertSee('About Us');
        $home->assertSee('Events');
        $home->assertDontSee('Faculty &amp; Club Leadership', false);

        $about = $this->get(route('about'));
        $about->assertOk();
        $about->assertSee('About the branch');
        $about->assertSee('Objectives');
        $about->assertSee('Faculty &amp; Club Leadership', false);
    }

    public function test_guest_is_sent_to_login_before_submitting_a_paper(): void
    {
        $this->get(route('research-papers.create'))->assertRedirect(route('login'));
    }

    public function test_submission_stays_hidden_until_an_admin_approves_and_publishes_it(): void
    {
        Storage::fake('local');
        $member = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $this->actingAs($member)->post(route('research-papers.store'), $this->paperPayload([
            'title' => 'Campus sensor network notes',
            'authors' => 'Author One',
            'keywords' => 'sensors, campus',
            'pdf' => $this->pdfUpload(),
        ]))->assertRedirect(route('member.dashboard'));

        $paper = ResearchPaper::query()->where('title', 'Campus sensor network notes')->first();
        $this->assertNotNull($paper);
        $this->assertSame('pending', $paper->review_status);
        $this->assertFalse($paper->is_published);
        Storage::disk('local')->assertExists($paper->pdf_path);

        auth()->logout();
        $this->get(route('research-papers.index'))->assertDontSee('Campus sensor network notes');
        $this->get(route('research-papers.show', $paper->slug))->assertNotFound();
        $this->get(route('research-papers.pdf', $paper->slug))->assertNotFound();

        $this->actingAs($member)->get(route('member.dashboard'))->assertSee('Pending Review');
        $this->actingAs($member)->get(route('research-papers.show', $paper->slug))->assertOk()->assertSee('Campus sensor network notes');

        $this->actingAs($member)->get(route('admin.research-papers.index'))->assertForbidden();
        $this->actingAs($member)->post(route('admin.research-papers.publish', $paper))->assertForbidden();

        $this->actingAs($admin)->post(route('admin.research-papers.publish', $paper))->assertRedirect()->assertSessionHasErrors('review_status');
        $this->actingAs($admin)->post(route('admin.research-papers.approve', $paper), [
            'review_note' => 'Approved for the branch archive.',
        ])->assertRedirect();

        $paper->refresh();
        $this->assertSame('approved', $paper->review_status);
        $this->assertFalse($paper->is_published);
        $this->assertSame($admin->id, $paper->reviewed_by);
        $this->assertNotNull($paper->reviewed_at);
        auth()->logout();
        $this->get(route('research-papers.index'))->assertDontSee('Campus sensor network notes');

        $this->actingAs($admin)->post(route('admin.research-papers.publish', $paper))->assertRedirect();
        $paper->refresh();
        $this->assertTrue($paper->is_published);

        $this->get(route('research-papers.index', ['q' => 'sensors']))
            ->assertOk()
            ->assertSee('Campus sensor network notes')
            ->assertSee('Author One');

        auth()->logout();
        $this->get(route('research-papers.show', $paper->slug))
            ->assertOk()
            ->assertSee('Campus sensor network notes')
            ->assertDontSee('Approved for the branch archive.');

        $download = $this->get(route('research-papers.pdf', ['paper' => $paper->slug, 'download' => 1]));
        $download->assertOk();
        $this->assertStringStartsWith('%PDF-', $download->streamedContent());

        $this->actingAs($admin)->post(route('admin.research-papers.reject', $paper), [
            'review_note' => 'Needs a clearer abstract.',
        ])->assertRedirect();

        auth()->logout();
        $this->get(route('research-papers.show', $paper->slug))->assertNotFound();
        $this->get(route('research-papers.pdf', $paper->slug))->assertNotFound();
        $this->actingAs($member)->get(route('member.dashboard'))->assertSee('Rejected');
    }

    public function test_only_a_real_pdf_can_be_submitted(): void
    {
        Storage::fake('local');
        $member = User::factory()->create();

        $this->actingAs($member)->post(route('research-papers.store'), $this->paperPayload([
            'pdf' => UploadedFile::fake()->createWithContent('paper.php.pdf', "<?php echo 'no';"),
        ]))->assertSessionHasErrors('pdf');

        $this->assertDatabaseCount('research_papers', 0);
    }

    public function test_admin_can_store_a_custom_leadership_position_with_a_department(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.leadership.store'), [
            'role' => 'other',
            'position_label' => 'Technical mentor',
            'name' => 'Profile Gamma',
            'designation' => 'Professor',
            'department' => 'Department of ICE',
            'bio' => 'A biography supplied by the branch.',
            'sort_order' => 3,
            'is_published' => '1',
        ])->assertRedirect();

        $about = $this->get(route('about'));
        $about->assertSee('Profile Gamma');
        $about->assertSee('Professor');
        $about->assertSee('Technical mentor');
        $about->assertSee('Department of ICE');
        $about->assertSee('Faculty &amp; Club Leadership', false);
        $about->assertSee('About the branch');
    }

    private function paperPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Untitled draft',
            'authors' => 'Author One',
            'abstract' => 'This abstract was entered on the submission form.',
            'category' => 'computing',
            'keywords' => 'testing',
            'publication_year' => 2026,
            'publication_status' => 'preprint',
            'pdf' => $this->pdfUpload(),
        ], $overrides);
    }

    private function pdfUpload(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('paper.pdf', "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF");
    }
}
