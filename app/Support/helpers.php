<?php

use App\Support\SettingsStore;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

if (! function_exists('setting')) {
    function setting(string $key, ?string $default = null): string
    {
        return SettingsStore::get($key, $default);
    }
}

if (! function_exists('page_content')) {
    function page_content(string $key, ?string $default = null): string
    {
        return SettingsStore::content($key, $default);
    }
}

if (! function_exists('like_term')) {
    function like_term(string $value): string
    {
        $value = trim($value);
        $value = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);

        return '%'.$value.'%';
    }
}

if (! function_exists('csv_list')) {
    function csv_list(?string $value, int $max = 20): array
    {
        if ($value === null || trim($value) === '') {
            return [];
        }

        $parts = preg_split('/[,\\n]+/', $value) ?: [];
        $clean = [];

        foreach ($parts as $part) {
            $part = trim(strip_tags($part));
            if ($part === '') {
                continue;
            }
            $clean[] = mb_substr($part, 0, 40);
            if (count($clean) >= $max) {
                break;
            }
        }

        return array_values(array_unique($clean));
    }
}

if (! function_exists('unique_slug')) {
    function unique_slug(string $class, string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        if ($base === '') {
            $base = 'item';
        }

        $usesTrash = in_array(SoftDeletes::class, class_uses_recursive($class), true);
        $slug = $base;
        $i = 2;

        while (true) {
            $query = $usesTrash ? $class::withTrashed() : $class::query();
            $query->where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
            if (! $query->exists()) {
                return $slug;
            }
            $slug = $base.'-'.$i;
            $i++;
        }
    }
}

if (! function_exists('safe_url')) {
    function safe_url(?string $url): ?string
    {
        if ($url === null || trim($url) === '') {
            return null;
        }

        $url = trim($url);
        if (! preg_match('#^https?://#i', $url)) {
            return null;
        }

        return $url;
    }
}

if (! function_exists('safe_embed')) {
    function safe_embed(?string $url): ?string
    {
        $url = safe_url($url);
        if ($url === null) {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $allowed = [
            'www.google.com',
            'google.com',
            'maps.google.com',
            'www.openstreetmap.org',
            'openstreetmap.org',
        ];

        return in_array($host, $allowed, true) ? $url : null;
    }
}

if (! function_exists('public_file_url')) {
    function public_file_url(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        return asset('storage/'.$path);
    }
}

if (! function_exists('content_paragraphs')) {
    /**
     * @return list<string>
     */
    function content_paragraphs(string $body): array
    {
        $parts = preg_split("/\r\n|\n|\r/", trim($body)) ?: [];

        return array_values(array_filter(array_map('trim', $parts), fn ($line) => $line !== ''));
    }
}

if (! function_exists('benefit_cards')) {
    /**
     * Lines formatted as "Title|Description".
     *
     * @return list<array{title: string, text: string}>
     */
    function benefit_cards(?string $body): array
    {
        $cards = [];
        foreach (content_paragraphs((string) $body) as $line) {
            [$title, $text] = array_pad(explode('|', $line, 2), 2, '');
            $title = trim($title);
            $text = trim($text);
            if ($title === '') {
                continue;
            }
            $cards[] = ['title' => $title, 'text' => $text !== '' ? $text : $title];
        }

        return $cards;
    }
}
