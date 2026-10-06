<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Announcement;
use App\Models\CommitteeMember;
use App\Models\CommitteePosition;
use App\Models\ContactMessage;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\ExecutiveCommittee;
use App\Models\GalleryAlbum;
use App\Models\GalleryCategory;
use App\Models\GalleryItem;
use App\Models\PageContent;
use App\Models\Resource;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Support\SettingsStore;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->seedSettings();
        $this->seedContent();

        if (app()->environment('production') && ! env('SEED_ALLOW')) {
            $this->command?->warn('Demo accounts were skipped because APP_ENV is production.');

            return;
        }

        $super = $this->account('Super Admin', 'superadmin@bupieee.test', 'ChangeMe!Super2026', 'super_admin');
        $admin = $this->account('Branch Admin', 'admin@bupieee.test', 'ChangeMe!Admin2026', 'admin');
        $member = $this->account('Ayesha Rahman', 'member@bupieee.test', 'ChangeMe!Member2026', 'member', [
            'student_id' => '221-15-1001',
            'department' => 'Information and Communication Engineering',
            'batch' => '2022',
            'session' => '2021-22',
            'ieee_membership_status' => 'student',
            'skills' => ['Python', 'Public speaking'],
            'interests' => ['Embedded systems', 'Workshops'],
            'bio' => 'Student member who helps with technical sessions.',
            'show_social' => true,
        ]);
        $this->account('Rafiul Hasan', 'rafiul@bupieee.test', 'ChangeMe!Member2026', 'member', [
            'student_id' => '221-15-1044',
            'department' => 'Computer Science and Engineering',
            'batch' => '2023',
            'ieee_membership_status' => 'none',
            'skills' => ['Web', 'Design'],
            'interests' => ['Hackathons'],
            'bio' => 'Interested in student competitions.',
        ]);
        $this->account('Pending Student', 'pending@bupieee.test', 'ChangeMe!Pending2026', 'member', [], 'pending');

        $positions = [];
        foreach ([
            'Chair' => 1,
            'Vice Chair' => 2,
            'Secretary' => 3,
            'Treasurer' => 4,
            'Organizing Secretary' => 5,
            'Technical Lead' => 6,
            'Membership Coordinator' => 7,
            'Publicity Lead' => 8,
            'Executive Member' => 9,
        ] as $name => $order) {
            $positions[$name] = CommitteePosition::query()->updateOrCreate(
                ['slug' => str($name)->slug()],
                ['name' => $name, 'sort_order' => $order]
            );
        }

        $current = ExecutiveCommittee::query()->updateOrCreate(
            ['year' => 2026],
            ['name' => '2026 Executive Committee', 'is_current' => true, 'description' => 'The committee currently published on the website.']
        );
        ExecutiveCommittee::query()->where('id', '!=', $current->id)->update(['is_current' => false]);
        $previous = ExecutiveCommittee::query()->updateOrCreate(
            ['year' => 2025],
            ['name' => '2025 Executive Committee', 'is_current' => false, 'description' => 'Previous term, kept for the committee archive.']
        );

        $this->officer($current, $positions['Chair'], 'Nusrat Jahan', 'ICE', '2022');
        $this->officer($current, $positions['Vice Chair'], 'Mahir Rahman', 'CSE', '2022');
        $this->officer($current, $positions['Secretary'], 'Ayesha Rahman', 'ICE', '2022');
        $this->officer($current, $positions['Treasurer'], 'Farhan Kabir', 'EEE', '2023');
        $this->officer($previous, $positions['Chair'], 'Sadia Islam', 'CSE', '2021');
        $this->officer($previous, $positions['Secretary'], 'Tanvir Ahmed', 'ICE', '2021');

        $workshopBanner = $this->image('events/workshop-banner.jpg', 'Workshop', [0, 78, 124]);
        $seminarBanner = $this->image('events/seminar-banner.jpg', 'Seminar', [12, 44, 86]);
        $pastBanner = $this->image('events/hackathon-banner.jpg', 'Hackathon', [90, 62, 20]);

        $workshop = Event::query()->updateOrCreate(['slug' => 'git-and-collaboration-workshop'], [
            'title' => 'Git and Collaboration Workshop',
            'description' => "A hands-on session on version control for student projects.\nBring a laptop if you want to follow along.",
            'banner' => $workshopBanner,
            'starts_at' => now()->addDays(12)->setTime(15, 0),
            'ends_at' => now()->addDays(12)->setTime(17, 30),
            'venue' => 'BUP campus, room to be confirmed',
            'speaker' => 'Branch technical team',
            'organizer' => 'BUP IEEE Student Branch',
            'category' => 'workshop',
            'registration_enabled' => true,
            'registration_deadline' => now()->addDays(11),
            'status' => 'published',
            'is_featured' => true,
            'created_by' => $admin->id,
        ]);

        Event::query()->updateOrCreate(['slug' => 'industry-conversation'], [
            'title' => 'Industry Conversation',
            'description' => "An informal seminar with graduates working in technology.\nQuestions from students are welcome.",
            'banner' => $seminarBanner,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addHours(3),
            'venue' => 'Online',
            'speaker' => 'Guest alumni',
            'organizer' => 'BUP IEEE Student Branch',
            'category' => 'seminar',
            'registration_enabled' => false,
            'status' => 'published',
            'created_by' => $admin->id,
        ]);

        $past = Event::query()->updateOrCreate(['slug' => 'campus-project-showcase'], [
            'title' => 'Campus Project Showcase',
            'description' => 'Students presented course and personal projects to their peers.',
            'banner' => $pastBanner,
            'starts_at' => now()->subMonths(2)->setTime(10, 0),
            'ends_at' => now()->subMonths(2)->setTime(16, 0),
            'venue' => 'BUP campus',
            'organizer' => 'BUP IEEE Student Branch',
            'category' => 'competition',
            'registration_enabled' => false,
            'status' => 'published',
            'created_by' => $admin->id,
        ]);

        EventRegistration::query()->updateOrCreate(
            ['event_id' => $workshop->id, 'email' => $member->email],
            [
                'user_id' => $member->id,
                'name' => $member->name,
                'student_id' => '221-15-1001',
                'department' => 'Information and Communication Engineering',
                'batch' => '2022',
                'ieee_membership_status' => 'student',
                'status' => 'registered',
            ]
        );

        Announcement::query()->updateOrCreate(['slug' => 'workshop-registration-open'], [
            'title' => 'Workshop registration is open',
            'content' => "Registration is open for the Git and Collaboration Workshop.\nSeats are limited to the room we can use.",
            'author_id' => $admin->id,
            'category' => 'event_notice',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
        Announcement::query()->updateOrCreate(['slug' => 'general-member-call'], [
            'title' => 'Call for general members',
            'content' => 'Students who want to volunteer with the branch can submit the interest form on the membership page.',
            'author_id' => $admin->id,
            'category' => 'recruitment',
            'status' => 'published',
            'published_at' => now()->subDays(3),
        ]);
        Announcement::query()->updateOrCreate(['slug' => 'branch-update'], [
            'title' => 'Branch website is live',
            'content' => 'Events, the committee, and announcements will be updated from the admin panel.',
            'author_id' => $super->id,
            'category' => 'news',
            'status' => 'published',
            'published_at' => now()->subDays(6),
        ]);
        Announcement::query()->updateOrCreate(['slug' => 'draft-notice'], [
            'title' => 'Draft notice',
            'content' => 'This draft should not appear on the public site.',
            'author_id' => $admin->id,
            'category' => 'important',
            'status' => 'draft',
        ]);

        Achievement::query()->updateOrCreate(['title' => 'Project showcase participation'], [
            'description' => 'Branch volunteers helped run the campus project showcase.',
            'person_or_team' => 'BUP IEEE Student Branch',
            'category' => 'branch',
            'achieved_on' => now()->subMonths(2)->toDateString(),
            'status' => 'published',
            'is_featured' => true,
            'submitted_by' => $member->id,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now()->subMonth(),
        ]);
        Achievement::query()->updateOrCreate(['title' => 'Hackathon team mention'], [
            'description' => 'A student team asked the branch to record their hackathon result. Waiting for review.',
            'person_or_team' => 'Team North',
            'category' => 'hackathon',
            'achieved_on' => now()->subWeeks(3)->toDateString(),
            'status' => 'pending',
            'submitted_by' => $member->id,
        ]);

        $eventsCategory = GalleryCategory::query()->updateOrCreate(['slug' => 'events'], ['name' => 'Events']);
        GalleryCategory::query()->updateOrCreate(['slug' => 'workshops'], ['name' => 'Workshops']);
        GalleryCategory::query()->updateOrCreate(['slug' => 'seminars'], ['name' => 'Seminars']);
        GalleryCategory::query()->updateOrCreate(['slug' => 'competitions'], ['name' => 'Competitions']);
        GalleryCategory::query()->updateOrCreate(['slug' => 'team'], ['name' => 'Team Activities']);
        GalleryCategory::query()->updateOrCreate(['slug' => 'other'], ['name' => 'Other Activities']);

        $album = GalleryAlbum::query()->updateOrCreate(['slug' => 'project-showcase-album'], [
            'title' => 'Project showcase',
            'description' => 'Photos from the campus project showcase.',
            'gallery_category_id' => $eventsCategory->id,
            'event_id' => $past->id,
            'created_by' => $admin->id,
        ]);
        if ($album->items()->count() === 0) {
            foreach (['One', 'Two', 'Three'] as $index => $label) {
                $path = $this->image('gallery/showcase-'.$index.'.jpg', $label, [10 + ($index * 30), 50, 90]);
                GalleryItem::query()->create([
                    'gallery_album_id' => $album->id,
                    'image' => $path,
                    'caption' => 'Showcase photo '.$label,
                    'sort_order' => $index + 1,
                ]);
                if ($index === 0) {
                    $album->cover = $path;
                    $album->save();
                }
            }
        }

        Resource::query()->updateOrCreate(['title' => 'IEEE student membership page'], [
            'description' => 'Official IEEE page for joining. The branch does not process membership fees.',
            'category' => 'ieee',
            'type' => 'link',
            'external_url' => 'https://www.ieee.org/membership/join/index.html',
            'author' => 'IEEE',
            'published_on' => now()->toDateString(),
            'visibility' => 'public',
            'created_by' => $admin->id,
        ]);
        Resource::query()->updateOrCreate(['title' => 'Workshop notes placeholder'], [
            'description' => 'Replace this link with the notes from the next workshop.',
            'category' => 'workshop',
            'type' => 'link',
            'external_url' => 'https://www.ieee.org/',
            'author' => 'BUP IEEE Student Branch',
            'published_on' => now()->toDateString(),
            'visibility' => 'members',
            'created_by' => $admin->id,
        ]);

        ContactMessage::query()->firstOrCreate(['email' => 'visitor@example.com', 'subject' => 'Question about the next workshop'], [
            'name' => 'Campus visitor',
            'message' => 'Could you share the room once it is confirmed?',
            'status' => 'unread',
        ]);

        SettingsStore::bust();
    }

    private function seedSettings(): void
    {
        foreach (SettingsStore::settingDefaults() as $key => $value) {
            Setting::query()->firstOrCreate(['key' => $key], [
                'value' => $value,
                'group' => in_array($key, ['branch_name', 'tagline', 'logo', 'favicon', 'ieee_join_url'], true) ? 'system' : 'contact',
            ]);
        }
    }

    private function seedContent(): void
    {
        foreach (SettingsStore::contentDefaults() as $key => $meta) {
            PageContent::query()->firstOrCreate(['key' => $key], $meta);
        }
    }

    private function account(string $name, string $email, string $password, string $role, array $profile = [], string $status = 'active'): User
    {
        $user = User::withTrashed()->where('email', $email)->first() ?: new User;
        $user->name = $name;
        $user->email = $email;
        if (! $user->exists) {
            $user->password = $password;
        }
        $user->role_id = Role::query()->where('slug', $role)->value('id');
        $user->status = $status;
        $user->email_verified_at = now();
        if ($status === 'active') {
            $user->approved_at = $user->approved_at ?: now();
        }
        $user->save();

        if ($role === 'member') {
            $user->profile()->updateOrCreate(['user_id' => $user->id], array_merge([
                'directory_visible' => true,
                'show_email' => false,
                'show_phone' => false,
                'show_social' => true,
                'ieee_membership_status' => 'none',
            ], $profile));
        }

        return $user;
    }

    private function officer(ExecutiveCommittee $committee, CommitteePosition $position, string $name, string $department, string $batch): void
    {
        CommitteeMember::query()->updateOrCreate(
            ['executive_committee_id' => $committee->id, 'name' => $name],
            [
                'committee_position_id' => $position->id,
                'department' => $department,
                'batch' => $batch,
                'bio' => $position->name.' for the '.$committee->year.' term.',
            ]
        );
    }

    private function image(string $path, string $label, array $rgb): string
    {
        if (Storage::disk('public')->exists($path)) {
            return $path;
        }

        $image = imagecreatetruecolor(1200, 700);
        $background = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
        $white = imagecolorallocate($image, 255, 255, 255);
        imagefilledrectangle($image, 0, 0, 1200, 700, $background);
        imagestring($image, 5, 48, 330, $label, $white);
        ob_start();
        imagejpeg($image, null, 85);
        $binary = ob_get_clean();
        imagedestroy($image);
        Storage::disk('public')->put($path, $binary);

        return $path;
    }
}
