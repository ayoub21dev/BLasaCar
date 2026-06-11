# Charte Graphique - BlasaCar

This mockup folder now mirrors the current Laravel/Inertia design system with static HTML and CSS only.

## Source of Truth

- `resources/css/app.css`
- `resources/js/components/Layout.tsx`
- `resources/js/components/RideCard.tsx`
- `resources/js/components/MobileShell.tsx`
- Current page files under `resources/js/Pages`

## Typography

- Interface font: `Outfit`
- Display accent font: `Instrument Serif`
- Main headings use very heavy weights and tight line height.
- Buttons, labels, navigation, and dashboard chrome use compact bold text.

## Colors

- Brand 50: `#f2fbff`
- Brand 100: `#dff5ff`
- Brand 200: `#b8e8fb`
- Brand 400: `#46bee7`
- Brand 500: `#0ea5e9`
- Brand 600: `#0284c7`
- Brand 700: `#0369a1`
- Brand 900: `#0a3448`

Neutral surfaces follow the Laravel slate palette:

- Page background: `#f8fafc`
- Soft panel: `#ffffff`
- Main text: `#020617`
- Secondary text: `#64748b`
- Borders: `#e2e8f0`

## Layout Rules

- Public pages use `max-width: 1800px` shell containers.
- Home hero uses a dark rounded image panel with radius around `5rem`.
- Public cards use large radii from `2rem` to `3.5rem`.
- Dashboards use tighter `16px` radius panels and a `280px` left sidebar.
- Mobile screens use `8px` cards, slate-black primary actions, and a fixed bottom tab bar.

## Components

- Header: logo image plus `BlasaCar` text, centered pill navigation, login link, black sign-up pill.
- Ride card: driver avatar, route timeline, dashed connector, price, and blue booking CTA.
- Forms: slate-50 inputs with slate borders, focus ring in brand blue.
- Dashboard: white panels, slate-50 rows, status chips, compact tables.
- Footer: slate-100 translucent band with three columns and the current copyright line.
