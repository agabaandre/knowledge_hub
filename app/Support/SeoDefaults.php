<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Fallback meta descriptions when a view does not set $pageDescription.
 * Keeps snippets unique per route/path for search engines.
 */
class SeoDefaults
{
    public static function descriptionForRequest(Request $request): string
    {
        $site = settings()->site_name ?? 'Africa Health Knowledge Hub';
        $name = $request->route()?->getName();
        if ($name !== null) {
            $byName = self::byRouteName($name, $site, $request);
            if ($byName !== null) {
                return $byName;
            }
        }

        return self::byPath(trim($request->path(), '/'), $site, $request);
    }

    private static function byRouteName(string $name, string $site, Request $request): ?string
    {
        return match (true) {
            $name === 'login' => "Sign in to {$site} to access saved resources, discussion forums, courses, communities, and your profile.",
            $name === 'register' => "Register for a free {$site} account to save favourites, join forums, enrol in courses, and collaborate in communities of practice.",
            $name === 'password.request' => "Request a password reset link for your {$site} account.",
            $name === 'password.reset' => "Set a new password for your {$site} account.",
            $name === 'privacy' => "Privacy policy and data practices for {$site} and the Africa CDC knowledge platform.",
            $name === 'browse.authors' => "Browse organisations and authors contributing publications and resources to {$site}.",
            $name === 'health-topics.index' => "Browse health topics, emergencies, and thematic areas with linked publications and resources on {$site}.",
            $name === 'health-topics.show' => "Publications, resources, and discussions for this health topic on {$site}.",
            $name === 'countries' => "Explore public health resources and coverage by member state on {$site}.",
            $name === 'adminunits' => "Browse administrative areas and linked health resources on {$site}.",
            $name === 'community.index' => "Join communities of practice on {$site}: collaborate on public health themes with peers across Africa.",
            $name === 'community.detail' => "Community of practice on {$site}: members, discussions, and shared knowledge products.",
            $name === 'forums.index' => "Public health discussion forums on {$site}: ask questions, share evidence, and connect with experts.",
            $name === 'forums.create' => "Start a new discussion thread on {$site} forums.",
            $name === 'forums.thread' => "Forum thread and replies on {$site}.",
            $name === 'courses.details' => "Course overview, modules, and enrolment on {$site} learning.",
            str_starts_with($name, 'account.') => "Your {$site} account: profile, favourites, publications, forums, and communities.",
            $name === 'events.show' => "Event details, dates, and registration information on {$site}.",
            $name === 'content-request' => "Request health content or a resource to be added to {$site}.",
            $name === 'content-request.track' => "Track the status of your content request to {$site}.",
            default => null,
        };
    }

    private static function byPath(string $path, string $site, Request $request): string
    {
        return match (true) {
            $path === '' => self::homeBody($site),
            $path === 'courses' => "Browse online courses on public health, leadership, and technical skills on {$site}. Enrol and learn at your own pace.",
            str_starts_with($path, 'courses/details') => "Course information, syllabus, ratings, and enrolment options on {$site}.",
            str_starts_with($path, 'records') => "Search publications, communities, and forums on {$site}. Filter by theme, country, author, type, or keywords.",
            str_starts_with($path, 'faqs') => "Frequently asked questions about using {$site}, accounts, submissions, and support.",
            str_starts_with($path, 'tools') => "Data tools and viewers available on {$site} for exploration and analysis.",
            str_starts_with($path, 'healthassets') => "Health assets and related resources published on {$site}.",
            str_starts_with($path, 'publications') => "Publications listing and contributor tools on {$site}.",
            str_starts_with($path, 'browse/themes') => "Browse resources by health theme on {$site}.",
            str_starts_with($path, 'browse/subthemes') => "Browse resources by sub-theme and speciality area on {$site}.",
            str_starts_with($path, 'authors') => "Author and organisation pages with their publications on {$site}.",
            str_starts_with($path, 'countries') => "Country pages with regional public health resources on {$site}.",
            str_starts_with($path, 'adminunits') => "Administrative unit pages with linked resources on {$site}.",
            default => self::genericFromPath($path, $site),
        };
    }

    private static function homeBody(string $site): string
    {
        return "Discover flagship initiatives, health topics, publications, forums, courses, and communities on {$site} — Africa CDC’s continental public health knowledge platform.";
    }

    private static function genericFromPath(string $path, string $site): string
    {
        if ($path === '') {
            return self::homeBody($site);
        }
        $parts = array_values(array_filter(explode('/', $path)));
        $last = end($parts) ?: 'page';
        $label = str_replace(['-', '_'], ' ', $last);
        if (ctype_digit($label) && count($parts) >= 2) {
            $label = str_replace(['-', '_'], ' ', $parts[count($parts) - 2]);
        }

        return ucfirst($label)." — {$site}. Explore verified public health resources, discussions, and learning.";
    }
}
