# DESIGN.MD — Alumni Tracking System Design Specification

> **Specification Standard:** [VoltAgent/awesome-design-md](https://github.com/VoltAgent/awesome-design-md)  
> **Target Framework:** Modern Semantic HTML5 + Bootstrap 5.3 + Custom CSS Tokens  
> **Brand Identity:** Istanbul University Alumni Tracking System (Mezun Takip Portalı)  
> **Brand Overview:** [brand.md](./brand.md) · **Data Architecture:** [ddd.md](./ddd.md) · **Project Root:** [README.md](./README.md)

---

## 1. Design Philosophy & Atmosphere

- **Archetype:** Institutional & Modern Academic Career Hub (Hybrid of University Heritage and Linear/Vercel modern developer clarity).
- **Tone & Mood:** Authoritative, clean, accessible, trustworthy, career-forward.
- **Visual Density:** Medium-balanced (Spacious padding, clean grid, clear typographic rhythm).
- **Depth & Dimension:** Soft elevation, subtle border separation, 1px boundary lines with minimal shadows.

---

## 2. Design Tokens

### 2.1. Color Roles & Palette

```css
:root {
  /* Brand Core Colors */
  --color-primary: #1e3a8a;          /* Deep Istanbul University Navy */
  --color-primary-hover: #1e40af;    /* Darkened Navy */
  --color-accent: #2563eb;           /* Interactive Royal Blue */
  --color-accent-hover: #1d4ed8;     /* Active Blue State */
  --color-gold: #f59e0b;             /* Alumni Prestige Gold */

  /* Semantic Feedback Roles */
  --color-success: #10b981;          /* Active, Accepted, Healthy (200/201) */
  --color-warning: #f97316;          /* Pending, In Review, Validation (422) */
  --color-danger: #ef4444;           /* Closed, Rejected, Guardian Alert (404/500) */
  --color-info: #0ea5e9;             /* System Notices, Documentation */

  /* Neutral Surface & Backgrounds */
  --bg-app: #f8fafc;                 /* Light cool slate background */
  --bg-surface: #ffffff;             /* Crisp card and modal surface */
  --bg-subtle: #f1f5f9;              /* Table headers, code blocks */
  --border-subtle: #e2e8f0;          /* Card and table borders */
  --border-strong: #cbd5e1;          /* Form input borders */

  /* Typography Colors */
  --text-primary: #0f172a;           /* Highest contrast title and body text */
  --text-secondary: #475569;         /* Supporting labels, captions */
  --text-muted: #94a3b8;             /* Timestamps, disabled hints */
  --text-inverse: #ffffff;           /* Text on primary buttons and hero */
}
```

### 2.2. Typography Hierarchy

```css
:root {
  --font-sans: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
  --font-mono: "SF Mono", Menlo, Consolas, "Liberation Mono", monospace;

  /* Type Scale */
  --text-xs: 0.75rem;    /* 12px - Badges, pills */
  --text-sm: 0.875rem;   /* 14px - Meta info, small buttons */
  --text-base: 1rem;     /* 16px - Primary body copy */
  --text-lg: 1.125rem;   /* 18px - Card subtitles */
  --text-xl: 1.25rem;    /* 20px - Card headers, modal titles */
  --text-2xl: 1.5rem;    /* 24px - Section headers */
  --text-3xl: 2rem;      /* 32px - Page main headers */
  --text-4xl: 2.75rem;   /* 44px - Hero display title */

  /* Weights */
  --weight-regular: 400;
  --weight-medium: 500;
  --weight-semibold: 600;
  --weight-bold: 700;
  --weight-black: 900;
}
```

### 2.3. Spacing & Elevation

```css
:root {
  /* Radius */
  --radius-sm: 6px;      /* Badges, tags */
  --radius-md: 10px;     /* Buttons, form inputs */
  --radius-lg: 16px;     /* Content cards */
  --radius-xl: 24px;     /* Hero container, modals */
  --radius-pill: 9999px; /* Status pills, counters */

  /* Shadows (Soft Elevation) */
  --shadow-sm: 0 1px 3px rgba(15, 23, 42, 0.05);
  --shadow-md: 0 4px 12px rgba(15, 23, 42, 0.07);
  --shadow-lg: 0 10px 30px rgba(15, 23, 42, 0.10);
  --shadow-focus: 0 0 0 3px rgba(37, 99, 235, 0.18);
}
```

---

## 3. Component Architecture & Patterns

### 3.1. Navigation Bar (`Navbar`)
- **Structure:** Clean white surface (`--bg-surface`), sticky-top, border-bottom: `1px solid var(--border-subtle)`.
- **Branding:** Graduation cap emoji or official emblem + "Alumni Tracking" title in `--color-primary`.
- **Navigation Links:** Semibold font weight, subtle hover effect with `--color-accent`.

### 3.2. Data & Feature Cards
- **Structure:** White background, `border: 1px solid var(--border-subtle)`, `border-radius: var(--radius-lg)`.
- **Interactive State:** Hover elevates by `translateY(-4px)` with `--shadow-md` and border tint `--color-accent`.
- **Content Flow:** Header with category badge $\rightarrow$ bold title $\rightarrow$ metadata line (location, company) $\rightarrow$ body description $\rightarrow$ footer action button.

### 3.3. Buttons & Action Elements
- **Primary:** Background: `--color-accent`, text: `--text-inverse`, radius: `--radius-md`.
- **Outline:** Transparent background, border: `1px solid var(--border-strong)`, text: `--text-primary`.
- **Danger:** Background: `--color-danger`, for delete or alert confirmations.
- **Micro-Interaction:** Smooth 150ms cubic-bezier transition on all hover and active states.

### 3.4. Forms & Inputs
- **Inputs:** Height: 44px, padding: 8px 14px, radius: `--radius-md`, border: `1px solid var(--border-strong)`.
- **Focus:** Border switches to `--color-accent`, halo effect via `--shadow-focus`.
- **Validation:**
  - Valid: Green border `--color-success`.
  - Invalid: Red border `--color-danger`, accompanying error text in `--text-xs`.

### 3.5. Badges & Status Pills
- **Active / Success:** Background: `rgba(16, 185, 129, 0.12)`, text: `#065f46`.
- **Pending / In Review:** Background: `rgba(249, 115, 22, 0.12)`, text: `#9a3412`.
- **Remote / Hybrid:** Background: `rgba(37, 99, 235, 0.10)`, text: `#1e40af`.
- **Geometry:** `border-radius: var(--radius-pill)`, uppercase typography, font-size: `--text-xs`.

---

## 4. Layout & Grid Principles

1. **Container Widths:**
   - Standard content width: `max-width: 1200px` (Bootstrap `container`).
   - Focused forms & error cards: `max-width: 680px`.
2. **Responsive Breakpoints:**
   - Mobile (`< 768px`): 1-column stacked cards, full-width buttons.
   - Tablet (`768px - 991px`): 2-column card grid.
   - Desktop (`>= 992px`): 3-column feature grid with fixed sidebar navigation when appropriate.

---

## 5. Do's and Don'ts for AI Agents & Developers

### ✅ DO:
- Use semantic CSS variables (`var(--color-primary)`) instead of hardcoded hex values.
- Adhere strictly to the 120-character limitation on Job Titles and Company Names (truncate visual overflow with CSS).
- Include appropriate icons (Emojis or Bootstrap Icons) alongside labels for higher scanning speed.
- Support responsive viewport scaling with mobile-first CSS utility classes.

### ❌ DON'T:
- Never use random off-brand colors (e.g., neon purples, lime greens, dark gradients that clash with university identity).
- Never render raw unstyled HTML tables without Bootstrap table classes (`table table-hover align-middle`).
- Never present an unhandled 404 or 500 error as a bare text string; always route through the Guardian UI components.
- Do not add heavy external CSS libraries; maintain the light footprint using vanilla CSS variables and Bootstrap 5.3.

---

## 6. Upstream Reference

```yaml
Standard: VoltAgent/awesome-design-md
Version: 1.0.0
Author: Battal Buğra Karataş (@tegmenbugo)
Course: YBSB3001 Web Programming
Instructor: Assoc. Prof. Dr. Emre Akadal
```
