<?php

namespace App\Support;

use App\Models\PageContent;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsStore
{
    public static function get(string $key, ?string $default = null): string
    {
        $all = self::settings();
        if (array_key_exists($key, $all) && $all[$key] !== null && $all[$key] !== '') {
            return (string) $all[$key];
        }

        return $default ?? (self::settingDefaults()[$key] ?? '');
    }

    public static function content(string $key, ?string $default = null): string
    {
        $all = self::contents();
        if (array_key_exists($key, $all) && $all[$key] !== null && $all[$key] !== '') {
            return (string) $all[$key];
        }

        return $default ?? (self::contentDefaults()[$key]['body'] ?? '');
    }

    public static function settings(): array
    {
        try {
            return Cache::remember('site.settings', 300, function () {
                return Setting::query()->pluck('value', 'key')->all();
            });
        } catch (\Throwable) {
            return [];
        }
    }

    public static function contents(): array
    {
        try {
            return Cache::remember('site.contents', 300, function () {
                return PageContent::query()->pluck('body', 'key')->all();
            });
        } catch (\Throwable) {
            return [];
        }
    }

    public static function bust(): void
    {
        Cache::forget('site.settings');
        Cache::forget('site.contents');
        Cache::forget('home.payload');
        Cache::forget('admin.stats');
    }

    public static function putSetting(string $key, ?string $value, string $group): void
    {
        Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );
    }

    /**
     * @return array<string, string>
     */
    public static function settingDefaults(): array
    {
        return [
            'branch_name' => 'BUP IEEE Student Branch',
            'tagline' => 'Empowering Students. Inspiring Innovation. Connecting Futures.',
            'official_email' => '',
            'phone' => '',
            'address' => 'Bangladesh University of Professionals, Mirpur Cantonment, Dhaka-1216, Bangladesh',
            'facebook' => '',
            'linkedin' => '',
            'instagram' => '',
            'website' => '',
            'ieee_join_url' => 'https://www.ieee.org/membership/join/index.html',
            'map_embed_url' => 'https://maps.google.com/maps?q=Bangladesh%20University%20of%20Professionals&z=16&output=embed',
            'logo' => '',
            'favicon' => '',
        ];
    }

    /**
     * @return array<string, array{title: string, body: string, group: string}>
     */
    public static function contentDefaults(): array
    {
        return [
            'home.hero_intro' => [
                'title' => 'Homepage introduction',
                'group' => 'home',
                'body' => 'The BUP IEEE Student Branch is a student community at Bangladesh University of Professionals. This introduction is editable from the admin panel so the executive committee can publish the branch\'s own wording.',
            ],
            'home.about_preview' => [
                'title' => 'Homepage about preview',
                'group' => 'home',
                'body' => 'The branch brings students together for workshops, technical sessions, competitions, and professional activities. Replace this preview with the current committee\'s preferred introduction.',
            ],
            'home.why_join' => [
                'title' => 'Why join introduction',
                'group' => 'home',
                'body' => 'Students take part to learn practical skills, volunteer, and meet peers who are interested in technology. Keep the points below aligned with the membership information the branch wants to publish.',
            ],
            'home.benefits' => [
                'title' => 'Membership benefit cards',
                'group' => 'home',
                'body' => "Technical learning|Workshops, seminars, and hands-on sessions organized by the branch.\nCommunity|Meet students who want to build, volunteer, and learn together.\nLeadership practice|Help run activities and serve with the executive committee.\nIEEE connection|A campus path toward IEEE student membership and its global community.",
            ],
            'about.branch' => [
                'title' => 'About the branch',
                'group' => 'about',
                'body' => 'BUP IEEE Student Branch is the student community at Bangladesh University of Professionals associated with IEEE. Use this section for the official branch description approved by the executive committee.',
            ],
            'about.ieee' => [
                'title' => 'About IEEE',
                'group' => 'about',
                'body' => 'IEEE is a technical professional organization focused on advancing technology for the benefit of humanity. Student branches are campus communities formed through IEEE. Update this text if the branch prefers a specific official description.',
            ],
            'about.vision' => [
                'title' => 'Vision',
                'group' => 'about',
                'body' => 'A campus community where BUP students can learn, build, and lead together.',
            ],
            'about.mission' => [
                'title' => 'Mission',
                'group' => 'about',
                'body' => 'To help students explore technology, practice leadership, and take part in branch activities throughout the academic year.',
            ],
            'about.objectives' => [
                'title' => 'Objectives',
                'group' => 'about',
                'body' => "Encourage technical learning beyond the classroom.\nOrganize workshops, seminars, competitions, and technical sessions.\nSupport student projects and professional development.\nKeep a welcoming volunteer community for new and returning members.",
            ],
            'about.what_we_do' => [
                'title' => 'What we do',
                'group' => 'about',
                'body' => 'The branch publishes events, announcements, resources, and gallery albums from this website. Program details should be updated here whenever the committee\'s activities change.',
            ],
            'about.why' => [
                'title' => 'Why BUP IEEE',
                'group' => 'about',
                'body' => 'The branch gives BUP students a place to find activities, committee contacts, and membership guidance in one website. Specific reasons to join should be confirmed and edited by the committee.',
            ],
            'about.history' => [
                'title' => 'Branch history',
                'group' => 'about',
                'body' => 'Add the confirmed branch history here. This starter text is intentionally general so dates, charter details, and milestones are published only after the committee verifies them.',
            ],
            'about.leadership' => [
                'title' => 'Faculty & Club Leadership introduction',
                'group' => 'about',
                'body' => 'Faculty advisors and moderators are listed here after the branch confirms each profile. Names, photographs, and contact details stay unpublished until an administrator adds them.',
            ],
            'membership.why' => [
                'title' => 'Why join IEEE',
                'group' => 'membership',
                'body' => 'IEEE student membership is offered by IEEE, not by this website. Use this section to explain, in the branch\'s own words, why a BUP student might want to join. Do not present this starter text as an official IEEE rule.',
            ],
            'membership.benefits' => [
                'title' => 'Membership benefits',
                'group' => 'membership',
                'body' => 'List only benefits the executive committee wants to publish. IEEE\'s own membership pages are the source for official benefits, discounts, and eligibility. Link to those pages instead of copying rules that may change.',
            ],
            'membership.eligibility' => [
                'title' => 'Eligibility',
                'group' => 'membership',
                'body' => 'Eligibility for IEEE membership is defined by IEEE. Add a short campus note here, and point students to the official IEEE join page for the current requirements.',
            ],
            'membership.how' => [
                'title' => 'How to join',
                'group' => 'membership',
                'body' => "Review the membership notes on this page.\nOpen the official IEEE join page and complete IEEE's own application.\nSubmit the branch interest form so the committee knows you would like to take part in BUP activities.\nCreate a website account if you want the member dashboard. An administrator approves campus accounts.",
            ],
            'membership.ieee_info' => [
                'title' => 'IEEE membership information',
                'group' => 'membership',
                'body' => 'Official membership instructions, fees, and grades are published by IEEE. Keep this section as guidance and update the join link in system settings if IEEE changes the address.',
            ],
            'membership.join' => [
                'title' => 'Join the branch',
                'group' => 'membership',
                'body' => 'The form below is an interest form for the BUP IEEE Student Branch. It does not create an IEEE membership and it does not take payment.',
            ],
        ];
    }
}
