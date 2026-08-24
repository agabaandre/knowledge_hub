# Knowledge Hub user guide

This guide is for visitors, contributors, community members, and reviewers. Administrators should also read the [administrator guide](/administrator-guide).

Open it any time from the site footer (**User Guide**) or at `/user_manual`.

---

## 1. What the Knowledge Hub is

The Knowledge Hub is Africa CDC’s platform for public health knowledge: publications, discussion forums, communities of practice, courses, events, country or administrative-unit profiles, and partner-hub content.

Without an account you can:

- Search and read public publications
- Browse forums, communities, health topics, courses, and events
- Open country pages (continental hubs) or administrative-unit pages (country hubs)
- Browse approved partner-hub content at `/federated`

With an account you can publish resources, start discussions, join communities, save favourites, request missing content, and (if permitted) review pending items.

![Home page](/manual/user-guide/01-home.png)
*Home page — search, featured resources, and main navigation.*

---

## 2. Create an account and sign in

1. Choose **Sign up** in the header.
2. Enter your name, email, country, and password — or continue with Microsoft, Google, or LinkedIn when those options are enabled.
3. Confirm your email if the hub asks you to verify.
4. Sign in from **Log in**.

![Create an account](/manual/user-guide/03-register.png)
*Registration form — email sign-up and optional social login.*

![Sign in](/manual/user-guide/02-login.png)
*Sign-in page — email and password (social login appears when configured).*

Update your profile from the account menu (name, organisation, country, ORCID, and related fields). Your organisation shown on community publication cards comes from this profile.

![Account profile](/manual/user-guide/22-account.png)
*Account profile — name, organisation, country, and related fields.*

---

## 3. Search and browse resources

**Path:** `/records` (also linked as browse / search from the header)

- Type keywords (title, description, authors, tags).
- Narrow results with filters: theme, sub-theme, country or administrative unit, author, file type, tags.
- Open a card to read the summary, preview or download files, comment, favourite, share, or start a Khub AI chat on attached PDFs.

Publication cards on **search**, **theme/tag pages**, and **administrative-unit detail** pages share the same layout: cover, title, excerpt, authors, dates, file-type badges, and actions (preview, download, favourite, AI).

SEO-friendly URLs use slugs from titles (for example `/records/resource/{slug}`). If an administrator renames a theme, publication, or similar record, the public URL updates to match the new name.

![Records search](/manual/user-guide/04-records.png)
*Records search — keywords, filters, and publication cards.*

![Publication detail](/manual/user-guide/05-publication-detail.png)
*Publication page — summary, files, preview, download, favourite, and AI chat.*

---

## 4. Publish a resource

**Path:** `/account/publish` (sign in required)

1. Open **Publish a resource** from the Create menu or your account.
2. Enter title, description (aim for a substantial abstract), authors, and affiliation. Uploading a document can pre-fill description, authors, and affiliation with AI when that feature is enabled.
3. Choose theme, sub-theme, category, and at least one health topic/tag. Add optional metadata (DOI, ISBN, licence, funder).
4. Upload files and/or an external link. PDF is preferred for reports. Word, Excel, and PowerPoint can be converted to PDF when LibreOffice is available on the server.
5. Submit. Unless auto-approve is on, a reviewer must accept the resource before it appears in public search.

Track status under **My publications**. Pending and rejected items can be edited and resubmitted. After approval, further changes usually go through a new version.

You will receive email when a reviewer decides. Reviewers work from a single **Approvals** inbox (`/admin/approvals`), so links in those emails open that inbox rather than older specialist queues.

### Videos

Use this when the resource is a recording (webinar, training clip, announcement) rather than a PDF.

1. In the publish wizard, choose **External Link** if the video is already on YouTube, Vimeo, or a similar host. Do **not** upload a copy as an attachment unless you only have a local file.
2. Paste the **full watch URL** from the browser address bar (for example `https://www.youtube.com/watch?v=…` or a `youtu.be/…` short link).
3. Turn on **Embedded On Page** so visitors can play the video on the resource page instead of leaving the Hub.
4. Complete title, description, theme, and tags as for any other resource, then submit.

If you only have a file on your computer, choose **Attachment** and upload a common format such as MP4 or WebM. Scripts and executables are not allowed. The Hub flags video resources automatically and can generate a cover image from the platform thumbnail or the first seconds of a file.

### Embedded dashboards

Use this for Power BI, Tableau, DHIS2, Excel Online, or other live views that should appear inside the Hub.

1. Choose **External Link** and paste the dashboard’s **embed or share URL** (the address that loads in a browser, not a file download).
2. Turn on **Embedded On Page**. The resource page shows the dashboard in an in-page frame so people can explore it without opening a new tab.
3. Pick the dashboard / data-visualisation **category** your hub uses, plus theme and tags, so the item is findable in search.
4. Administrators can also tick **Admin Only Access** to keep a sensitive dashboard off the public catalogue (it then appears under **Admin → Dashboards**). **Default in Category** features it as the primary item in that category.

If embedding is blocked by the dashboard host (X-Frame-Options / CSP), leave **Embedded On Page** off so the Hub opens the external URL instead.

### New versions of a document

Do **not** publish a second, unrelated record when a report is updated. After the original resource is approved, open it and choose **Submit Version** (shown when versioning is enabled for the hub). That form is at `/account/newversion`.

- Enter a **version number or name** (the Hub suggests the next number).
- Upload the new file and/or paste a new link. Title, authors, theme, and tags are copied from the original — change only what is different.
- Submit. The new version goes through the same **Approvals** inbox as a first publication unless auto-approve is on.
- The original record stays the public canonical page. Approved versions are listed on that page so readers can open earlier or later editions.
- You can version a parent resource only, not another version.

Until a version is approved it stays under **My publications** as pending, like any other submission.

![Publish a resource](/manual/user-guide/20-publish.png)
*Publish wizard — title, description, authors, theme, files, and submit.*

![My publications](/manual/user-guide/21-my-publications.png)
*My publications — draft, pending, approved, and rejected items.*

---

## 5. Forums

**Path:** `/forums`

- Browse approved discussions by tag or community.
- Start a thread from **Create → Forum discussion** (guests are asked to sign in).
- Reply, attach files, and share threads. Office attachments are converted to PDF when possible.
- Your own threads are listed under your account (My forums / My discussions).

New threads wait for moderation unless the hub auto-approves them.

![Forums](/manual/user-guide/06-forums.png)
*Forums list — approved discussions by tag or community.*

![Forum thread](/manual/user-guide/07-forum-thread.png)
*Forum thread — original post, replies, and attachments.*

![My forums](/manual/user-guide/24-my-forums.png)
*My forums / discussions — threads you started.*

---

## 6. Communities of practice

**Path:** `/communities`

- Open a community (`/communities/detail/{slug}`).
- Request membership if it is not open to everyone. Managers approve participants.
- Members see tabbed activity: **Wall**, **Publications**, **Forums**, and **Processed requests**, without a full page reload.
- The sidebar lists recent forums, events, members, badges, and your other communities.

![Communities](/manual/user-guide/08-communities.png)
*Communities of practice — browse and open a community.*

![Community detail](/manual/user-guide/09-community-detail.png)
*Community page — Wall, Publications, Forums, and membership.*

![My communities](/manual/user-guide/25-my-communities.png)
*My communities — memberships and pending requests.*

---

## 7. Countries and administrative units

**Continental hubs** (member-state mode):

- Browse `/countries` and open a country profile for indicators and linked publications.

**Country / regional hubs** (administrative units):

- Browse `/adminunits`.
- Open a unit detail page (`/adminunits/details?id=…`) to see child units and that unit’s publications.
- Publication cards on the unit page match records search: same cover, excerpt, preview, download, and AI actions.

![Countries](/manual/user-guide/10-countries.png)
*Geographical areas — member states used for tagging and filters (public `/countries` on continental hubs).*

![Country profile](/manual/user-guide/11-country-detail.png)
*Country / area records — names, ISO codes, flags, and edit actions.*

![Administrative units](/manual/user-guide/12-adminunits.png)
*Administrative units (country hubs) — map and unit list.*

![Administrative-unit detail](/manual/user-guide/13-adminunit-detail.png)
*Administrative units — manage units when the hub uses sub-national geography.*

---

## 8. Partner country hubs (federation)

**Path:** `/federated`

On a continental hub you can browse public publications and forums that partner country hubs have shared.

- Linked partner hubs appear in a carousel **above** the listings. Cards use the partner country’s flag; click a card to filter to that hub.
- Listings use compact covers (aligned with the text) and longer excerpts (about 140 words).
- Partner content is staged after sync. It appears here only after a central administrator approves it in **Approvals** (federated queue) or **Settings → Federated Knowledge Hubs → Review pending content**.

Country hubs can connect to the continental hub during install or later under **Admin → Federated Knowledge Hubs** to import branding and lookup metadata.

![Federated browse](/manual/user-guide/14-federated.png)
*Federated Knowledge Hubs — connect partner hubs; public `/federated` appears once partner content is approved.*

---

## 9. Health topics, courses, events, and FAQs

| Page | Path | What it is |
|------|------|------------|
| Health topics | `/health-topics` | Topic overviews and related resources |
| Courses | `/courses` | Learning catalogue |
| Events | `/events/{id}` | Public event pages |
| FAQs | `/faqs` | Short answers to common questions |
| Content request | `/publications/request-content` | Ask the hub team for missing material |
| Privacy | `/privacy` | Privacy policy |
| User guide | `/user_manual` | This guide in the portal |

![Health topics](/manual/user-guide/15-health-topics.png)
*Health topics — topic overviews and related resources.*

![Health topic detail](/manual/user-guide/16-health-topic-detail.png)
*Health topic page — description and linked publications.*

![Courses](/manual/user-guide/17-courses.png)
*Courses catalogue.*

![FAQs](/manual/user-guide/18-faqs.png)
*FAQs — short answers to common questions.*

![Content request](/manual/user-guide/19-content-request.png)
*Content request — ask the hub team for missing material.*

---

## 10. Khub AI

Where enabled:

- **PDF chat** on a publication: ask questions about one or more attached PDFs.
- **AI search insights** on records search.
- **Forum summaries** on long threads.

AI is an aid for discovery. Always open the source publication for decisions and citation.

---

## 11. Favourites, requests, and your account

- Favourite a publication from its card or detail page; review them in your account.
- Request missing content from **Support / Content request**. If the request is referred to people or communities, you receive a private tracking link.
- Account menu: profile, my publications, my forums, my communities, favourites, and (for staff) **Admin panel**.

![Favourites](/manual/user-guide/23-favourites.png)
*Favourites — publications you saved from a card or detail page.*

---

## 12. For reviewers

If you can approve publications, forums, community participants, or federated items:

1. Open **Admin → Approvals** (`/admin/approvals`).
2. Filter by type (`publication`, `forum`, `cop_participant`, `federated`) or search by title/submitter.
3. Approve or reject one item or a selection. Include a reason when rejecting — the contributor is notified by email.
4. A daily summary email lists remaining pending items (scheduled at 08:00).

The inbox shows **only items still waiting**. History stays on the original admin pages.

Permissions: `moderate_publication`, `moderate_forum`, `moderate_cop_participants`. Federated items are shown to publication or forum moderators.

![Approvals inbox](/manual/user-guide/26-approvals.png)
*Approvals inbox — pending publications, forums, community participants, and federated items.*

---

## 13. Tips and troubleshooting

- Use **PDF** for reports and strategies so in-browser preview, download, and AI chat work reliably.
- If a public link 404s after a title change, search for the resource; the slug is regenerated from the new title.
- If an administrative-unit page does not show the same publication cards as search, use `/adminunits/details?id=…` after the latest release.
- For account or permission problems, contact the hub administrator listed in the site footer.

More operator detail: [administrator guide](/administrator-guide).
