---
name: "colonel-clawd-code"
description: "Developer standards for this repo: code quality, readability, maintainability, simplicity, scalability. Apply to ALL code written in this project."
---

# The Developer Standards I Expect

I want a highly skilled developer who prioritizes **code quality, readability, maintainability, simplicity, and scalability**.

Write code another developer can open six months later and understand easily.

## Core rules (full rationale in the original document)

1. **Keep files short** — ideally ≤150 lines; never God files. Every file has one clear responsibility. Do not split artificially.
2. **A function does one thing** (SRP). You should explain it in one sentence.
3. **SOLID**: single responsibility; open for extension, closed for unnecessary modification; substitutable parts; depend only on what you need; depend on abstractions, not one provider.
4. **DRY** — centralize meaningful duplication, not every similar line.
5. **KISS** — the simplest solution that makes sense.
6. **YAGNI** — do not build for imaginary future needs.
7. **No overengineering** — an abstraction must earn its place.
8. **Clear names** — names answer "what does this contain?"; avoid data/result/info/x.
9. No unnecessary variables; no single-use wrapper functions without value.
10. Ternaries for simple decisions only; **early returns**; avoid deep nesting.
11. **Separate responsibilities**: controllers thin, services hold business logic, validators validate, models model.
12. **Handle errors properly; never swallow them silently.** Never expose internals to users.
13. Comments explain **why**, not what. Delete dead/commented-out code — git remembers.
14. Avoid giant utils/ dumping grounds; keep architecture consistent with the project.
15. Prefer object parameters over long positional lists; name magic values.
16. **Never trust user input**; validate at the backend; security from the start; secrets in env, never in code.
17. Test what matters (auth, permissions, payments, critical logic) — success AND failure paths.
18. Don't optimize before measuring. Think about edge cases (null, empty, missing, timeouts).
19. Predictable code, minimal side effects; `getUser()` must not delete users.
20. Respect project consistency; make focused changes; read and understand before modifying.

## The general philosophy

Before a line: is this necessary? Before a file: does it have one responsibility? Before an abstraction: does it simplify? Before a dependency: do we really need it?

> Good code is not about being clever. Good code is about making complexity easier to understand.
