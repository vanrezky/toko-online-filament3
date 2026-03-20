# Product Requirements Document (PRD)

## Public Voucher System

**Version:** 1.0  
**Status:** Implemented  
**Last Updated:** March 2026  
**Platform:** Vue 3 + Inertia.js + Laravel  
**Path Implement:**

- Backend: `app/`
- Frontend: `resources/js/frontend/`
- Database: `database/migrations/`

---

## 1. Executive Summary

Public Voucher System memungkinkan admin untuk membuat dan mengelola voucher yang dapat digunakan oleh semua customer. Sistem mendukung multiple voucher application (1 shipping voucher + 1 product voucher) dengan berbagai jenis diskon dan validasi otomatis.

**Key Features:**

- Public vouchers dapat dilihat dan digunakan customer
- Multiple voucher stacking (1 shipping + 1 product)
- Auto-apply via URL parameter
- Card-based UI dengan background image opsional
- Real-time validation dan feedback

---

## 2. Goals & Objectives

### Primary Goals

1. **Meningkatkan Conversion** - Voucher sebagai insentif belanja
2. **User Engagement** - Tampilan menarik untuk menarik perhatian user
3. **Fleksibilitas** - Support berbagai jenis voucher (ongkir & produk)
4. **Transparency** - User bisa lihat semua voucher yang tersedia

### Success Metrics

- Voucher usage rate: > 30% dari total orders
- Average order increase: > 15% dengan voucher
- Cart abandonment reduction: > 10%
- Time to apply voucher: < 5 seconds

---

## 3. Requirements Summary

### 3.1 Voucher Types

| Type            | Description   | Stacking   |
| --------------- | ------------- | ---------- |
| `SHIPPING_COST` | Diskon ongkir | 1 per cart |
| `PRODUCT`       | Diskon produk | 1 per cart |

### 3.2 Discount Types

| Type         | Description                 |
| ------------ | --------------------------- |
| `FIXED`      | Diskon nominal tetap (Rp X) |
| `PERCENTAGE` | Diskon persentase (% X)     |

### 3.3 Stacking Rules

```
✅ ALLOWED:
- [SHIPPING_VOUCHER] + [PRODUCT_VOUCHER]

❌ FORBIDDEN:
- [SHIPPING_VOUCHER] + [SHIPPING_VOUCHER]
- [PRODUCT_VOUCHER] + [PRODUCT_VOUCHER]
- [SHIPPING_VOUCHER] + [PRODUCT_VOUCHER] + [ANY]
```

### 3.4 Auto-Apply Rules

- Click voucher dari list → auto-apply → redirect ke cart
- URL parameter: `/vouchers?apply=CODE`
- Validation dilakukan sebelum apply
- Success toast ditampilkan

---

## 4. Voucher Display Locations

### 4.1 Location A: Voucher Page (`/vouchers`)

**Purpose:** Browse semua public vouchers

**Features:**

- List semua voucher public
- Filter: Semua, Ongkir, Produk
- Sort by: Newest (default)
- Badge "Expiring Soon" jika < 3 hari
- Copy code button
- Gunakan voucher button → auto-apply

### 4.2 Location B: Homepage Section

**Purpose:** Highlight voucher menarik di homepage

**Features:**

- Section "Voucher Tersedia" atau "Promo Spesial"
- Tampilkan 3-5 voucher teratas
- Card-based display
- Link ke halaman `/vouchers`

### 4.3 Location C: Floating Banner/Toast

**Purpose:** Notification saat ada voucher yang applicable

**Features:**

- Toast notification saat voucher berhasil di-apply
- Badge counter di header (jika ada voucher aktif)
- Slide-in notification

### 4.4 Location D: Cart & Checkout Integration

**Purpose:** Display dan manage applied vouchers

**Features:**

- Show applied vouchers di summary
- Input kode voucher
- Remove voucher button
- Update totals dengan diskon
- Clear indication of savings

---

## 5. Database Schema

### 5.1 New Columns - `vouchers` table

```php
// Add to existing vouchers table
$table->unsignedInteger('usage_count')->default(0)->after('max_user_used');
// Tracks how many times voucher has been used
```

### 5.2 New Columns - `carts` table

```php
// Add to existing carts table
$table->foreignId('shipping_voucher_id')->nullable()->constrained('vouchers')->nullOnDelete();
$table->foreignId('product_voucher_id')->nullable()->constrained('vouchers')->nullOnDelete();
$table->decimal('shipping_discount', 12, 2)->default(0);
$table->decimal('product_discount', 12, 2)->default(0);
```

### 5.3 Existing Fields - `vouchers` table

| Field           | Type     | Description                                    |
| --------------- | -------- | ---------------------------------------------- |
| `name`          | string   | Nama voucher                                   |
| `description`   | text     | Deskripsi promo                                |
| `voucher_type`  | enum     | SHIPPING_COST, PRODUCT                         |
| `discount_type` | enum     | FIXED, PERCENTAGE                              |
| `product_type`  | enum     | ALL_PRODUCT, PHYSICAL_PRODUCT, DIGITAL_PRODUCT |
| `code`          | string   | Kode voucher unik                              |
| `discount`      | decimal  | Nilai diskon                                   |
| `discount_min`  | decimal  | Minimum belanja                                |
| `discount_max`  | decimal  | Maximum diskon (untuk percentage)              |
| `max_user_used` | integer  | Max penggunaan per user                        |
| `usage_count`   | integer  | Total penggunaan                               |
| `is_active`     | boolean  | Status aktif                                   |
| `is_public`     | boolean  | Bisa dilihat customer                          |
| `start_at`      | datetime | Tanggal mulai                                  |
| `end_at`        | datetime | Tanggal berakhir                               |
| `category_id`   | foreign  | Kategori produk (optional)                     |

---

## 6. Backend Implementation

### 6.1 Voucher Model - Scopes & Accessors

```php
// app/Models/Voucher.php

// Scopes
active()           // is_active = true
public()          // is_public = true
valid()           // Dalam periode valid (start_at <= now <= end_at)
notExpired()      // end_at > now
shippingOnly()    // voucher_type = SHIPPING_COST
productOnly()     // voucher_type = PRODUCT

// Accessors
isExpiringSoon()      // Returns true if < 72 hours remaining
remainingUses()       // max_user_used - user usage count
isUsableBy(User $user) // Check all conditions
formattedDiscount()   // "Rp 15.000" or "20%"
```

### 6.2 VoucherService

```php
// app/Services/VoucherService.php

class VoucherService
{
    public function getPublicVouchers(): Collection;

    public function validateVoucher(string $code, Cart $cart, ?User $user): ValidationResult;

    public function applyShippingVoucher(Cart $cart, Voucher $voucher): Cart;

    public function applyProductVoucher(Cart $cart, Voucher $voucher): Cart;

    public function removeVoucher(Cart $cart, string $type): Cart;

    public function calculateDiscount(Voucher $voucher, Cart $cart): DiscountResult;

    public function trackUsage(Voucher $voucher, User $user): void;
}
```

### 6.3 API Endpoints

| Endpoint                        | Method | Description                     |
| ------------------------------- | ------ | ------------------------------- |
| `/api/vouchers`                 | GET    | List public vouchers            |
| `/api/vouchers/{code}/validate` | GET    | Validate voucher code           |
| `/api/vouchers/apply`           | POST   | Apply voucher to cart           |
| `/api/vouchers/remove`          | POST   | Remove voucher from cart        |
| `/api/vouchers/available`       | GET    | Get vouchers available for cart |

### 6.4 Frontend Controller

| Endpoint               | Method | Description             |
| ---------------------- | ------ | ----------------------- |
| `/vouchers`            | GET    | Voucher list page       |
| `/vouchers?apply=CODE` | GET    | Auto-apply and redirect |

---

## 7. Frontend Components

### 7.1 VoucherCard.vue

**Props:**

```typescript
interface VoucherCardProps {
    voucher: Voucher;
    variant: "default" | "compact" | "horizontal";
    showApplyButton?: boolean;
    showCopyButton?: boolean;
}
```

**States:**

- Default (tanpa image)
- With Background Image
- Applied (sedang digunakan)
- Expired
- Expiring Soon (< 3 hari)

**Design:**

```
┌──────────────────────────────────────────┐
│ [Background Image - jika ada]             │
│                                          │
│ ┌──────────────────────────────────────┐ │
│ │ 🚚 FREE ONGKIR                      │ │
│ │                                      │ │
│ │ Diskon: Rp 15.000                   │ │
│ │ Min. Belanja: Rp 100.000           │ │
│ │                                      │ │
│ │ ┌────────────────┐ ┌─────────────┐  │ │
│ │ │ MAKANENAKFREE  │ │ 📋 Copy    │  │ │
│ │ └────────────────┘ └─────────────┘  │ │
│ │                                      │ │
│ │ ⏰ Berakhir: 25 Mar 2026            │ │
│ │ 👤 150x digunakan                   │ │
│ │                                      │ │
│ │ [ 🛒 Gunakan Voucher ]              │ │
│ └──────────────────────────────────────┘ │
└──────────────────────────────────────────┘
```

### 7.2 AppliedVouchers.vue

**Location:** Cart & Checkout summary section

**Features:**

- Display applied vouchers (shipping + product)
- Show discount amount
- Remove button per voucher
- Total savings display

```
┌──────────────────────────────────────────┐
│ 💰 Voucher Saya                         │
├──────────────────────────────────────────┤
│ 🚚 Ongkir:    -Rp 15.000   [✕ Hapus]   │
│ 🛒 Produk:   -Rp 25.000   [✕ Hapus]   │
├──────────────────────────────────────────┤
│ 💸 Total Diskon:      -Rp 40.000        │
└──────────────────────────────────────────┘
```

### 7.3 VoucherToast.vue

**Trigger:** Setelah voucher berhasil di-apply

**Features:**

- Success icon + message
- Voucher code + discount amount
- Link ke cart
- Auto-dismiss after 5 seconds

```
┌──────────────────────────────────────────┐
│ ✅ Voucher berhasil digunakan!            │
│    MAKANENAK20 - Hemat Rp 25.000        │
│    [ Lihat Keranjang ]                   │
└──────────────────────────────────────────┘
```

### 7.4 VoucherSection.vue

**Location:** Homepage

**Features:**

- Title: "Voucher Tersedia" atau dari template
- Display 3-5 vouchers
- Horizontal scroll on mobile
- Link "Lihat Semua" ke `/vouchers`

### 7.5 VoucherList.vue

**Location:** `/vouchers` page

**Features:**

- Filter tabs: Semua | Ongkir | Produk
- Sort: Newest (default)
- Badge expiring soon
- Pagination atau infinite scroll
- Empty state

---

## 8. User Flows

### 8.1 Browse & Apply Voucher Flow

```
1. User membuka /vouchers
2. Melihat list voucher public
3. Klik "Gunakan Voucher" pada voucher tertentu
4. Sistem validate voucher
   ├── ✅ Valid → Apply ke cart → Redirect ke /cart
   └── ❌ Invalid → Show error toast
5. Cart page menampilkan voucher applied
6. Total di-update dengan diskon
```

### 8.2 Auto-Apply dari URL Flow

```
1. User klik link: /vouchers?apply=MAKANENAK20
2. Sistem validate voucher
   ├── ✅ Valid & user logged in
   │   └── Apply ke cart → Redirect ke /cart
   ├── ✅ Valid & user not logged in
   │   └── Save voucher code → Redirect ke /login?redirect=/cart&voucher=CODE
   └── ❌ Invalid
       └── Redirect ke /vouchers dengan error
```

### 8.3 Manual Input Voucher Flow

```
1. User di halaman Cart
2. Masukkan kode voucher di input
3. Klik "Pakai" atau tekan Enter
4. Sistem validate
   ├── ✅ Valid → Apply + success toast
   └── ❌ Invalid → Show error message
```

### 8.4 Remove Voucher Flow

```
1. User di halaman Cart
2. Klik tombol ✕ pada voucher
3. Voucher di-remove dari cart
4. Total di-update
5. Success toast "Voucher dihapus"
```

---

## 9. Validation Rules

### 9.1 Apply Shipping Voucher

| Condition               | Error Message                          |
| ----------------------- | -------------------------------------- |
| Voucher not found       | "Kode voucher tidak valid"             |
| Voucher inactive        | "Voucher sudah tidak aktif"            |
| Voucher not public      | "Voucher tidak tersedia"               |
| Voucher expired         | "Voucher sudah berakhir"               |
| User already used max   | "Anda sudah mencapai batas penggunaan" |
| Shipping voucher exists | "Hanya bisa gunakan 1 voucher ongkir"  |

### 9.2 Apply Product Voucher

| Condition              | Error Message                                    |
| ---------------------- | ------------------------------------------------ |
| Voucher not found      | "Kode voucher tidak valid"                       |
| Voucher inactive       | "Voucher sudah tidak aktif"                      |
| Voucher not public     | "Voucher tidak tersedia"                         |
| Voucher expired        | "Voucher sudah berakhir"                         |
| Min purchase not met   | "Minimal belanja Rp X untuk gunakan voucher ini" |
| User already used max  | "Anda sudah mencapai batas penggunaan"           |
| Product voucher exists | "Hanya bisa gunakan 1 voucher produk"            |

---

## 10. Technical Implementation Files

### 10.1 Database Migrations

```
📁 database/migrations/
├── 2026_03_20_000001_add_usage_count_to_vouchers_table.php
└── 2026_03_20_000002_add_voucher_columns_to_carts_table.php
```

### 10.2 Models

```
📁 app/Models/
├── Voucher.php (update - add scopes & accessors)
└── Cart.php (update - add voucher relations)
```

### 10.3 Services

```
📁 app/Services/
└── VoucherService.php (new)
```

### 10.4 Controllers

```
📁 app/Http/Controllers/
├── Api/
│   └── VoucherController.php (new)
└── Frontend/
    └── VoucherController.php (new)
```

### 10.5 Frontend Services

```
📁 resources/js/frontend/
├── services/
│   └── voucherService.js (new)
├── composables/
│   └── useVoucher.js (new)
└── components/UI/
    ├── VoucherCard.vue (new)
    ├── VoucherToast.vue (new)
    └── VoucherSection.vue (new)
```

### 10.6 Frontend Pages

```
📁 resources/js/frontend/pages/
└── Voucher/
    └── Index.vue (new)
```

### 10.7 Update Existing Pages

```
📁 resources/js/frontend/pages/
├── Cart/
│   └── Index.vue (update - add voucher integration)
└── Checkout/
    └── Index.vue (update - add voucher integration)
```

---

## 11. UI/UX Specifications

### 11.1 Color Scheme

| Element                | Color          | Usage                   |
| ---------------------- | -------------- | ----------------------- |
| Shipping Voucher Badge | Blue/Info      | Shipping type indicator |
| Product Voucher Badge  | Green/Success  | Product type indicator  |
| Expiring Soon Badge    | Yellow/Warning | < 3 days remaining      |
| Discount Text          | Primary        | Price/discount values   |
| Copy Button            | Secondary      | Background              |

### 11.2 Typography

| Element        | Font              | Size | Weight  |
| -------------- | ----------------- | ---- | ------- |
| Voucher Name   | Plus Jakarta Sans | 16px | Bold    |
| Discount Value | Plus Jakarta Sans | 20px | Bold    |
| Description    | Plus Jakarta Sans | 14px | Regular |
| Meta Info      | Plus Jakarta Sans | 12px | Regular |

### 11.3 Spacing & Layout

| Element       | Spacing           |
| ------------- | ----------------- |
| Card Padding  | 16px              |
| Card Gap      | 12px              |
| Section Gap   | 24px              |
| Border Radius | 12px (rounded-xl) |

### 11.4 Background Image Handling

```css
/* With Image */
.voucher-card {
    background-image: url("image-url");
    background-size: cover;
    background-position: center;
}

/* Overlay */
.voucher-card::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.7));
}

/* Without Image - Card Style */
.voucher-card {
    background: white;
    border: 1px solid var(--color-border);
}
```

---

## 12. Caching Strategy

| Data                    | Cache Duration | Cache Key         |
| ----------------------- | -------------- | ----------------- |
| Public Vouchers         | 1 hour         | `public_vouchers` |
| User's Applied Vouchers | No cache       | -                 |
| Voucher Validation      | No cache       | -                 |

---

## 13. Error Handling

### 13.1 API Error Responses

```json
{
    "success": false,
    "error": {
        "code": "VOUCHER_EXPIRED",
        "message": "Voucher sudah berakhir"
    }
}
```

### 13.2 Error Codes

| Code                           | HTTP Status | Description                      |
| ------------------------------ | ----------- | -------------------------------- |
| `VOUCHER_NOT_FOUND`            | 404         | Kode voucher tidak valid         |
| `VOUCHER_INACTIVE`             | 400         | Voucher tidak aktif              |
| `VOUCHER_EXPIRED`              | 400         | Voucher sudah berakhir           |
| `VOUCHER_NOT_STARTED`          | 400         | Voucher belum dimulai            |
| `VOUCHER_NOT_PUBLIC`           | 403         | Voucher tidak public             |
| `VOUCHER_MAX_USAGE`            | 400         | Batas penggunaan tercapai        |
| `VOUCHER_MIN_PURCHASE`         | 400         | Minimum belanja tidak terpenuhi  |
| `VOUCHER_TYPE_EXISTS`          | 400         | Sudah ada voucher type yang sama |
| `VOUCHER_NO_ELIGIBLE_PRODUCTS` | 400         | Tidak ada produk eligible        |

---

## 14. Testing Scenarios

### 14.1 Unit Tests

- [ ] Voucher scopes (active, public, valid)
- [ ] Discount calculation (fixed & percentage)
- [ ] Validation rules
- [ ] Usage tracking

### 14.2 Integration Tests

- [ ] Apply voucher to cart
- [ ] Remove voucher from cart
- [ ] Multiple voucher stacking
- [ ] Auto-apply from URL
- [ ] Max usage limit

### 14.3 UI Tests

- [ ] Copy to clipboard
- [ ] Apply button click
- [ ] Filter tabs
- [ ] Toast notifications
- [ ] Responsive mobile

---

## 15. Acceptance Criteria

### 15.1 Backend

- [ ] Voucher model has all required scopes
- [ ] VoucherService handles all validation logic
- [ ] API endpoints return correct responses
- [ ] Database migrations run without errors
- [ ] Usage tracking works correctly

### 15.2 Frontend - Voucher Page

- [ ] List all public vouchers
- [ ] Filter by type (shipping/product)
- [ ] Show expiring soon badge
- [ ] Copy code works with feedback
- [ ] Apply button redirects to cart
- [ ] Auto-apply from URL works

### 15.3 Frontend - Cart Integration

- [ ] Show applied vouchers in summary
- [ ] Display discount amounts
- [ ] Remove voucher functionality
- [ ] Update totals correctly
- [ ] Error messages display properly
- [ ] Input kode voucher works

### 15.4 Frontend - Homepage

- [ ] Voucher section displays
- [ ] Shows top vouchers
- [ ] Links to voucher page
- [ ] Responsive mobile

### 15.5 UX

- [ ] Copy feedback (toast/animation)
- [ ] Apply success notification
- [ ] Error states are clear
- [ ] Loading states during API calls
- [ ] Empty state for no vouchers

---

## 16. Timeline & Effort

| Phase   | Tasks                 | Estimated Time |
| ------- | --------------------- | -------------- |
| Phase 1 | Database & Models     | 30 min         |
| Phase 2 | VoucherService & API  | 45 min         |
| Phase 3 | Frontend Services     | 30 min         |
| Phase 4 | VoucherCard Component | 30 min         |
| Phase 5 | Cart Integration      | 30 min         |
| Phase 6 | Checkout Integration  | 30 min         |
| Phase 7 | Voucher Page          | 30 min         |
| Phase 8 | Homepage Section      | 15 min         |
| Phase 9 | Toast & Polish        | 30 min         |

**Total Estimated: ~4.5 hours**

---

## 17. Revision History

| Version | Date     | Author   | Changes                 |
| ------- | -------- | -------- | ----------------------- |
| 1.0     | Mar 2026 | Dev Team | Initial PRD creation    |
| 1.1     | Mar 2026 | Dev Team | Implementation complete |

---

**Document End**

_This PRD is a living document and subject to change based on user feedback and market conditions._
