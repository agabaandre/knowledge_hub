# Starter frontend theme pack

Copy this folder, change `theme.json`, zip it, and upload it under **Admin → Configure → Frontend**.

Required:

- `theme.json` with `name` and `extends` (`university`, `language-academy`, or `online-course`)
- optional `id` (lowercase hyphenated)
- optional `tokens.css` (CSS variables such as `--bd-primary`)

The Next.js nucleus at `front_end/src/nucleus/` supplies shared molecules (nav, record cards, partner bar). A pack cannot ship React; it reuses one of the three built-in chrome kits and overlays tokens.

```bash
cd front_end/src/themes/_starter
zip -r ../../../starter-theme.zip theme.json tokens.css
```
