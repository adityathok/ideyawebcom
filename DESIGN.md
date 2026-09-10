---
version: alpha
name: ideyaweb-design-analysis
description: "A product-led incident-response canvas built on a clean white ground, PP Mori (Pangram Pangram's geometric grotesque) carrying the entire hierarchy, and a confident deep blue brand — deep blue (#0a1589) for CTAs and labels, vivid blue (#2b4bff) for gradient accents and iconography. Blue-white cards (#fafbff) and blue tints (#f3f6ff / #e3eaff) lift off the white canvas with soft rounded corners (16–32px) and gentle blue-tinted shadows. PP Editorial Old serif at light weight punctuates hero and section headlines, while a deep blue (#06105a) rounded-top footer and a dark-navy (#131a38) announcement banner anchor the page vertically. Gradient text, pill badges, and 20px-radius buttons complete an airy, friendly, deeply product-centric system."
 
colors:
  primary: "#0a1589"
  on-primary: "#ffffff"
  accent: "#2b4bff"
  ink: "#100f12"
  ink-muted: "#65646e"
  ink-subtle: "#787685"
  ink-tertiary: "#aaa9ae"
  canvas: "#ffffff"
  surface-1: "#fafbff"
  surface-2: "#f3f6ff"
  surface-3: "#e3eaff"
  inverse-canvas: "#111319"
  inverse-surface-1: "#3a4566"
  inverse-ink: "#ffffff"
  inverse-ink-muted: "#cbd3e8"
  hairline: "#e3eaff"
  hairline-soft: "#c7d6ff"
  brand-blue-deep: "#06105a"
  blue-500: "#7d95ff"
  amber: "#f0883e"
  amber-soft: "#ffc387"
  gradient-pink: "#f5bbc3"
  gradient-amber: "#fbb040"
  semantic-error: "#c62424"
  semantic-success: "#25742d"

typography:
  display-xxl:
    fontFamily: PPEditorialOld
    fontSize: 96px
    fontWeight: 200
    lineHeight: 1.10
    letterSpacing: -1.6px
  display-xl:
    fontFamily: PPMori
    fontSize: 60px
    fontWeight: 500
    lineHeight: 1.03
    letterSpacing: -0.16px
  display-lg:
    fontFamily: PPMori
    fontSize: 54px
    fontWeight: 500
    lineHeight: 1.05
    letterSpacing: -0.16px
  display-md:
    fontFamily: PPMori
    fontSize: 48px
    fontWeight: 500
    lineHeight: 1.20
    letterSpacing: -0.16px
  headline:
    fontFamily: PPMori
    fontSize: 40px
    fontWeight: 500
    lineHeight: 1.15
    letterSpacing: -0.16px
  subhead:
    fontFamily: PPMori
    fontSize: 36px
    fontWeight: 500
    lineHeight: 1.10
    letterSpacing: -0.16px
  card-title:
    fontFamily: PPMori
    fontSize: 26px
    fontWeight: 600
    lineHeight: 1.20
    letterSpacing: -0.16px
  body-lg:
    fontFamily: PPMori
    fontSize: 20px
    fontWeight: 400
    lineHeight: 1.50
    letterSpacing: -0.16px
  body:
    fontFamily: PPMori
    fontSize: 18px
    fontWeight: 400
    lineHeight: 1.40
    letterSpacing: -0.16px
  body-sm:
    fontFamily: PPMori
    fontSize: 16px
    fontWeight: 400
    lineHeight: 1.50
    letterSpacing: 0
  caption:
    fontFamily: PPMori
    fontSize: 14px
    fontWeight: 400
    lineHeight: 1.30
    letterSpacing: -0.16px
  caption-sm:
    fontFamily: PPMori
    fontSize: 12px
    fontWeight: 400
    lineHeight: 1.40
    letterSpacing: 0
  button:
    fontFamily: PPMori
    fontSize: 18px
    fontWeight: 500
    lineHeight: 1.00
    letterSpacing: 0
  eyebrow:
    fontFamily: PPMori
    fontSize: 12px
    fontWeight: 600
    lineHeight: 1.40
    letterSpacing: 0.5px
  label:
    fontFamily: PPMori
    fontSize: 18px
    fontWeight: 500
    lineHeight: 1.00
    letterSpacing: -0.16px

rounded:
  xs: 6px
  sm: 10px
  md: 12px
  lg: 16px
  xl: 20px
  xxl: 32px
  pill: 9999px
  full: 9999px

spacing:
  xxs: 4px
  xs: 8px
  sm: 12px
  md: 16px
  lg: 20px
  xl: 24px
  xxl: 40px
  p-80: 80px
  p-96: 96px
  p-120: 120px
  p-132: 132px
  p-160: 160px
  section: 96px

components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.on-primary}"
    typography: "{typography.button}"
    rounded: "{rounded.xl}"
    padding: 18px 32px 16px
  button-primary-pressed:
    backgroundColor: "{colors.brand-blue-deep}"
    textColor: "{colors.on-primary}"
    typography: "{typography.button}"
    rounded: "{rounded.xl}"
    padding: 18px 32px 16px
  button-ghost:
    backgroundColor: "#131a381a"
    textColor: "{colors.ink}"
    typography: "{typography.button}"
    rounded: "{rounded.xl}"
    padding: 14px 20px 12px
  button-on-dark:
    backgroundColor: "{colors.surface-1}"
    textColor: "{colors.brand-blue-deep}"
    typography: "{typography.button}"
    rounded: "{rounded.pill}"
    padding: 12px 20px 10px
  pill-badge:
    backgroundColor: "{colors.surface-2}"
    textColor: "{colors.primary}"
    typography: "{typography.label}"
    rounded: "{rounded.lg}"
    padding: 11px 16px 9px
  fact-pill:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.accent}"
    typography: "{typography.card-title}"
    rounded: "{rounded.pill}"
    padding: 24px
  pricing-card:
    backgroundColor: "{colors.surface-2}"
    textColor: "{colors.ink}"
    typography: "{typography.body}"
    rounded: "{rounded.xxl}"
    padding: 32px
  pricing-card-featured:
    backgroundColor: "{colors.ink}"
    textColor: "{colors.on-primary}"
    typography: "{typography.body}"
    rounded: "{rounded.xxl}"
    padding: 32px
  feature-card:
    backgroundColor: "{colors.surface-1}"
    textColor: "{colors.ink}"
    typography: "{typography.body}"
    rounded: "{rounded.lg}"
    padding: 24px
  integration-card:
    backgroundColor: "{colors.surface-2}"
    textColor: "{colors.accent}"
    typography: "{typography.body}"
    rounded: "{rounded.xxl}"
    padding: 32px
  product-mockup-card:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    typography: "{typography.body}"
    rounded: "{rounded.xxl}"
    padding: 0px
  content-image-frame:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    typography: "{typography.caption}"
    rounded: "{rounded.xxl}"
    padding: 0px
  testimonial-card:
    backgroundColor: "{colors.surface-1}"
    textColor: "{colors.ink}"
    typography: "{typography.body-lg}"
    rounded: "{rounded.lg}"
    padding: 32px
  customer-logo-tile:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink-tertiary}"
    typography: "{typography.caption}"
    rounded: "{rounded.xs}"
    padding: 16px
  text-input:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    typography: "{typography.body}"
    rounded: "{rounded.lg}"
    padding: 14px 16px
  text-input-focused:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    typography: "{typography.body}"
    rounded: "{rounded.lg}"
    padding: 14px 16px
  pricing-tab-default:
    backgroundColor: "{colors.surface-2}"
    textColor: "{colors.ink-muted}"
    typography: "{typography.button}"
    rounded: "{rounded.pill}"
    padding: 12px 24px 10px
  pricing-tab-selected:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.on-primary}"
    typography: "{typography.button}"
    rounded: "{rounded.pill}"
    padding: 12px 24px 10px
  faq-row:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.primary}"
    typography: "{typography.card-title}"
    rounded: "{rounded.xxl}"
    padding: 32px 48px 16px
  cta-banner:
    backgroundColor: "{colors.surface-2}"
    textColor: "{colors.ink}"
    typography: "{typography.headline}"
    rounded: "{rounded.xxl}"
    padding: 48px
  announcement-banner:
    backgroundColor: "#131a38"
    textColor: "{colors.on-primary}"
    typography: "{typography.caption}"
    rounded: "{rounded.xs}"
    padding: 18px 48px
  top-nav:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    typography: "{typography.body-sm}"
    rounded: "{rounded.lg}"
    height: 64px
  footer:
    backgroundColor: "{colors.brand-blue-deep}"
    textColor: "{colors.on-primary}"
    typography: "{typography.caption}"
    rounded: 120px
    padding: 120px 48px 96px
---

## Overview

Rootly's marketing canvas is a clean **white** ground (`{colors.canvas}`) — the airy, high-contrast base that lets blue-tinted cards and a vivid blue accent do the talking. This is a product-led SaaS surface: the product is the protagonist, and the marketing chrome stays light, rounded, and friendly.

On top of white sit **blue-white cards** (`{colors.surface-1}` #fafbff) and **blue tint tiles** (`{colors.surface-2}` #f3f6ff), with **blue borders** (`{colors.hairline}` #e3eaff) framing mockups and image frames. Corners are generous — `{rounded.lg}` 16px on small cards and `{rounded.xxl}` 32px on the large product and integration frames.

The brand is **blue**, split into two roles. The **deep blue** (`{colors.primary}` #0a1589) is the system primary: it paints the primary CTA, the pill section badges, and inline links. The **vivid blue** (`{colors.accent}` #2b4bff) is the expressive accent: it lives in gradient text, icon glows, and hover states. A **midnight blue** (`{colors.brand-blue-deep}` #06105a) grounds the footer and large dark blue panels.

Display type is **PP Mori** — Pangram Pangram's geometric grotesque — with body copy at its **light weight (200)**, which is what gives Rootly pages their airy, editorial texture. A second family, **PP Editorial Old** (a serif), appears at light weight for oversized editorial accents — a deliberate contrast against the geometric sans. Rootly writes gradient runs inside headlines: a blue→pink (`{colors.accent}` → `{colors.gradient-pink}`) span for emphasis, and a blue→amber (`{colors.accent}` → `{colors.gradient-amber}`) treatment on `<strong>`.

The page rhythm is anchored top and bottom: a **dark-navy announcement banner** (`#131a38`) sits above the nav, and a **deep blue footer** (`{colors.brand-blue-deep}`) with a 120px rounded top closes the page.

**Key Characteristics:**
- **White canvas** (`{colors.canvas}`) is the default ground; blue tints (`{colors.surface-1}`, `{colors.surface-2}`) carry cards and section blocks.
- **Deep blue primary** (`{colors.primary}` #0a1589) on CTAs and pill badges; **vivid blue** (`{colors.accent}` #2b4bff) for gradient accents and icons.
- **PP Mori** carries the entire marketing hierarchy; **PP Editorial Old** serif at light weight is the display accent.
- Body copy runs at **PP Mori light (200)** — the signature airy texture — with headings at weight 500.
- **Gradient text** is a brand device: blue→pink spans and blue→amber `<strong>` runs.
- Rounded and pill-heavy: `{rounded.xl}` 20px buttons, `{rounded.xxl}` 32px frames, `{rounded.pill}` badges and chips.
- **Accent-tinted soft shadows** (`0 0 18px #2b4bff33`, `0 2px 5px #0a158933`) — not neutral gray drop shadows.
- Deep blue **rounded-top footer** (120px radius) and dark-navy announcement banner are the vertical anchors.

## Colors

> Source pages: rootly.com (home), /pricing, /ai-sre, /on-call, /incident-response, /retrospectives, /integrations.

### Brand & Accent
- **Brand Blue** ({colors.primary}): The system primary at #0a1589. Primary CTA background, section pill badges, inline links, and label text.
- **Vivid Blue** ({colors.accent}): The expressive accent at #2b4bff. Gradient text, icon glows (`box-shadow 0 0 18px #2b4bff33`), CTA hover, and small subtitle emphasis.
- **Deep Blue** ({colors.brand-blue-deep}): #06105a — the footer ground and large dark blue panels; also the deep end of the text-on-light ramp.
- **Blue 500** ({colors.blue-500}): #7d95ff — the mid stop in the blue ramp, used in icon artwork and tints.
- **Amber** ({colors.amber}): #f0883e — the warm secondary accent, seen in gradient endpoints and highlight details.
- **Amber Soft** ({colors.amber-soft}): #ffc387 — the light amber used as a gradient stop in the 45° decorative wash.

### Surface
- **Canvas** ({colors.canvas}): Default page background — pure white.
- **Surface 1** ({colors.surface-1}): #fafbff — blue-white, used for floating cards and the soft fade overlays over imagery.
- **Surface 2** ({colors.surface-2}): #f3f6ff — the primary blue tint for badges, integration cards, section blocks, and pricing panels.
- **Surface 3** ({colors.surface-3}): #e3eaff — the strongest blue tint, used for image wells and accent fills.
- **Hairline** ({colors.hairline}): 1px blue border (#e3eaff) framing content mockups and cards.
- **Hairline Soft** ({colors.hairline-soft}): #c7d6ff — the deeper blue rule for dividers and gradient stops.

### Text
- **Ink** ({colors.ink}): Headlines, body type, button-adjacent labels — near-black #100f12.
- **Ink Muted** ({colors.ink-muted}): Secondary type at #65646e — metadata, captions, deselected tabs.
- **Ink Subtle** ({colors.ink-subtle}): Tertiary type at #787685 — helper text, hints on light surfaces.
- **Ink Tertiary** ({colors.ink-tertiary}): Quaternary type at #aaa9ae — eyebrows, disabled labels, muted meta.
- **Inverse Ink** ({colors.inverse-ink}): White type on the deep blue footer and dark-navy banner.
- **Inverse Ink Muted** ({colors.inverse-ink-muted}): #cbd3e8 — muted links and meta on dark grounds.

### Dark & Inverse
- **Inverse Canvas** ({colors.inverse-canvas}): #111319 — the darkest section ground for dark marketing blocks and dark pricing cards.
- **Inverse Surface 1** ({colors.inverse-surface-1}): #3a4566 — the slate lift used in the announcement banner overlay and nav dropdown ground.
- **Announcement Navy**: #131a38 — the dark-navy announcement bar that sits above the nav.

### Semantic
- **Error Red** ({colors.semantic-error}): #c62424 — form validation and destructive states.
- **Success Green** ({colors.semantic-success}): #25742d — positive states and confirmations.

### Brand Gradients
Rootly uses gradient *type*, not flat color, for emphasis:
- **Blue → Pink** (`linear-gradient(112deg, {colors.accent}, {colors.gradient-pink} 80%)`) — the headline emphasis span.
- **Blue → Amber** (`linear-gradient(90deg, ..., {colors.gradient-amber} ...)`) — the `<strong>` treatment running `{colors.accent}` → `{colors.blue-500}` → `{colors.gradient-amber}`.
- **Blue wash** (`linear-gradient(45deg, {colors.hairline-soft}, {colors.hairline-soft} 35%, {colors.amber-soft} 83%, {colors.hairline-soft})`) — decorative panel fill.

These gradients are applied to *text* (via `background-clip: text`) as often as to fills — they are a typographic device, not a background wash.

## Typography

### Font Family

- **PP Mori** (`Ppmori`, fallback `Arial, sans-serif`) — the primary geometric grotesque. Carries display, headings, body, eyebrow, and button. This is the whole marketing hierarchy.
- **PP Editorial Old** (`Ppeditorialold`, fallback `"Times New Roman", serif`) — a serif used at light weight (200) for oversized editorial display accents only.

Hierarchy is carried by size + weight + family change — Rootly *does* swap families at the largest display sizes, from PP Mori to PP Editorial Old, unlike single-family systems.

Note that Rootly sets base `body` to `font-weight: 200` (PP Mori Light). It is the light body weight — not a heavier regular — that produces the brand's airy, editorial texture.

### Hierarchy

| Token | Size | Weight | Line Height | Letter Spacing | Use |
|---|---|---|---|---|---|
| `{typography.display-xxl}` | 96px | 200 | 1.10 | -1.6px | PP Editorial Old serif hero / section accent |
| `{typography.display-xl}` | 60px | 500 | 1.03 | -0.16px | Largest PP Mori hero headline (h1) |
| `{typography.display-lg}` | 54px | 500 | 1.05 | -0.16px | Large section opener |
| `{typography.display-md}` | 48px | 500 | 1.20 | -0.16px | Sub-section headline (h3) |
| `{typography.headline}` | 40px | 500 | 1.15 | -0.16px | Smaller headline / CTA banner |
| `{typography.subhead}` | 36px | 500 | 1.10 | -0.16px | Compact section headline |
| `{typography.card-title}` | 26px | 600 | 1.20 | -0.16px | FAQ questions, fact pills, card titles |
| `{typography.body-lg}` | 20px | 400 | 1.50 | -0.16px | Lead / large body |
| `{typography.body}` | 18px | 400 | 1.40 | -0.16px | Default body |
| `{typography.body-sm}` | 16px | 400 | 1.50 | 0 | Card body, nav |
| `{typography.caption}` | 14px | 400 | 1.30 | -0.16px | Footer links, captions |
| `{typography.caption-sm}` | 12px | 400 | 1.40 | 0 | Fine meta |
| `{typography.button}` | 18px | 500 | 1.00 | 0 | Button labels |
| `{typography.eyebrow}` | 12px | 600 | 1.40 | 0.5px | Uppercase eyebrow/label (`.title`) |
| `{typography.label}` | 18px | 500 | 1.00 | -0.16px | Pill badge text (`.title-label`) |

### Principles

- **Light weight carries body.** PP Mori at weight 200 is the default body weight — the airy texture is the brand.
- **Weight 500 carries display.** Headings sit at medium (500); card titles step up to semibold (600).
- **Negative tracking is nearly constant** at -0.16px across headings, body, and buttons, tightening only to -1.6px on the largest serif display.
- **Line-heights tighten on display, relax on body.** 1.03 on the 60px h1, 1.40–1.50 on body.
- **One serif, one sans.** PP Editorial Old is reserved for oversized display accents; everything else is PP Mori.
- **Eyebrows are uppercase, tracked, and small** — 12px / 600 / 0.5px, colored `{colors.ink-tertiary}`.
- **Gradient emphasis replaces bold color.** Emphasis inside headlines uses gradient-clipped spans, not a different text color.

### Note on Font Substitutes

Both PP Mori and PP Editorial Old are commercial Pangram Pangram typefaces. If implementing without them, **Geist Sans**, **Inter**, or **General Sans** approximate PP Mori (use light 200/300 for body and 500 for display); the serif accent can be substituted with **Source Serif 4 Light** or **Lora Light**. Body copy should still lean light — that weight choice is the brand.

## Layout

### Spacing System

- **Base unit**: 8px.
- **Core tokens**: `{spacing.xs}` 8px · `{spacing.sm}` 12px · `{spacing.md}` 16px · `{spacing.lg}` 20px · `{spacing.xl}` 24px · `{spacing.xxl}` 40px.
- **Section-scale padding** (Rootly's `--size--p-*` tokens): `{spacing.p-80}` 80px · `{spacing.p-96}` 96px · `{spacing.p-120}` 120px · `{spacing.p-132}` 132px · `{spacing.p-160}` 160px.
- Hero uses `padding-top: 10rem` (160px) and `padding-bottom` at the p-132 (132px) scale.
- Card interior padding: 24px on feature/testimonial cards; 32px on pricing and integration cards; 32px / 48px on FAQ rows; 48px on CTA banners.
- Button padding: 18px top · 32px horizontal · 16px bottom (asymmetric to optically center the label).

### Grid & Container

- Max content width sits around 1440px (the footer container caps at `90rem`, with 48px gutters).
- Hero button pairs use a centered auto-width 2-column grid with 16px gaps; button groups wrap at ~60% width with 32px gaps.
- Card grids are commonly 3-up at desktop, 2-up at tablet, 1-up at mobile.
- Integration and product mockup frames span full content width or sit at ~1020px max — they're the protagonist of the section.
- Footer link grid is 4-up (3 link columns + 1 auto) with 64px gaps inside a 160px-gap stacked container.

### Whitespace Philosophy

White space plus the blue tint carries the structure. Rootly sections separate by large vertical padding (`{spacing.p-96}`–`{spacing.p-160}`) and by tint changes — a `{colors.surface-2}` blue block, a white block, a dark `{colors.inverse-canvas}` block — rather than by heavy borders or dividers.

## Elevation & Depth

| Level | Treatment | Use |
|---|---|---|
| 0 (flat) | No shadow, no border | Body type, hero text, footer |
| 1 (tint lift) | `{colors.surface-1}` / `{colors.surface-2}` on `{colors.canvas}` | Feature cards, fact pills, section blocks |
| 2 (border lift) | Tint + 1px `{colors.hairline}` blue border | Content mockups, integration frames, cards |
| 3 (glow lift) | Accent-tinted soft shadow (`0 0 18px #2b4bff33`, `0 2px 5px #0a158933`) | Floating icons, hovered cards |
| 4 (deep) | `{colors.brand-blue-deep}` / `{colors.inverse-canvas}` | Footer panel, dark sections, featured pricing |

Rootly's shadows are **accent-tinted, low-opacity, and diffuse** (`0 0 18px #2b4bff33`, `0 0 20px #2b4bff4d`, `0 5px 11px #b3c4f559`) — never neutral gray. Cards default to a transparent shadow and animate the shadow in on hover.

### Decorative Depth

- **Product UI mockups** dominate every major section — hero screen captures, integration frames, and dashboard imagery, framed in white/blue with `{rounded.xxl}` 32px corners.
- **Glass surfaces**: secondary buttons use `backdrop-filter: blur(96px)` over `#131a381a` — a frosted slate effect.
- **Gradient washes** appear as decorative panel fills and as fade overlays over imagery (`linear-gradient(#fafbff00, {colors.surface-1} 80%)`).
- **Floating integration icons** sit at slight rotations (±18°) with blue glow shadows.

## Shapes

### Border Radius Scale

| Token | Value | Use |
|---|---|---|
| `{rounded.xs}` | 6px | Small chips, badges |
| `{rounded.sm}` | 10px | Compact inline tags (0.6rem) |
| `{rounded.md}` | 12px | Small controls, image chips |
| `{rounded.lg}` | 16px | Cards, nav items, inputs |
| `{rounded.xl}` | 20px | All primary and ghost buttons |
| `{rounded.xxl}` | 32px | Product/integration frames, pricing cards, FAQ rows |
| `{rounded.pill}` | 9999px | Pill badges, chips, on-dark buttons |
| `{rounded.full}` | 9999px | Avatar circles, logo tiles |

Rootly also uses **40px** radii on oversized panels and a distinctive **120px** radius on the footer's top corners. Corners are always soft; nothing in the system is square.

### Photography & Illustration Geometry

- Product UI screenshots dominate the marketing surface; they sit in `{rounded.xxl}` 32px frames, often with a 1px `{colors.hairline}` blue border and a `{colors.surface-2}` well behind them.
- Hero product imagery runs full-bleed within a white rounded container, cropped to the top (`object-position: 50% 0%`).
- Customer logo tiles render small (~1.6rem logo height) inside fixed 9rem × 3rem cells with no border.
- Avatar circles in testimonial cards use `{rounded.full}` at ~2.6rem with a 1px `{colors.hairline}` ring.
- Gradient-clipped text and gradient decorative panels are the only "illustration" language — no hand-drawn spot illustrations.

## Components

### Buttons

**`button-primary`** — Deep blue CTA. The default primary across all pages.
- Background `{colors.primary}` (#0a1589), text `{colors.on-primary}`, type `{typography.button}` (18px / 500 / lh 1), radius `{rounded.xl}` 20px, padding 18px 32px 16px.
- Sits in centered pairs (`Get started for free` + `Book a demo`) with 16px gaps.

**`button-primary-pressed`** — Pressed / hover state. Deepens toward `{colors.brand-blue-deep}`; label swaps via an in-button text roll animation.

**`button-ghost`** — Frosted secondary. Used for lighter secondary actions and chips.
- `backdrop-filter: blur(96px)`, background `#131a381a`, text `{colors.ink}`, radius `{rounded.xl}`, padding 14px 20px 12px.

**`button-on-dark`** — White pill CTA on dark blue/dark grounds.
- Background `{colors.surface-1}` or `{colors.canvas}`, text `{colors.brand-blue-deep}`, radius `{rounded.pill}`, padding 12px 20px 10px.

### Badges & Pills

**`pill-badge`** (`.title-label`) — Section eyebrow badge.
- Background `{colors.surface-2}` (blue), text `{colors.primary}`, type `{typography.label}` (18px / 500 / -0.16px), radius `{rounded.lg}` 16px, padding 11px 16px 9px.

**`fact-pill`** (`.fact-card-parent`) — Rounded stat/fact capsule.
- Background `{colors.canvas}` white, text `{colors.accent}` (#2b4bff), border 1px `#e0e8ff`, type 20px / 600 / lh 1, radius `{rounded.pill}`, padding 24px.

### Pricing Tabs

**`pricing-tab-default`** + **`pricing-tab-selected`** — Pill toggle on `/pricing`.
- Default: `{colors.surface-2}` blue background, `{colors.ink-muted}` text, radius `{rounded.pill}`.
- Selected: `{colors.primary}` deep blue background, `{colors.on-primary}` text — selected = fill with brand blue.

### Cards & Containers

**`pricing-card`** — Each tier on `/pricing`.
- Background `{colors.surface-2}` blue, text `{colors.ink}`, type `{typography.body}`, radius `{rounded.xxl}`, padding 32px.

**`pricing-card-featured`** — Featured tier — inverts to dark.
- Background `{colors.ink}` (#100f12) or `{colors.inverse-canvas}`, text `{colors.on-primary}`, otherwise identical structure.

**`integration-card`** — Feature + screenshot pairing (`.integration-card`).
- Background `{colors.surface-2}` blue, 1px white border, radius `{rounded.xxl}` 32px, max-width ~1020px, two-column layout (copy + image), overflow hidden.

**`feature-card`** — Generic feature/tint card (`.event-card`).
- Background `{colors.surface-1}` or `{colors.surface-2}`, text `{colors.ink}`, radius `{rounded.lg}` 16px, padding 24px, box-shadow animated in on hover (from transparent).

**`product-mockup-card`** — The dominant media frame — holds a high-fidelity product UI screenshot or video.
- Background `{colors.canvas}` (often over a `{colors.surface-2}` well), radius `{rounded.xxl}` 32px, zero interior padding, overflow hidden.

**`content-image-frame`** (`.content-card-img-wrap`) — Bordered image well.
- 1px `{colors.hairline}` (#e3eaff) border, radius `{rounded.xxl}` 32px, `{colors.surface-1}` background behind imagery.

**`testimonial-card`** — Customer quote with avatar + name + role.
- Background `{colors.surface-1}`, text `{colors.ink}`, type `{typography.body-lg}`, radius `{rounded.lg}`, padding 32px.

**`customer-logo-tile`** (`.philos-logo-card`) — Fixed logo cell in the customer/philosophy marquee.
- Background `{colors.canvas}`, ~9rem × 3rem, no border, logos scaled to ~1.6rem height.

**`cta-banner`** — Closing CTA panel near page bottom.
- Background `{colors.surface-2}` blue (or a dark/gradient variant), text `{colors.ink}`, type `{typography.headline}`, radius `{rounded.xxl}`, padding 48px.

### Inputs & Forms

**`text-input`** + **`text-input-focused`** — Form fields on contact, demo, and search surfaces.
- Background `{colors.canvas}`, text `{colors.ink}`, type `{typography.body}`, radius `{rounded.lg}` 16px, padding 14px 16px. Focus shifts the border toward `{colors.primary}`.

### FAQ

**`faq-row`** (`.question-card-parent`) — Expandable accordion row.
- Background `{colors.canvas}` white, text `{colors.primary}`, border 1px `{colors.hairline}` (#e3eaff), type 26px / 600 / lh 1.2 (overriding to `{typography.card-title}`), radius `{rounded.xxl}` 32px, padding 32px 48px 16px.

### Navigation

**`top-nav`** — Sticky white bar with the Rootly wordmark left, mega-menu nav links center-left, and a `Log in` / `Book a demo` action pair right.
- Background `{colors.canvas}`, text `{colors.ink}`, type `{typography.body-sm}`, nav items radius `{rounded.lg}` 16px with a blue hover fill.
- `Log in` is a text/ghost link; `Book a demo` is `button-primary` deep blue.

**`announcement-banner`** (`.nav-banner-wrap-2`) — Dark-navy bar above the nav that cycles campaign messages.
- Background `#131a38`, text `{colors.on-primary}`, type `{typography.caption}`, padding 18px 48px. A slate overlay (`{colors.inverse-surface-1}` #3a4566) is used for the dropdown variant.

### Footer

**`footer`** (`.footer`) — Deep blue rounded-top panel closing every page.
- Background `{colors.brand-blue-deep}` (#06105a), border-top-left/right-radius 120px, white wordmark, 4-column link grid.
- Links: `{colors.on-primary}` white, `{typography.caption}` 14px, -0.16px, line-height 1.3. Container caps at 1440px with 160px internal gaps.

## Do's and Don'ts

### Do

- Keep `{colors.canvas}` white as the default ground and lift content with blue tints (`{colors.surface-1}`, `{colors.surface-2}`).
- Use `{colors.primary}` (#0a1589) for the primary CTA, pill badges, and links.
- Use `{colors.accent}` (#2b4bff) for gradient text, icon glows, and hover states.
- Run body copy at PP Mori light (200) and headings at 500 — the weight contrast is the brand.
- Round generously: `{rounded.xl}` 20px buttons, `{rounded.xxl}` 32px frames, `{rounded.pill}` badges.
- Reserve PP Editorial Old serif for oversized display accents only.
- Tint shadows (`#2b4bff33`, `#0a158933`) rather than using neutral gray.
- Use gradient-clipped text for emphasis inside headlines instead of switching text colors.

### Don't

- Don't use a cream or gray canvas — Rootly's ground is white.
- Don't introduce a third family; the system is PP Mori + PP Editorial Old serif.
- Don't set body copy at regular/bold weight — the light body is intentional.
- Don't swap the deep blue CTA for a black or neutral one.
- Don't use neutral gray drop shadows.
- Don't square off corners or build sharp-cornered cards.
- Don't use the serif for body or UI text — it is a display accent only.
- Don't over-apply gradients — they are for emphasis text, glows, and decorative washes, not full section backgrounds at the top of the page.
- Don't promote `{colors.semantic-error}` / `{colors.semantic-success}` to brand surfaces.

## Responsive Behavior

### Breakpoints

| Name | Width | Key Changes |
|---|---|---|
| Desktop-XL | 1440px | Default desktop layout; 90rem container |
| Desktop | 1280px | Card grids 3-up maintained |
| Tablet | 1024px | Card grids 3-up → 2-up; radius tokens step down (20px → 16px buttons, 32px → 32px frames) |
| Mobile-Lg | 768px | Nav collapses to hamburger; hero padding-top reduces ~10rem → ~6rem; footer radius shrinks |
| Mobile | 480px | Single-column; `{typography.display-xl}` 60px scales toward 36px; footer top radius reduces toward 32px |

Rootly scales its type tokens with the viewport: the same `t-60px` token resolves to 60px on desktop, 48px at tablet, and 36px at mobile — a fluid token system rather than fixed jumps.

### Touch Targets

- CTAs hold ≥48px tap height across viewports (18px + 16px padding + label).
- Pill badges and tabs hold ≥40px tap height.
- Form inputs hold ≥48px tap target on touch.

### Collapsing Strategy

- **Top nav**: mega-menu links collapse to a hamburger below 768px; the `Book a demo` CTA stays visible.
- **Announcement banner**: single-line message truncates or shortens below 768px.
- **Card grids**: 3-up → 2-up at 1024px → 1-up below 768px.
- **Integration cards**: two-column copy+image stack to single column below 768px.
- **Footer**: 4-column link grid → 2-up at tablet → single stacked column at mobile; the 120px top radius reduces.
- **Display type**: `{typography.display-xl}` 60px scales toward `{typography.display-md}`; the serif `{typography.display-xxl}` uses viewport units (e.g. 6.8vw) at the largest sizes.

### Image Behavior

- Product UI screenshots maintain aspect ratio; hero imagery is cropped to the top rather than squashed.
- Customer logos in the marquee collapse from many-up to 3-up below 768px.
- Gradient fades (`linear-gradient(#fafbff00, {colors.surface-1} 80%)`) overlay the bottom of tall imagery so it dissolves into the card.

## Iteration Guide

1. Focus on ONE component at a time and reference it by its `components:` token name.
2. When introducing a section, decide first whether it sits on `{colors.canvas}` white (default) or lifts into a `{colors.surface-2}` blue block or a dark `{colors.inverse-canvas}` block.
3. Default body to `{typography.body}` at PP Mori weight 200–400.
4. Run `npx @google/design.md lint DESIGN.md` after edits.
5. Add new variants as separate component entries.
6. Treat `{colors.accent}` (#2b4bff) as the expressive accent: gradients, glows, hovers — and `{colors.primary}` (#0a1589) as the CTA/badge color.
7. Lead every major section with a product screenshot in a `{rounded.xxl}` frame.
8. Keep letter-spacing at -0.16px across the type system unless a specific display size calls for more.

## Known Gaps

- The **vivid blue** (#2b4bff) and **deep blue** (#0a1589) are distinct roles; a few components use them interchangeably in the source and this document assigns the canonical role for each.
- PP Mori and PP Editorial Old are commercial typefaces; an open-source substitute (Geist, Inter, General Sans / Source Serif 4) is acceptable, and preserving the *light body weight* matters more than the exact family.
- The footer's 120px top radius is captured as a single value; it reduces at tablet and mobile breakpoints at values not fully enumerated in the public CSS.
- Form-field error and validation styling is not explicitly visible on the inspected pages beyond the `{colors.semantic-error}` token.
- Dark mode is not documented because the marketing site ships a single light theme (dark *sections* exist, but not a user dark mode).
- The in-product incident console, on-call, and AI SRE surfaces shown inside mockups use in-product UI states that are not formal marketing chrome.
- The marquee/carousel timing, announcement-banner rotation, and button text-roll animations are motion behaviors and are out of scope for this static token document.
