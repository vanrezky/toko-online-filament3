# Frontend Agent Instruction — Online Shop Platform

You are a **Senior Frontend Engineer & UI Architect**.
Your task is to build the **frontend (FE)** of an **online shop platform**.

## image reference: .docs/reference-images/ecommerce-template.reference.png

## path 
- resources/js/frontend




## 1. Context & Constraints

- Backend API, dashboard, dan demo product **SUDAH ADA**
- Frontend **BELUM ADA**
- Fokus utama: **customer-facing storefront**
- FE harus **template-based** dan **future-proof**
- Awal pengembangan: **1 template aktif**
- FE **tidak mengatur admin/dashboard**

## 2. Core Goal

Build a **modern, responsive online shop frontend** that:
- Can **switch templates** in the future
- Supports **digital & physical products**
- Supports **product variants**
- Clean, elegant, e-commerce–grade UI
- UX optimized for **desktop & mobile**

## 3. Design Style Reference

- Visual style: **modern fashion / lifestyle ecommerce**
- Clean layout, white space dominant
- Large product imagery
- Elegant typography
- Subtle borders & soft shadows
- Similar feeling to the attached reference image (fashion ecommerce style)
- Avoid heavy gradients or overly playful UI

## 4. Template System (IMPORTANT)

- Use a **Template Provider / Layout System**
- Example:
  - `DefaultTemplate`
  - `TemplateContext`
- All pages must be rendered **inside a template wrapper**
- Header, Footer, and layout spacing must be **template-driven**
- Switching template must NOT require rewriting pages

## 5. Global UI Components

### Topbar Notice
- Sticky / static bar at top
- Used for:
  - Promotion
  - Shipping info
  - Announcement
- Example text:
  - "Free Shipping Worldwide"
  - "Flash Sale ends in 2 hours"

### Header
- Logo
- Search
- Cart icon (with badge)
- Account / login icon
- Responsive (hamburger menu on mobile)

---

## 6. Pages & Features

### 6.1 Home Page

Sections (ordered):

1. **Hero Carousel**
   - Auto-slide
   - Image + headline + CTA
   - Responsive

2. **Flash Sale (1 Row)**
   - Horizontal product list
   - Countdown optional
   - Emphasized pricing

3. **Product Highlight (2 Rows)**
   - Featured products
   - Best seller / New arrival

4. **Category Highlight**
   - Each category = **1 row**
   - Example:
     - Shoes
     - Apparel
     - Accessories

#### Product Grid Rule
- 1 row contains:
  - **Desktop**: 4 or 6 cards (choose the most visually balanced)
  - **Mobile**: horizontal scroll or 2 columns

#### Product Card Must Show
- Product image
- Product name
  - Max 2 lines
  - Overflow → `...`
- Price
- Discount price (strikethrough original price)
- Badge (optional):
  - Sale
  - New
  - Digital

---

### 6.2 Product Detail Page

Include:

- Product gallery (image slider / thumbnails)
- Product name
- Price & discounted price
- Stock status
- Product type:
  - Digital
  - Physical
- Variant selection:
  - Size
  - Color
  - Other options
- Quantity selector
- Add to cart
- Description
- Specification / tabs
- Suggested products / related products

UX Notes:
- Disable Add to Cart until variant selected
- Variant change updates price & image

---

### 6.3 Cart Page

Features:
- List of cart items
- Change quantity
- Remove item
- Price summary
- Subtotal
- CTA to Checkout

---

### 6.4 Checkout Page

Steps:
- Shipping address
- Shipping method (ongkir)
- Order summary
- Payment CTA

Notes:
- Shipping cost must affect total
- Digital product:
  - Skip shipping selection

---

### 6.5 My Orders Page

- Order list
- Status:
  - Pending
  - Paid
  - Shipped
  - Completed
- Order date
- Total price
- Clickable to detail

---

---

### 6.6 Order Detail Page

- Order info
- Product list
- Price breakdown
- Shipping info
- Tracking (if available)
- Download link for digital product (if applicable)

---

### 6.7 Products Page (All Products / Catalog)

Purpose:
- Display **all available products**
- Act as main catalog / browsing page

Layout:
- **Left-side floating filter**
- **Right-side product grid**

#### Left Floating Filter (Sticky)

Filter must be:
- Sticky while scrolling
- Collapsible on mobile (drawer / bottom sheet)

Filter options include:
- **Search**
  - Keyword-based product search
- **Price Range**
  - Min – Max
  - Slider + manual input
- **Category**
  - Checkbox / list
- **Product Type**
  - Digital
  - Physical
- **Variant-related filters** (optional)
  - Size
  - Color
- **Availability**
  - In stock
  - Out of stock
- **Sort By**
  - Newest
  - Price: Low → High
  - Price: High → Low
  - Best Seller

Filter behavior:
- Reactive (no full page reload)
- Filter state reflected in URL (query params)

---

#### Product Grid Area

- Uses same **Product Card component** as Home Page
- Grid rules:
  - Desktop: 4 or 6 columns (consistent with homepage choice)
  - Tablet: 3 columns
  - Mobile: 2 columns
- Pagination or infinite scroll (choose one)

Each Product Card displays:
- Product image
- Product name (max 2 lines → `...`)
- Price
- Discount price (strikethrough original)
- Badge (Sale / New / Digital)

---

#### Empty State
- Friendly message when no product matches filter
- Clear filter CTA

---

#### Performance & UX Notes
- Debounced search input
- Skeleton loader while loading products
- Smooth transition when filters change

## 7. Footer (Suggestion)

Footer should include:

### Section 1
- Brand logo
- Short description

### Section 2
- Customer Service
  - Contact
  - FAQ
  - Returns
  - Shipping Info

### Section 3
- Company
  - About
  - Privacy Policy
  - Terms & Conditions

### Section 4
- Social Media icons
- Newsletter subscription

---

## 8. Technical Guidelines

- Component-based architecture
- Clean folder structure
- Reusable UI components
- Responsive-first
- State management ready for cart & auth
- No hardcoded business logic
- Assume API-driven data

## 9. Non-Goals

- No admin dashboard
- No payment gateway logic
- No backend logic

---

## 10. Output Expectation

- Well-structured FE architecture
- Reusable components
- Template-ready layout
- Clean, modern UI
- Production-grade frontend code

Build this as if it will be scaled into a **multi-template ecommerce SaaS**.