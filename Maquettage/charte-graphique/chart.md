# Charte Graphique - BlassaCar

## 1. Introduction

BlassaCar is a Moroccan carpooling platform connecting drivers and passengers. The design emphasizes trust, safety, simplicity, and a modern aesthetic, utilizing a clean interface with clear typography and a soothing primary color palette.

## 2. Branding & Colors

The primary color scheme revolves around "Sky Blue" to evoke openness, travel, and reliability, complemented by neutral grays for structure and text.

### Primary Colors (Sky Blue)

- **Primary / Brand Color (Sky 500/600):** `#0ea5e9` / `#0284c7` - Used for primary buttons, active states, and important links.
- **Hover States (Sky 700):** `#0369a1`
- **Backgrounds & Highlights (Sky 50/100):** `#f0f9ff` / `#e0f2fe` - Used for alert boxes, soft backgrounds, and section highlights.

### Semantic & Status Colors

- **Success (Green):** `#22c55e` (500) / `#16a34a` (600) - Verified badges, completed status.
- **Danger (Red):** `#ef4444` (500) / `#dc2626` (600) - Error alerts, critical actions (delete/logout).
- **Warning (Amber/Yellow):** `#f59e0b` (Amber 500) / `#fbbf24` (Yellow 500) - Star ratings, pending alerts.
- **Info/Secondary (Blue/Purple):** `#2563eb` (Blue 600) / `#a855f7` (Purple 500) - Preference tags (AC, Music).

### Neutral Colors (Grays)

- **Backgrounds (Gray 50/100):** `#f9fafb` / `#f3f4f6` - Used for the main body background and card backgrounds.
- **Borders & Dividers (Gray 200/300):** `#e5e7eb` / `#d1d5db`
- **Text & Typography (Gray 500/700/900):**
  - _Secondary Text (Gray 500):_ `#6b7280`
  - _Primary Text (Gray 900):_ `#111827`

## 3. Typography

The project uses the **Inter** font family to ensure high legibility and a modern, sleek tech-startup feel.

- **Font Family:** `Inter`, sans-serif
- **Font Weights:**
  - Light (300)
  - Regular (400)
  - Medium (500)
  - Semi-Bold (600)
  - Bold (700)

## 4. UI Library & Frameworks

- **CSS Framework:** Tailwind CSS
- **UI Component Library:** Preline UI
- **Icons:** SVG icons with dynamic stroke colors.

## 5. UI Components Guidelines

- **Buttons:** Rounded corners (`rounded-lg`), bold text, with clear hover transitions (`transition-colors`).
- **Cards:** White backgrounds (`bg-white`), subtle shadows (`shadow-sm`), rounded borders (`rounded-xl`), and borders for structural definition (`border-gray-200`).
- **Forms:** Input fields should have soft borders, focusing to a primary sky blue ring.
- **Navigation:** Sticky sidebar or top header with clear contrast between the dark top bar (`bg-gray-900`) and the lighter content areas.

## 6. Layout Spacing

The layout follows Tailwind's default spacing scale, heavily relying on:

- Padding/Margin of `4` (1rem), `6` (1.5rem), and `8` (2rem) for structured spacing between sections and inside cards.
- Max-width containers (`max-w-7xl`) to keep the content centered and readable on large screens.
