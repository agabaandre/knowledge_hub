# Approvals inbox

Reviewers with moderation rights use a **single inbox** instead of jumping between specialist pending pages.

**UI:** `/admin/approvals`  
**Route names:** `admin.approvals.index`, `admin.approvals.review`

---

## Queues

| `type` query | Content |
|--------------|---------|
| *(omitted)* / `all` | Everything still pending |
| `publication` | Publications awaiting approve/reject |
| `forum` | Forum threads awaiting approve/reject |
| `cop_participant` | Community membership requests |
| `federated` | Partner-hub items staged after `federation:sync` |

The inbox lists **pending items only**. History stays on publications, forums, participants, and federated-content admin pages.

---

## Notifications

`App\Support\ApprovalNotifications` emails users who can act on that type. Links go to `/admin/approvals?type=…`, not the older pending-list URLs.

| Channel | When |
|---------|------|
| Immediate | New publication, forum, CoP request, or federated batch needing review |
| Daily digest | `php artisan approvals:daily-summary` at **08:00** |

---

## Permissions

| Permission | Queue |
|------------|--------|
| `moderate_publication` | Publications |
| `moderate_forum` | Forums |
| `moderate_cop_participants` | CoP participants |
| `moderate_publication` or `moderate_forum` | Federated content |

---

## Key files

| Path | Role |
|------|------|
| `app/Http/Controllers/Admin/ApprovalsController.php` | Inbox and approve/reject |
| `app/Services/ApprovalInboxService.php` | Pending aggregation |
| `app/Support/ApprovalNotifications.php` | Recipients and mail URLs |
| `app/Console/Commands/SendDailyApprovalSummary.php` | Daily digest |
| `resources/views/admin/approvals/index.blade.php` | Inbox UI |
| `tests/Unit/ApprovalsInboxViewTest.php` | Form actions without JS |
| `tests/Unit/ApprovalNotificationsTest.php` | Mail links and recipients |
