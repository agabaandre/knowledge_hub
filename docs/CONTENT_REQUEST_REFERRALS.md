# Content request referrals

This document describes how **content requests** are **referred** from admin to hub users and/or communities of practice (CoP), including **multi-assignee** behaviour, **community forum threads**, tracking, and notifications.

## Overview

- Admins with **`manage_content_requests`** can **refer** a pending (non-processed, not-yet-referred) request from **Admin → Content requests** (`/admin/content-requests`).
- A referral requires **at least two assignees in total**, chosen from any combination of:
  - **Hub users** (multi-select), and/or
  - **Communities of practice** (multi-select).
- Each selected **community** gets its own **approved, community-scoped forum thread** so members can comment; the requester is **not** identified in the forum body beyond what they typed in the original description (see privacy below).
- Assigned **hub users** collaborate via the **authenticated hub discussion** (`/content-request/referral/{id}/discuss`), not via those forum threads unless they are also CoP members.
- The requester receives a **private tracking link** (token URL) for “private notes” and sees **all** community forum links when more than one CoP is assigned.

## Data model

### `content_requests` (legacy + summary columns)

Still used for workflow and backward compatibility:

| Field | Role |
|--------|------|
| `referral_type` | `user`, `community`, or `mixed` |
| `referred_to_user_id` | First selected user (legacy / quick display) |
| `referred_to_community_id` | First selected community (legacy / quick display) |
| `referred_at`, `referred_by`, `referral_notes` | Referral metadata |
| `requestor_track_token` | Secret token for public track page |
| `referral_forum_id` | **First** created forum ID (compatibility for code that expects a single forum) |

**Authoritative list of assignees** is in **`content_request_referral_targets`**.

### `content_request_referral_targets`

One row per assignee:

| Column | Meaning |
|--------|---------|
| `content_request_id` | Parent request |
| `user_id` | Set for a hub user assignee |
| `community_of_practice_id` | Set for a CoP assignee |
| `referral_forum_id` | For CoP rows: forum created for that community (nullable if creation failed) |

Exactly one of `user_id` or `community_of_practice_id` should be set per row (enforced by application logic).

Migration **`2026_04_01_210000_create_content_request_referral_targets_table`** creates the table and **backfills** rows from existing `referred_to_user_id` / `referred_to_community_id` (+ legacy `referral_forum_id` on the community row where applicable).

### Messages

- **`content_request_referral_messages`**: threaded messages for the **hub discussion** and the requester’s **track** page (separate from forum comments).

## Admin UI

- Refer modal: **Select2** multi-select for users and communities (`referred_user_ids[]`, `referred_community_ids[]`), with `dropdownParent` set to the modal so the dropdown layers correctly.
- Client and server validation: **≥ 2** assignees total.
- A request can only be referred **once**; further updates use hub discussion, forums, or track messages.

## Community forums

- Implemented in **`App\Services\ContentRequestReferralForumService::createForCommunityReferral($contentRequest, $communityId, $actorUserId)`**.
- Creates a **live** forum, links it to that CoP (`forum_community_of_practices`), subscribes approved members, increments engagement, and dispatches **`NotifyCommunityMembers`** for that community.
- Forum title/describe the request as a **community-linked content request**; the stored requester **email** on the record is redacted from title/body/notes where it appears as the same string (see `redactRequestorIdentifiersFromText`).
- **Privacy**: forum banner and meta descriptions avoid exposing requester PII; assignees use hub discussion URLs in email where appropriate.

## URLs and permissions

- **`ContentRequest::discussionUrl()`** — first forum if any, else hub discussion URL.
- **`ContentRequest::discussionUrlForCommunity($communityId)`** — forum for that CoP when this request was referred to that community; used on **community detail** “Open forum thread” / “Mark processed”.
- **`ContentRequest::referralForumIds()`** — all distinct forum IDs (targets + legacy `referral_forum_id`).
- **`userMayParticipateInReferralDiscussion`**, **`userMayMarkReferralAsProcessed`** — consider **all** targets plus legacy columns.

## Notifications and jobs

- **`App\Services\ContentRequestReferralNotifier`**: requester email, per-user assignee emails (hub discuss URL), community path via `NotifyCommunityMembers` when a forum exists; legacy member blast only if a community target has **no** forum.
- **`App\Jobs\SendContentRequestForumAiSummaryJob`**: accepts `($contentRequestId, $forumId)`; admin refer dispatches **one job per forum** with staggered delays.

## Linked forum thread UI

- **`ForumsController@thread`** resolves a linked `ContentRequest` if `referral_forum_id` matches the thread **or** any target row’s `referral_forum_id`.
- Banner partial uses **`linkedReferralForumCommunity`** so the correct CoP name shows when one request has multiple forums.

## Community pages

- **`CommunitiesController`**: pending/processed content request lists include requests where the CoP appears in **`referralTargets`** or matches legacy **`referred_to_community_id`** (including `mixed`).

## Operations checklist

1. Run migrations (includes targets table + backfill): `php artisan migrate`
2. Queue worker running for **`SendMailJob`**, **`NotifyCommunityMembers`**, **`SendContentRequestForumAiSummaryJob`**
3. After deploy, clear views if needed: `php artisan view:clear`

## Related files (reference)

| Area | Location |
|------|-----------|
| Admin refer action | `app/Http/Controllers/Admin/ContentRequestAdminController.php` (`refer`) |
| Model | `app/Models/ContentRequest.php`, `app/Models/ContentRequestReferralTarget.php` |
| Forum creation | `app/Services/ContentRequestReferralForumService.php` |
| Email orchestration | `app/Services/ContentRequestReferralNotifier.php` |
| Forum comment → requester email | `app/Repositories/ForumsRepository.php` (linked request lookup) |
| AI summary job | `app/Jobs/SendContentRequestForumAiSummaryJob.php` |
| Admin index + modal | `resources/views/admin/content_requests/index.blade.php` |
