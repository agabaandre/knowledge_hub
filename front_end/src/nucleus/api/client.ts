/**
 * Knowledge Hub HTTP client.
 * OpenAPI: https://khub.africacdc.org/docs/#/
 */
export const KHUB_API_DOCS = "https://khub.africacdc.org/docs/#/";

export function khApiUrl(path: string): string {
  const base = process.env.NEXT_PUBLIC_KHUB_API_URL || "/knowledge_hub/api";
  return `${base.replace(/\/$/, "")}/${path.replace(/^\//, "")}`;
}

export function khCookiePath(): string {
  const base = process.env.NEXT_PUBLIC_KHUB_API_URL || "/knowledge_hub/api";
  try {
    if (base.startsWith("http")) {
      const pathname = new URL(base).pathname.replace(/\/api\/?$/, "") || "/";
      return pathname.endsWith("/") ? pathname : `${pathname}/`;
    }
  } catch {
    /* fall through */
  }
  const local = base.replace(/\/api\/?$/, "") || "/knowledge_hub";
  return local.endsWith("/") ? local : `${local}/`;
}

export function khReadCookie(name: string): string {
  if (typeof document === "undefined") {
    return "";
  }
  const match = document.cookie.match(new RegExp(`(?:^|; )${name}=([^;]*)`));
  return match ? decodeURIComponent(match[1]) : "";
}

export function khWriteCookie(name: string, value: string): void {
  if (typeof document === "undefined") {
    return;
  }
  document.cookie = `${name}=${encodeURIComponent(value)};path=${khCookiePath()};max-age=${60 * 60 * 24 * 365};SameSite=Lax`;
}

export function khCurrentLocale(): string {
  return khReadCookie("khub_locale") || "en";
}

function withLocale(path: string, locale: string): string {
  const url = khApiUrl(path);
  if (/[?&]locale=/.test(url)) {
    return url;
  }
  return `${url}${url.includes("?") ? "&" : "?"}locale=${encodeURIComponent(locale)}`;
}

export async function khGet<T>(path: string, locale?: string): Promise<T> {
  const active = locale || khCurrentLocale();
  const response = await fetch(withLocale(path, active), {
    headers: {
      Accept: "application/json",
      "Accept-Language": active,
    },
    credentials: "same-origin",
  });
  if (!response.ok) {
    throw new Error(`Knowledge Hub API ${path} failed (${response.status})`);
  }
  return response.json() as Promise<T>;
}

export type KhListItem = Record<string, unknown>;

export function khItemTitle(item: KhListItem, untitled = "Untitled"): string {
  return String(item.title ?? item.name ?? item.tag_text ?? item.question ?? item.thread_title ?? untitled);
}

export function khItemSummary(item: KhListItem): string {
  const raw = item.description ?? item.summary ?? item.excerpt ?? item.answer ?? "";
  const text = String(raw).replace(/<[^>]+>/g, " ").trim();
  return text.length > 180 ? `${text.slice(0, 177)}…` : text;
}

export function khItemId(item: KhListItem): string {
  return String(item.id ?? item.publication_id ?? item.forum_id ?? "");
}

export function khItemCover(item: KhListItem): string {
  const raw = item.cover ?? item.image_url ?? item.image ?? item.thumbnail ?? item.banner ?? "";
  return typeof raw === "string" ? raw : "";
}

export function khItemAuthor(item: KhListItem): string {
  const author = item.author;
  if (typeof author === "string" && author.trim()) {
    return author;
  }
  if (author && typeof author === "object") {
    const row = author as Record<string, unknown>;
    const name = row.name ?? row.full_name ?? row.author_name;
    if (typeof name === "string" && name.trim()) {
      return name;
    }
  }
  const nested = item.authors;
  if (Array.isArray(nested) && nested.length > 0) {
    const first = nested[0];
    if (typeof first === "string") {
      return first;
    }
    if (first && typeof first === "object") {
      const name = (first as Record<string, unknown>).name;
      if (typeof name === "string") {
        return name;
      }
    }
  }
  return String(item.author_name ?? item.user_name ?? item.created_by ?? "");
}

export function khItemDate(item: KhListItem): string {
  const raw = item.publication_date ?? item.published_at ?? item.created_at ?? item.date ?? "";
  const value = String(raw);
  if (!value) {
    return "";
  }
  const parsed = new Date(value);
  if (Number.isNaN(parsed.getTime())) {
    return value;
  }
  return parsed.toLocaleDateString();
}
