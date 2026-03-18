# Product Requirements Document (PRD)

## UMKM Digital Marketplace Platform

**Version:** 2.0  
**Status:** Active  
**Last Updated:** March 2026  
**Platform:** Vue 3 + Inertia.js
**Target Users:** Indonesian UMKM (Small-Medium Enterprises) and Consumers
**Database Scheme:** /database/schema/mysql-schema.sql
**Path Implement:** /resources/js/frontend

---

## 1. Executive Summary

UMKM Toko Digital is a lightweight, user-friendly e-commerce platform designed specifically for Indonesian small business owners (UMKM) and their customers. The platform simplifies online selling by providing an intuitive interface that requires minimal technical knowledge, allowing local entrepreneurs to reach broader audiences while maintaining conversion-focused product discovery.

**Key Value Proposition:**

- Simplified product management for non-tech-savvy sellers
- Fast, responsive shopping experience optimized for mobile-first users
- Warm, welcoming design that builds trust with Indonesian consumers
- Focus on product discovery and quick checkout

---

## 2. Goals & Objectives

### Primary Goals

1. **Enable UMKM Growth** - Provide accessible tools for small businesses to sell online
2. **Drive Conversions** - Create a product-focused interface that encourages purchases
3. **Build Trust** - Design a warm, friendly interface reflecting Indonesian business culture
4. **Mobile Excellence** - Optimize for mobile-first users (primary market in Indonesia)

### Success Metrics

- Product page load time: < 2 seconds
- Mobile conversion rate: > 3%
- Cart abandonment rate: < 70%
- User engagement: Average 5+ products viewed per session
- Mobile traffic: > 75% of total traffic

---

## 3. Target Users

### Primary Users

1. **UMKM Owners/Sellers**

    - Age: 25-55 years old
    - Tech literacy: Low to moderate
    - Pain points: Lack technical skills, limited time, want quick setup
    - Goals: Sell products online, reach new customers, manage inventory

2. **Indonesian Consumers**
    - Age: 18-50 years old
    - Primary device: Mobile phone (smartphone)
    - Behavior: Browse quickly, prefer local products, seek good deals
    - Goals: Find quality products, compare prices, checkout quickly

---

## 4. Core Features

### 4.1 Home Page

#### Hero Section

- [x] **Specifications:**
    - Height: 200-250px (compact, not banner-heavy)
    - Content: Static promotional message with emoji
    - CTA: "Belanja Sekarang" button
    - Purpose: Showcase current offers without dominating screen space
    - Responsive: Scales appropriately on mobile

#### Category Menu (Below Header)

- [x] **Specifications:**
    - Layout: Horizontal scrollable pill buttons
    - Items: Dynamic categories from database
    - Scroll: Native smooth scroll on mobile
    - Active state: Background color changes to primary
    - Inactive state: Light background with dark text
    - Font size: 14px, semibold
    - "Semua" (All) option for showing all products

#### Pilihan Terbaik (Featured Products)

- [x] **Specifications:**
    - Display: 4 products in horizontal scrollable carousel
    - On mobile: 2 products visible
    - Purpose: Highlight featured products
    - Native scroll experience
    - Section title: "Pilihan Terbaik" (Best Choices)
    - Scroll navigation buttons (left/right arrows)

#### Flash Sale Section

- [x] **Specifications:**
    - Display: Up to 8 products in grid
    - Section title: Flash sale name from database
    - Countdown timer: "Berakhir dalam" (Ends in)
    - Timer format: HH:MM:SS with red destructive color
    - Background: Gradient from destructive color
    - "Lihat Semua Promo" link to flash sale page

#### All Products Grid

- [x] **Specifications:**
    - Layout: Consistent 4-column grid (desktop), 2-column (mobile)
    - Spacing: 16px (1rem) gaps
    - Section title: "Semua Produk" or "Hasil Pencarian: {query}"
    - Supports "Load More" pagination
    - Product count display

#### Newsletter Section

- [x] **Specifications:**
    - Email subscription form
    - "Berlangganan" (Subscribe) button
    - Indonesian language throughout

### 4.2 Product Cards

#### Visual Design

- [x] **Size:** Variable widths in featured, consistent in grid
- [x] **Border Radius:** 16px (rounded-2xl, friendly appearance)
- [x] **Shadow:** Default `shadow-md`, hover `shadow-lg` with smooth transition
- [x] **Hover Effects:**
    - Scale: 1.05 (5% enlarge)
    - Vertical lift: -4px (translate-y-1)
    - Smooth transition: 300ms duration
    - Active state: scale-95 on button press (tactile feedback)

#### Card Sections

**Image Container:**

- [x] Aspect ratio: 1:1 (square)
- [x] Background: Secondary color (warm cream)
- [x] Content: Product image or emoji
- [x] Hover effect: Image scales 10% on hover
- [x] Badge position: Top-right corner
    - Badge types: "Terlaris" (Best Seller), "Diskon" (Discount), "Baru" (New)
    - Badge color: Destructive (red) with white text
    - Font size: 12px, bold

**Content Section:**

- [x] Padding: 16px (1rem)
- [x] Layout: Flex column with space-between

**Category Badge:**

- [x] Display: Small pill/chip format
- [x] Text: Category name
- [x] Color: Primary text on secondary background
- [x] Font size: 12px, bold

**Product Name:**

- [x] Font size: 16px (base)
- [x] Font weight: Bold (font-bold)
- [x] Line clamp: 2 lines maximum
- [x] Color: Foreground (dark brown/charcoal)
- [x] Hover: Color changes to primary

**Description:**

- [x] Font size: 12px
- [x] Color: Muted foreground
- [x] Line clamp: 1 line maximum

**Price Section:**

- [x] Separator: Border-top (1px border)
- [x] Padding: 12px top
- [x] Layout: Flex items-end justify-between
- [x] Label: "Harga" (Price) in 12px muted gray
- [x] Price display:
    - Font size: 20px (1.25rem)
    - Font weight: Bold
    - Color: Primary (warm orange)
    - Format: Indonesian Rupiah "Rp X.XXX.XXX"

**Add to Cart Button:**

- [x] Icon: Shopping cart (20-24px)
- [x] Background: Primary color (warm orange)
- [x] Hover: Changes to accent color
- [x] Shape: Perfect circle (rounded-full)
- [x] Shadow: Default shadow-md, hover shadow-lg
- [x] Active state: scale-95 (visual feedback)
- [x] Visibility: Shows on hover (opacity-0 → opacity-100)
- [x] Accessibility: aria-label for screen readers

### 4.3 Shopping Cart

#### Cart Page

- [x] **Header:** "Keranjang Belanja" (Shopping Cart)
- [x] **Layout:** 2-column (items left, summary right on desktop)

#### Cart Items Display

- [x] **Per Item:**
    - Product image (small, 64px)
    - Product name with link to detail
    - Variant info if applicable
    - Unit price
    - Quantity controls (-, +)
    - Remove button (trash icon)
    - Subtotal (unit price × quantity)

#### Cart Summary

- [x] **Sections:**
    - Subtotal: Sum of all item prices
    - Tax estimate: 10% of subtotal
    - Total: Subtotal + tax
    - All values formatted as Indonesian Rupiah
- [x] **Styling:** Clear visual hierarchy with larger font for total

#### Cart Actions

- [x] **Primary CTA:** "Lanjut ke Pembayaran" (Proceed to Checkout)
    - Color: Primary (warm orange)
    - Size: Full width, rounded-full
- [x] **Secondary CTA:** "Lanjut Belanja" (Continue Shopping)

#### Empty State

- [x] **Message:** "Keranjang Anda kosong" (Your cart is empty)
- [x] **Icon:** Empty shopping cart (64px)
- [x] **CTA:** "Mulai Belanja" (Start Shopping) button

### 4.4 Header/Navigation

#### Header Layout

- [x] **Structure:** Logo | Search bar | Cart icon with badge
- [x] **Height:** 64px (desktop), 56px (mobile)
- [x] **Background:** White with subtle border bottom
- [x] **Sticky:** Remains visible while scrolling

#### Logo

- [x] **Content:** Text logo from settings or "UMKM"
- [x] **Color:** Primary (warm orange)
- [x] **Font:** Bold, 18-20px

#### Search Bar

- [x] **Visibility:** Full search bar on both desktop and mobile
- [x] **Placeholder:** "Cari produk..." (Search products...)
- [x] **Width:** Flexible, max-w-xl on desktop
- [x] **Icon:** Search icon (left side)
- [x] **Clear button:** X icon to clear search
- [x] **Behavior:** Filters products on Enter key press

#### Cart Icon

- [x] **Icon:** Shopping cart
- [x] **Badge:** Circular badge in top-right
- [x] **Badge content:** Item count (0-99+)
- [x] **Badge color:** Destructive (red)
- [x] **Badge styling:** 16px diameter, white text, bold

#### Mobile Menu

- [x] **Hamburger:** Menu icon on mobile
- [x] **Sidebar:** Slides from left
- [x] **Links:** Home, Keranjang, Akun

---

## 5. User Stories & Use Cases

### User Story 1: Browse Products (Consumer)

**As a** consumer  
**I want to** quickly browse available products  
**So that** I can find what I need without confusion

**Acceptance Criteria:**

- [x] Featured products section visible immediately after scroll
- [x] Product grid loads within 2 seconds
- [x] All products show name, price, image, and category
- [x] Can scroll horizontally on featured section without freezing
- [x] Products are clearly clickable/interactive

### User Story 2: Search & Filter (Consumer)

**As a** consumer  
**I want to** search for specific products and filter by category  
**So that** I can find exactly what I'm looking for quickly

**Acceptance Criteria:**

- [x] Search bar appears and is easily accessible
- [x] Search results show relevant products
- [x] Category filter reduces product list effectively
- [x] Can clear search/filters with one click
- [x] Results show relevant products

### User Story 3: Add Product to Cart (Consumer)

**As a** consumer  
**I want to** add products to cart with one click  
**So that** I can quickly build my order

**Acceptance Criteria:**

- [x] Add to cart button is always visible on product card (on hover)
- [x] Cart count updates immediately when I add items
- [x] Visual feedback (button animation) confirms action
- [x] Can add multiple quantities of same product
- [x] Redirects to login if not authenticated

### User Story 4: Review & Checkout (Consumer)

**As a** consumer  
**I want to** review my cart items and totals before checkout  
**So that** I can verify my order is correct

**Acceptance Criteria:**

- [x] Cart displays all items with quantities and prices
- [x] Subtotal and tax are clearly calculated and shown
- [x] Can modify quantities directly in cart
- [x] Can remove items from cart
- [x] Total is prominently displayed
- [x] "Proceed to Checkout" button is clear and accessible

### User Story 5: Responsive Mobile Experience (All Users)

**As a** mobile user  
**I want to** browse and shop comfortably on my phone  
**So that** I can shop anywhere, anytime

**Acceptance Criteria:**

- [x] All content fits on screen without horizontal scroll (except carousel)
- [x] Touch targets are at least 48x48px
- [x] Buttons and inputs respond quickly to taps
- [x] Images load quickly and scale appropriately
- [x] Cart and navigation are easy to access on small screens

### User Story 6: User Authentication

**As a** consumer  
**I want to** create an account and login  
**So that** I can save my information and track orders

**Acceptance Criteria:**

- [x] Can register with email and password
- [x] Can login with email and password
- [x] Can reset password via email
- [x] Redirects to intended page after login

### User Story 7: Wishlist

**As a** consumer  
**I want to** save products to my wishlist  
**So that** I can buy them later

**Acceptance Criteria:**

- [x] Can add/remove products from wishlist
- [x] Can view all wishlist items
- [x] Wishlist count shown in header

### User Story 8: Order History

**As a** consumer  
**I want to** view my past orders  
**So that** I can track my purchases

**Acceptance Criteria:**

- [x] Can view list of past orders
- [x] Can view order details
- [x] Can see order status and payment method

### User Story 9: Flash Sale

**As a** consumer  
**I want to** see time-limited flash sale offers  
**So that** I can take advantage of special deals

**Acceptance Criteria:**

- [x] Flash sale section shows on home page
- [x] Countdown timer displays time remaining
- [x] Products show discounted prices

### User Story 10: Blog & Info Pages

**As a** consumer  
**I want to** read blog posts and informational pages  
**So that** I can learn more about products and the store

**Acceptance Criteria:**

- [x] Can view list of blog posts
- [x] Can read individual blog posts
- [x] Can view static pages (FAQ, About, etc.)

---

## 6. Technical Architecture

### Frontend Stack

- **Framework:** Vue 3 (Composition API)
- **Language:** JavaScript (with TypeScript types)
- **Styling:** Tailwind CSS v3
- **State Management:** Inertia.js + Vue reactivity
- **HTTP Client:** Inertia.js (Ziggy routes)
- **Build Tool:** Vite
- **UI Icons:** Lucide Vue
- **Notifications:** Vue Sonner

### Backend Stack

- **Framework:** Laravel 10+
- **Admin Panel:** Filament PHP
- **Database:** MySQL
- **Authentication:** Custom customer guard

---

## 7. Design System

### Color Palette

| Token       | Value                          | Usage                           |
| ----------- | ------------------------------ | ------------------------------- |
| Primary     | Warm Orange (`25 80% 55%`)     | CTAs, prices, accents           |
| Accent      | Lighter Orange (`35 95% 55%`)  | Hover states, secondary accents |
| Secondary   | Warm Cream (`40 30% 95%`)      | Product card backgrounds, chips |
| Destructive | Red (`348 80% 55%`)            | Badges, alerts, flash sale      |
| Background  | Off-white (`30 25% 99%`)       | Page background                 |
| Foreground  | Dark Brown (`20 30% 15%`)      | Text, primary copy              |
| Muted       | Light Gray (`30 5% 93%`)       | Subtle backgrounds              |
| Border      | Very Light Gray (`30 10% 92%`) | Dividers, borders               |

### Typography

| Element       | Font Family       | Size    | Weight        | Line Height |
| ------------- | ----------------- | ------- | ------------- | ----------- |
| Hero Title    | Plus Jakarta Sans | 32px    | Bold (700)    | 1.2         |
| Section Title | Plus Jakarta Sans | 24-28px | Bold (700)    | 1.2         |
| Product Name  | Plus Jakarta Sans | 16px    | Bold (700)    | 1.2         |
| Body Text     | Plus Jakarta Sans | 14px    | Regular (400) | 1.5         |
| Small Text    | Plus Jakarta Sans | 12px    | Regular (400) | 1.4         |
| Price         | Plus Jakarta Sans | 20px    | Bold (700)    | 1.2         |

### Spacing Scale

```
4px (0.25rem) - xs
8px (0.5rem) - sm
12px (0.75rem) - base
16px (1rem) - md
20px (1.25rem) - lg
24px (1.5rem) - xl
32px (2rem) - 2xl
48px (3rem) - 3xl
```

### Border Radius

```
8px (0.5rem) - sm (buttons, inputs)
12px (0.75rem) - md (cards, modals)
16px (1rem) - lg (featured cards, hero)
9999px - full (circular buttons, pills)
```

### Shadows

```
shadow-sm: 0 1px 2px rgba(0,0,0,0.05)
shadow-md: 0 4px 6px rgba(0,0,0,0.1)
shadow-lg: 0 10px 15px rgba(0,0,0,0.15)
```

---

## 8. Page Routes

| Page            | Route              | Description                                           |
| --------------- | ------------------ | ----------------------------------------------------- |
| Home            | `/`                | Main page with hero, categories, flash sale, products |
| Cart            | `/cart`            | Shopping cart page                                    |
| Checkout        | `/checkout`        | Checkout page (authenticated)                         |
| Orders          | `/orders`          | Order history (authenticated)                         |
| Order Detail    | `/orders/{id}`     | Single order details                                  |
| Account         | `/account`         | User profile (authenticated)                          |
| Wishlist        | `/wishlist`        | Saved products                                        |
| Flash Sale      | `/flash-sale`      | All flash sale products                               |
| Blog Index      | `/blog`            | Blog post list                                        |
| Blog Post       | `/blog/{slug}`     | Single blog post                                      |
| FAQ             | `/faq`             | Frequently asked questions                            |
| Page            | `/page/{slug}`     | Static pages                                          |
| Login           | `/login`           | User login                                            |
| Register        | `/register`        | User registration                                     |
| Forgot Password | `/forgot-password` | Password reset                                        |

---

## 9. API Endpoints (AJAX)

| Endpoint           | Method | Description               |
| ------------------ | ------ | ------------------------- |
| `/cart`            | GET    | View cart                 |
| `/cart/add`        | POST   | Add item to cart          |
| `/cart/{item}`     | PATCH  | Update cart item quantity |
| `/cart/{item}`     | DELETE | Remove cart item          |
| `/wishlist/toggle` | POST   | Toggle wishlist item      |

---

## 10. Responsive Breakpoints

| Breakpoint | Width          | Usage                  |
| ---------- | -------------- | ---------------------- |
| Mobile     | < 640px        | Small phones           |
| Tablet     | 640px - 1024px | Larger phones, tablets |
| Desktop    | > 1024px       | Computers              |

### Key Responsive Changes

- **Grid:** 2 columns (mobile) → 4 columns (desktop)
- **Hero:** Full padding (mobile) → Reduced padding (desktop)
- **Featured:** 2 products visible (mobile) → 4 (desktop)
- **Search:** Full search bar on all screens

---

## 11. Performance Requirements

### Load Time Targets

- Initial page load: < 2 seconds
- Product images: < 1 second
- Cart operations: < 300ms
- Search results: < 500ms

### Optimization Strategies

- Lazy load images below fold
- Compress product images (WebP)
- Minimize bundle size (< 150KB gzipped)
- Enable caching for static assets
- Optimize font loading (system fonts preferred)

### Mobile Optimization

- Mobile-first CSS approach
- Touch-friendly interactions (48px minimum targets)
- Optimize for 3G/4G connections

---

## 12. Accessibility (WCAG 2.1 AA)

### Requirements

- [x] Color contrast ratio minimum 4.5:1 for text
- [x] All images have alt text (or aria-label for icons)
- [x] Form inputs have associated labels
- [x] Keyboard navigation fully supported
- [x] Focus indicators clearly visible
- [x] Screen reader announcements for cart updates
- [x] Semantic HTML (buttons, links, heading hierarchy)

### Specific Implementations

- [x] All buttons: `aria-label` for icon-only buttons
- [x] Cart icon: Updates `aria-label` with item count
- [x] Product images: Decorative emoji marked as `aria-hidden`
- [x] Cart panel: Slide-in with proper transitions

---

## 13. Future Enhancements (Phase 2+)

### Phase 2 Features

- [ ] Product reviews and ratings
- [ ] Advanced filtering (price range)
- [ ] Product recommendations
- [ ] Push notifications
- [ ] Social sharing

### Phase 3 Features

- [ ] Payment gateway integration
- [ ] Order tracking with courier
- [ ] Seller dashboard
- [ ] Multiple product images per item
- [ ] Progressive Web App (PWA)

---

## 14. Constraints & Assumptions

### Constraints

- Budget: Limited marketing spend (organic growth priority)
- Team: Small team
- Timeline: MVP launch within 3 months
- Technology: Must work on low-bandwidth connections
- Localization: Indonesia-first, Indonesian language required

### Assumptions

- Users have access to stable mobile internet
- Users are comfortable with basic e-commerce flows
- Payment processing handled by admin manually
- Product catalog managed via Filament admin panel

---

## 15. Appendices

### A. Glossary

- **UMKM:** Usaha Mikro, Kecil, Menengah (Indonesian Small-Medium Enterprises)
- **MVP:** Minimum Viable Product
- **GMV:** Gross Merchandise Value
- **AOV:** Average Order Value

### B. Revision History

| Version | Date     | Author       | Changes                                        |
| ------- | -------- | ------------ | ---------------------------------------------- |
| 1.0     | Mar 2026 | Product Team | Initial PRD creation                           |
| 2.0     | Mar 2026 | Dev Team     | Added all implemented features, marked as done |

---

**Document End**

_This PRD is a living document and subject to change based on user feedback and market conditions._
