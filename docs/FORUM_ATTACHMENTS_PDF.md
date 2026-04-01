# Forum comment attachments: Office to PDF

This document describes **server software**, **configuration**, and **runtime behaviour** for converting forum comment uploads (Word, Excel, PowerPoint, OpenDocument, RTF) to PDF in the Africa CDC Knowledge Hub.

---

## Feature overview

- **Where it applies:** Attachments on **forum comments** (`model = forum_comments` in `custom_attachments`), including the main “Share your views” form and replies on forum threads (e.g. `/forums/thread?id=…`).
- **Goal:** Store and preview **PDF** where possible, instead of leaving binary office formats in public storage.
- **Code touchpoints (reference):**
  - `App\Services\OfficeDocumentToPdfService` — conversion (LibreOffice + PhpWord fallback for `.docx`)
  - `App\Repositories\ForumsRepository::save_comment_attachments` — convert on upload
  - `App\Http\Controllers\ForumsController::commentAttachmentPdf` — on-demand conversion for existing attachments
  - `GET /forums/comment-attachment/{attachment}/pdf` — route name `forums.comment-attachment.pdf`
  - Helpers in `app/Helpers/UtilsHelper.php`: `forum_comment_attachment_*`
  - Views: `resources/views/forums/partials/comment_file_attachment_link.blade.php`, plus `comment_item` / `forum_details` partials

---

## Software matrix

| Layer | Component | Required? | Notes |
|-------|-------------|------------|--------|
| OS | Linux, Windows Server, or macOS | Yes | PHP must run conversion where files are stored |
| PHP | 8.x (per project) | Yes | |
| Composer | phpoffice/phpword, mpdf/mpdf | Yes | Already in `composer.json`; needed for **.docx → PDF** fallback |
| Composer | symfony/process | Yes | Used to run LibreOffice with a timeout |
| Server | **LibreOffice** (`soffice`) | **Strongly recommended** | Best results for **.doc**, **.xlsx**, **.ppt/.pptx**, OpenDocument, **.rtf** |
| Web stack | Writable `storage/app/public/uploads/forum/` | Yes | Same as general Laravel `storage` requirements |

---

## Installing LibreOffice

### Linux (Debian / Ubuntu)

```bash
sudo apt update
sudo apt install -y libreoffice-writer libreoffice-calc libreoffice-impress
# or a single metapackage:
# sudo apt install -y libreoffice
```

Verify:

```bash
soffice --version
```

### Linux (RHEL / Alma / Rocky)

```bash
sudo dnf install -y libreoffice-headless
```

### macOS (typical dev machine)

```bash
brew install --cask libreoffice
```

Typical binary:

`/Applications/LibreOffice.app/Contents/MacOS/soffice`

### Windows

1. Install from [LibreOffice downloads](https://www.libreoffice.org/download/download/).
2. Locate `soffice.exe` (often under `C:\Program Files\LibreOffice\program\soffice.exe`).
3. Set `LIBREOFFICE_BINARY` in `.env` to that full path.

---

## Environment and config

| Variable | File | Purpose |
|----------|------|---------|
| `LIBREOFFICE_BINARY` | `.env` | Absolute path to `soffice` when it is not on the process `PATH` |

Mapped in `config/services.php`:

```php
'libreoffice' => [
    'binary' => env('LIBREOFFICE_BINARY'),
],
```

If unset, the service attempts common paths and `command -v soffice` (where allowed).

---

## File formats

Treated as **convertible** (same list as `OfficeDocumentToPdfService::CONVERTIBLE_EXTENSIONS`):

`doc`, `docx`, `xls`, `xlsx`, `ppt`, `pptx`, `odt`, `ods`, `odp`, `rtf`

Images, videos, plain **.pdf**, and **.txt** are not passed through this office→PDF pipeline (unchanged behaviour).

---

## Permissions and performance

- The user running **PHP-FPM**, **Apache** `mod_php`, or **php-cgi** must:
  - **Execute** the LibreOffice binary
  - **Read** uploaded source files and **write** PDFs under `storage/app/public/uploads/forum/`
- Subprocess timeout is **120 seconds** per conversion; tune PHP `max_execution_time` and reverse-proxy **read timeout** if operators upload very large documents.
- **SELinux / AppArmor:** may block `soffice` from executing or writing; adjust profiles if conversion silently fails (check application logs).

---

## Operational behaviour

### New uploads

1. File is validated and moved to `storage/app/public/uploads/forum/{md5}.{ext}`.
2. If the extension is convertible, the service runs LibreOffice (or PhpWord+MPDF for `.docx` only when LibreOffice is unavailable).
3. On success, the original office file is removed and the database stores `forum/{md5}.pdf` and an updated display name ending in `.pdf`.
4. On failure, the **original** file remains; users download the native format.

### Existing database rows (legacy office files)

1. UI links office types to `forums.comment-attachment.pdf`.
2. First request converts if needed, updates `custom_attachments.path` / `name`, deletes the old office file, then **redirects** to the normal storage URL for the PDF.

### Logs

- Conversion failures are logged at **info** / **warning** level with context (path, extension). Search logs for `OfficeDocumentToPdfService` or `Forum comment attachment`.

---

## Troubleshooting

| Symptom | Things to check |
|---------|------------------|
| Uploads stay `.docx` / `.pptx` | Is LibreOffice installed? Is `LIBREOFFICE_BINARY` correct? Can the web user run `soffice --version`? |
| Only `.docx` works | LibreOffice likely missing; PhpWord path may still work for `.docx` only |
| 504 / timeout | Increase PHP and proxy timeouts; reduce max attachment size or split large docs |
| Permission denied | `storage`, `uploads/forum`, and execute bit on `soffice` |
| Works in CLI, not in web | Different user / PATH; set `LIBREOFFICE_BINARY` explicitly |

---

## Related documentation

- Main installation guide: [README.md](../README.md) — prerequisites and `.env` snippet
- Application storage: run `php artisan storage:link` so public URLs resolve under `/storage`
