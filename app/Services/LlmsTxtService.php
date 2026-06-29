<?php

namespace App\Services;

class LlmsTxtService
{
    public function render(): string
    {
        $siteName = settings()->site_name ?? 'Africa Health Knowledge Hub';
        $tagline = settings()->tagline ?? 'Informing Health Decisions and Actions';
        $base = rtrim((string) config('app.url'), '/');
        $description = trim(strip_tags((string) (settings()->site_description ?? '')));
        if ($description === '') {
            $description = 'Public health knowledge portal for Africa CDC: publications, health emergencies, forums, communities of practice, courses, and member state data.';
        }
        $summary = \Illuminate\Support\Str::limit($description, 320, '…');

        $lines = [
            '# '.$siteName,
            '',
            '> '.$tagline.'. '.$summary,
            '',
            'This portal is operated by Africa CDC. It is a public knowledge hub for browsing health resources, '
            .'community discussions, member state data, and learning content across Africa.',
            '',
            '## How this portal works',
            '',
            '- **Browse and search** — Use [Browse resources]('.$base.'/records/search) to find publications, reports, and linked forum threads by topic, country, author, or keyword.',
            '- **Read resources** — Open any publication from search or [Publications]('.$base.'/publications) to view summaries, attachments, and related metadata.',
            '- **Join discussions** — [Forums]('.$base.'/forums) and [Communities of practice]('.$base.'/communities) host public health conversations; sign in to post or comment.',
            '- **Explore by geography and theme** — [Member states]('.$base.'/countries) show country profiles and linked resources; [Health topics]('.$base.'/health-topics) group content by theme.',
            '- **Learn and engage** — [Courses]('.$base.'/courses) and [Events]('.$base.'/events) cover training and upcoming activities.',
            '- **Need something missing?** — Submit a [Content request]('.$base.'/publications/request-content) or read the [User guide]('.$base.'/user_manual).',
            '',
            'All links in this file point to public pages. Use the [XML sitemap]('.$base.'/sitemap.xml) for a complete index of crawlable URLs.',
            '',
            '## Core pages',
            '',
            '- [Home]('.$base.'): Portal landing page with featured resources and health emergencies',
            '- [Browse resources]('.$base.'/records/search): Search publications, data, and linked forum threads',
            '- [Publications index]('.$base.'/publications): Browse approved public health resources',
            '- [Discussion forums]('.$base.'/forums): Community forum threads on public health topics',
            '- [Communities of practice]('.$base.'/communities): CoP spaces for expert collaboration',
            '- [Health topics]('.$base.'/health-topics): Thematic health topic overviews and health emergency tags',
            '- [Member states]('.$base.'/countries): African Union member state profiles, indicators, and linked resources',
            '- [Contributors]('.$base.'/authors): Author and contributor profiles',
            '- [Courses]('.$base.'/courses): Learning courses hosted on the hub',
            '- [Events]('.$base.'/events): Public health events calendar',
            '',
            '## Support and engagement',
            '',
            '- [FAQs]('.$base.'/faqs): Frequently asked questions about the Knowledge Hub',
            '- [Content request]('.$base.'/publications/request-content): Request missing content from the hub team',
            '- [User guide]('.$base.'/user_manual): How to use the portal',
            '- [Privacy policy]('.$base.'/privacy): Privacy and data use policy',
            '',
            '## Federation and discovery',
            '',
            '- [Partner country hubs]('.$base.'/federated): Federated resources from partner Knowledge Hubs',
            '- [XML sitemap index]('.$base.'/sitemap.xml): Full sitemap for crawlers and agents',
            '- [Robots policy]('.$base.'/robots.txt): Crawler allow/disallow rules',
            '',
            '## Optional',
            '',
            '- [Africa CDC website](https://africacdc.org): Parent organisation website',
            '- [Journal of Public Health in Africa](https://www.journalofpublichealthinafrica.org): Related journal',
        ];

        return implode("\n", $lines)."\n";
    }
}
