---
name: Artisanal Modernism
colors:
  surface: '#fdf8ff'
  surface-dim: '#ddd8e1'
  surface-bright: '#fdf8ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f7f2fb'
  surface-container: '#f1ecf5'
  surface-container-high: '#ebe6ef'
  surface-container-highest: '#e6e1ea'
  on-surface: '#1c1b21'
  on-surface-variant: '#484552'
  inverse-surface: '#312f36'
  inverse-on-surface: '#f4eff8'
  outline: '#797583'
  outline-variant: '#cac4d4'
  surface-tint: '#624eb1'
  primary: '#5f4cae'
  on-primary: '#ffffff'
  primary-container: '#7865c9'
  on-primary-container: '#fffbff'
  inverse-primary: '#cbbeff'
  secondary: '#a03f29'
  on-secondary: '#ffffff'
  secondary-container: '#fe876a'
  on-secondary-container: '#741f0b'
  tertiary: '#745800'
  on-tertiary: '#ffffff'
  tertiary-container: '#927000'
  on-tertiary-container: '#fffbff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#e6deff'
  primary-fixed-dim: '#cbbeff'
  on-primary-fixed: '#1d0061'
  on-primary-fixed-variant: '#4a3597'
  secondary-fixed: '#ffdad2'
  secondary-fixed-dim: '#ffb4a3'
  on-secondary-fixed: '#3d0700'
  on-secondary-fixed-variant: '#812914'
  tertiary-fixed: '#ffdf97'
  tertiary-fixed-dim: '#ecc155'
  on-tertiary-fixed: '#251a00'
  on-tertiary-fixed-variant: '#5a4400'
  background: '#fdf8ff'
  on-background: '#1c1b21'
  surface-variant: '#e6e1ea'
typography:
  display-lg:
    fontFamily: Playfair Display
    fontSize: 48px
    fontWeight: '700'
    lineHeight: '1.1'
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Playfair Display
    fontSize: 32px
    fontWeight: '700'
    lineHeight: '1.2'
  headline-md:
    fontFamily: Playfair Display
    fontSize: 32px
    fontWeight: '600'
    lineHeight: '1.3'
  headline-sm:
    fontFamily: Playfair Display
    fontSize: 24px
    fontWeight: '600'
    lineHeight: '1.4'
  body-lg:
    fontFamily: DM Sans
    fontSize: 18px
    fontWeight: '400'
    lineHeight: '1.6'
  body-md:
    fontFamily: DM Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: '1.6'
  body-sm:
    fontFamily: DM Sans
    fontSize: 14px
    fontWeight: '400'
    lineHeight: '1.5'
  data-price:
    fontFamily: JetBrains Mono
    fontSize: 16px
    fontWeight: '500'
    lineHeight: '1.0'
    letterSpacing: -0.03em
  data-label:
    fontFamily: JetBrains Mono
    fontSize: 12px
    fontWeight: '400'
    lineHeight: '1.0'
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  unit: 8px
  container-max: 1280px
  gutter: 24px
  margin-mobile: 16px
  margin-desktop: 40px
  stack-sm: 8px
  stack-md: 16px
  stack-lg: 32px
  section-gap: 80px
---

## Brand & Style

The design system is anchored in the intersection of Bolivian heritage and contemporary e-commerce. It evokes an **earthy, warm, and clean artisanal aesthetic**, prioritizing the tactile quality of hand-crafted goods through a digital lens. The visual language is sophisticated yet grounded, targeting global collectors and conscious donors who value transparency and cultural preservation.

The design style utilizes **Minimalism** with **Tactile** influences. By combining high-contrast editorial typography with a warm, organic color palette and generous negative space, the interface acts as a quiet gallery frame for the vibrant textures of Bolivian textiles and ceramics. The emotional response should be one of "Digital Serenity"—where the user feels the weight of tradition through a seamless, high-performance interface.

## Colors

This design system employs a palette inspired by the Andean landscape and natural pigments. 

- **Artisanal Violet (#7B68CC)**: Used for primary actions, brand moments, and gamification highlights. It represents the depth of Bolivian weaving traditions.
- **Terracotta (#E17055)**: A secondary color used for reservation status, warm call-to-outs, and earthen elements.
- **Andean Green (#1D9E75)**: Reserved for "Success" states, donation growth tracking, and environmental/sustainability labels.
- **Bone Background (#FAFAF8)**: The foundation of the system, providing a softer, more organic feel than pure white, reducing eye strain and mimicking natural paper or clay.
- **Text & Borders**: Contrast is maintained through a deep charcoal rather than black, ensuring legibility without harshness. Borders are kept extremely thin (0.5px) to maintain a "hair-line" precision that feels premium and intentional.

## Typography

The typographic hierarchy in this design system balances editorial elegance with functional clarity.

- **Headlines (Playfair Display)**: Used for storytelling, artisan names, and section titles. The serif nature adds a layer of established craftsmanship and authority.
- **UI & Body (DM Sans)**: Used for all transactional elements, descriptions, and navigational labels. Its low-contrast, geometric forms ensure readability across all device types.
- **Data & Pricing (JetBrains Mono)**: A monospaced font specifically for prices, QR codes, and reservation IDs. This creates a technical "receipt-style" contrast to the organic serifs, signaling precision in commerce and logistics.

## Layout & Spacing

This design system utilizes a **Fixed Grid** approach for desktop surfaces to ensure content remains centered and curated, like a luxury lookbook. Mobile layouts transition to a **Fluid Grid** with 16px side margins.

Generous whitespace is a core principle. Elements are given room to breathe to prevent the interface from feeling cluttered or "cheap." Section gaps are intentionally large (80px+) to clearly demarcate different artisan stories or product categories. The 8px spacing system governs all internal paddings, ensuring a consistent rhythm.

## Elevation & Depth

This design system rejects heavy shadows in favor of **Structural Flatness**. Depth is communicated through:

1.  **Low-contrast outlines:** 0.5px hair-line borders in `rgba(0,0,0,0.10)` define surfaces without lifting them aggressively off the background.
2.  **Tonal Layers:** Using the `Background` (#FAFAF8) for the canvas and `Surface` (#FFFFFF) for interactive cards or containers.
3.  **Flat Stacking:** Elements feel like layers of fine paper or fabric resting on one another. If a shadow must be used for a floating action button or a modal, it should be a "ghost shadow": highly diffused, 10% opacity, with a 20px blur and 0px offset.

## Shapes

The shape language is characterized by **Soft Geometric** forms. While the grid is rigid, the components themselves utilize a 12px to 16px corner radius.

- **Cards & Inputs:** Use a 12px radius for a friendly but modern feel.
- **Large Modals & Feature Banners:** Use a 16px radius.
- **Selection Chips:** Utilize a full pill-shape (32px+) to distinguish them from actionable buttons.
- **Icons:** Icons follow the Tabler style with a 1.25px stroke weight and slightly rounded terminals to match the UI's softness.

## Components

### Buttons
Primary buttons use the **Artisanal Violet** with white text. Secondary buttons are "Ghost" style—using a 0.5px hair-line border and the Primary Text color. All buttons feature 12px rounded corners and DM Sans Medium for labels.

### Cards
Artisan and product cards are white surfaces on the off-white background. They use no shadow, instead relying on the hair-line border for definition. Image aspect ratios should be strictly 4:5 or 1:1 to mimic gallery prints.

### Input Fields
Inputs are minimal, defined only by a bottom border or a full hair-line enclosure. Focus states transition the border from the default 10% black to the Artisanal Violet. Labels use **JetBrains Mono** at 12px for a "form-fill" aesthetic.

### Donation Progress & Gamification
Donation trackers use the **Andean Green** as a fill color. Progress bars are thin (4px height) with rounded caps. Gamified badges or donation milestones use the **Terracotta** color to feel warm and rewarding.

### Price Tags
Always rendered in **JetBrains Mono**. This font choice makes prices feel like part of a ledger or a gallery tag, separating the "cost" from the "story" told in Playfair Display.

### Icons
Use Tabler Icons (`ti-` prefix). Set icon strokes to 1.25px or 1.5px to maintain the "fine line" aesthetic of the system. Icons should always be accompanied by a label in smaller body text when used in navigation.