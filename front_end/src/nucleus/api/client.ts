export function khApiUrl(path: string): string {
  const base = process.env.NEXT_PUBLIC_KHUB_API_URL || "/knowledge_hub/api";
  return `${base.replace(/\/$/, "")}/${path.replace(/^\//, "")}`;
}

export async function khGet<T>(path: string): Promise<T> {
  const response = await fetch(khApiUrl(path), {
    headers: { Accept: "application/json" },
  });
  if (!response.ok) {
    throw new Error(`Knowledge Hub API ${path} failed (${response.status})`);
  }
  return response.json() as Promise<T>;
}

export type KhListItem = Record<string, unknown>;

export function khItemTitle(item: KhListItem): string {
  return String(item.title ?? item.name ?? item.tag_text ?? item.question ?? item.thread_title ?? "Untitled");
}

export function khItemSummary(item: KhListItem): string {
  const raw = item.description ?? item.summary ?? item.excerpt ?? item.answer ?? "";
  const text = String(raw).replace(/<[^>]+>/g, " ").trim();
  return text.length > 180 ? `${text.slice(0, 177)}…` : text;
}

export function khItemId(item: KhListItem): string {
  return String(item.id ?? item.publication_id ?? item.forum_id ?? "");
}
