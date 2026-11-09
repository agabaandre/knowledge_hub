import { truncate } from '@khub/shared';

/**
 * llms.txt (llmstxt.org): a Markdown map of the portal for AI agents. Same
 * sections as the Laravel LlmsTxtService; module-gated links are omitted
 * when the module is off.
 */
export const renderLlmsTxt = ({ site, settings, modules }) => {
  const base = site.publicUrl;
  const siteName = settings.site_name;
  const tagline = settings.slogan || 'Informing Health Decisions and Actions';
  const description = truncate(settings.site_description || 'Public health knowledge portal for Africa CDC: publications, health emergencies, forums, communities of practice, courses, and member state data.', 320);

  const link = (label, path, text) => `- [${label}](${base}${path}): ${text}`;
  const when = (module, line) => (modules[module] ? [line] : []);

  const lines = [
    `# ${siteName}`,
    '',
    `> ${tagline}. ${description}`,
    '',
    'This portal is a public knowledge hub for browsing health resources, community discussions, member state data, and learning content across Africa.',
    '',
    '## How this portal works',
    '',
    `- **Browse and search** — Use [Browse resources](${base}/records/search) to find publications, reports, and linked forum threads by topic, country, author, or keyword.`,
    `- **Read resources** — Open any publication from search or [Publications](${base}/publications) to view summaries, attachments, and related metadata.`,
    ...when('forums', `- **Join discussions** — [Forums](${base}/forums) and [Communities of practice](${base}/communities) host public health conversations; sign in to post or comment.`),
    ...when('health-topics', `- **Explore by theme** — [Health topics](${base}/health-topics) group content by theme and health emergency.`),
    `- **Need something missing?** — Submit a [Content request](${base}/publications/request-content) or read the [User guide](${base}/user_manual).`,
    '',
    `All links in this file point to public pages. Use the [XML sitemap](${base}/sitemap.xml) for a complete index of crawlable URLs.`,
    '',
    '## Core pages',
    '',
    link('Home', '', 'Portal landing page with featured resources and health emergencies'),
    link('Browse resources', '/records/search', 'Search publications, data, and linked forum threads'),
    link('Publications index', '/publications', 'Browse approved public health resources'),
    ...when('forums', link('Discussion forums', '/forums', 'Community forum threads on public health topics')),
    ...when('communities', link('Communities of practice', '/communities', 'CoP spaces for expert collaboration')),
    ...when('health-topics', link('Health topics', '/health-topics', 'Thematic health topic overviews and health emergency tags')),
    ...when('countries', link('Member states', '/countries', 'Member state profiles, indicators, and linked resources')),
    ...when('authors', link('Contributors', '/authors', 'Author and contributor profiles')),
    ...when('courses', link('Courses', '/courses', 'Learning courses hosted on the hub')),
    ...when('events', link('Events', '/events', 'Public health events calendar')),
    '',
    '## Support and engagement',
    '',
    ...when('faqs', link('FAQs', '/faqs', 'Frequently asked questions about the Knowledge Hub')),
    link('Content request', '/publications/request-content', 'Request missing content from the hub team'),
    link('User guide', '/user_manual', 'How to use the portal'),
    link('Privacy policy', '/privacy', 'Privacy and data use policy'),
    '',
    '## Federation and discovery',
    '',
    ...when('federation', link('Partner country hubs', '/federated', 'Federated resources from partner Knowledge Hubs')),
    link('XML sitemap index', '/sitemap.xml', 'Full sitemap for crawlers and agents'),
    link('Robots policy', '/robots.txt', 'Crawler allow/disallow rules'),
    '',
    '## Optional',
    '',
    '- [Africa CDC website](https://africacdc.org): Parent organisation website',
  ];

  return `${lines.join('\n')}\n`;
};
