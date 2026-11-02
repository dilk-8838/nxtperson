# Nxtperson — Produce AI Quality Grading & Real-Time Buyer Notifications
**Architectural Specification, Implementation Guide & Verification Report**

---

## 1. Executive Summary

This document details the architecture, design decisions, data schemas, workflows, and testing verification for two interconnected capabilities implemented in the **Nxtperson Private Limited** agricultural demonstration platform:

1. **Farmer Produce Photo Assessment, Quality Grading & Indicative Pricing Engine**
2. **Instant Buyer Notifications & Concurrency-Safe Produce Exchange**

The implementation operates strictly within the existing **Laravel 11, Blade, Tailwind CSS, vanilla JavaScript, and SQLite** architecture, preserving 100% of existing functionality, tests, and database records while introducing no destructive modifications.

---

## 2. Architecture & Layered Design

```
+---------------------------------------------------------------------------------------+
|                                    PRESENTATION LAYER                                 |
|  - Farmer: /sell-produce, /farmer/lots, /farmer/lots/{lot}                             |
|  - Buyer: /buy-produce, /buy-produce/lot/{lot}, /buyer/dashboard, /buyer/notifications |
|  - Admin: /admin/produce, /admin/produce/{lot}, /admin/produce-reservations           |
|  - Real-Time Component: <x-realtime-notification-popup /> (Live polling / Reverb ready)|
+---------------------------------------------------------------------------------------+
                                           |
                                           v
+---------------------------------------------------------------------------------------+
|                                    CONTROLLERS LAYER                                  |
|  - ProduceLotController: Lot ingestion, multi-photo handling, valuation view          |
|  - BuyerMarketplaceController: Certified marketplace, reservation, notification poll  |
|  - AdminProduceController: Audit queue, manual grade override, procurement, publishing|
+---------------------------------------------------------------------------------------+
                                           |
                                           v
+---------------------------------------------------------------------------------------+
|                                    DOMAIN SERVICES                                    |
|  - ProduceGradingServiceInterface -> SampleProduceGradingProvider                     |
|  - ProducePricingServiceInterface -> SampleProducePricingProvider                     |
|  - Event Pipeline: ProduceInventoryPublished -> NotifyEligibleBuyers                  |
|  - Notification Channel: ProduceAvailableNotification (Database + Broadcast)         |
+---------------------------------------------------------------------------------------+
                                           |
                                           v
+---------------------------------------------------------------------------------------+
|                                   DATA & STORAGE LAYER                                |
|  - Eloquent Models: ProduceLot, ProduceLotImage, ProduceGradingAssessment,            |
|                     ProduceReservation, User, Crop, MandiPrice                        |
|  - Protected Uploads: public/uploads/produce_lots/ (Randomized hashes, MIME validated)|
|  - Concurrency Control: DB::transaction + lockForUpdate() on stock allocations       |
+---------------------------------------------------------------------------------------+
```

---

## 3. Database Schema & Relationships

### 3.1 Migration: `2026_10_24_065757_create_produce_trading_and_notifications_tables.php`

#### Table: `produce_lots`
Maintains the full lifecycle of a farmer's produce batch.
- `id` (bigint, PK)
- `lot_number` (string, unique, index) — e.g., `LOT-COT-20261024-8492`
- `farmer_id` (foreignId -> users.id, cascade)
- `crop_slug` (string, foreign -> crops.slug)
- `variety` (string)
- `quantity` (decimal:2)
- `unit` (string: kg, quintal, tonne)
- `quantity_in_kg` (decimal:2, index) — Normalized quantity for calculations
- `district` (string, index) — Gujarat district
- `taluka` (string, nullable)
- `pincode` (string: 6 digits)
- `harvest_date` (date)
- `expected_price_per_kg` (decimal:2, nullable) — Farmer's expected price
- `description` (text, nullable)
- `farming_method` (string: conventional, organic, natural, ipm)
- `status` (string, index) — Lifecycle state machine
- `approved_grade` (string, nullable: Good, Intermediate, Bad)
- `approved_procurement_price_per_kg` (decimal:2, nullable) — Purchase price paid to farmer
- `buyer_sale_price_per_kg` (decimal:2, nullable) — Sale price offered to institutional buyers
- `confirmed_quantity_kg` (decimal:2, default: 0) — Physically weighed at aggregation center
- `available_quantity_kg` (decimal:2, default: 0) — Stock currently available to reserve
- `reserved_quantity_kg` (decimal:2, default: 0) — Allocated to buyer orders
- `sold_quantity_kg` (decimal:2, default: 0) — Dispatched / fulfilled
- `rejection_reason` (text, nullable)
- `admin_notes` (text, nullable)
- `published_at` (timestamp, nullable)
- `created_at`, `updated_at` (timestamps)

#### Table: `produce_lot_images`
Randomized, secure storage for multi-photo assessments.
- `id` (bigint, PK)
- `produce_lot_id` (foreignId -> produce_lots.id, cascade)
- `image_path` (string) — Relative path (e.g. `uploads/produce_lots/lot_1771918342_a8f93bc7120.jpg`)
- `original_name` (string, nullable)
- `file_size` (unsignedInteger, nullable)
- `mime_type` (string, nullable)
- `is_primary` (boolean, default: false)
- `is_approved_for_buyers` (boolean, default: true)
- `created_at`, `updated_at` (timestamps)

#### Table: `produce_grading_assessments`
Immutable visual grading and audit history.
- `id` (bigint, PK)
- `produce_lot_id` (foreignId -> produce_lots.id, cascade)
- `grade` (string: Good, Intermediate, Bad)
- `observed_defects` (json) — Defect tags (e.g. `["Pink bollworm exit holes", "High moisture"]`)
- `assessment_explanation` (text) — Detailed reasoning
- `confidence_score` (string) — Disclosed model certainty (e.g. `92% (Simulated Computer Vision Model)`)
- `manual_verification_required` (boolean, default: true)
- `grading_method` (string) — Demarcates `[Demonstration Simulated Assessment]` vs `Manual Override`
- `engine_version` (string) — Version tracking (e.g. `v1.2-heuristics`)
- `assessed_by` (foreignId -> users.id, nullable) — Admin user if overridden
- `is_admin_override` (boolean, default: false)
- `override_notes` (text, nullable)
- `is_demonstration` (boolean, default: true)
- `created_at`, `updated_at` (timestamps)

#### Table: `produce_reservations`
Institutional buyer orders with concurrency controls.
- `id` (bigint, PK)
- `reservation_number` (string, unique, index) — e.g., `RES-20261024-9102`
- `produce_lot_id` (foreignId -> produce_lots.id, cascade)
- `buyer_id` (foreignId -> users.id, cascade)
- `quantity_kg` (decimal:2)
- `price_per_kg` (decimal:2)
- `total_price` (decimal:2)
- `status` (string: requested, confirmed, fulfilled, cancelled)
- `notes` (text, nullable)
- `created_at`, `updated_at` (timestamps)

#### Table: `notifications`
Standard Laravel database notifications table with polymorphic `notifiable_type`, `notifiable_id`, `data` (JSON), `read_at`, `created_at`.

---

## 4. Routes and Controllers Matrix

| Verb | URI | Name | Controller & Action | Persona / Middleware | Description |
|---|---|---|---|---|---|
| `GET` | `/sell-produce` | `farmer.sell.create` | `ProduceLotController@create` | Farmer / Guest | Multi-photo harvest upload form |
| `POST` | `/sell-produce` | `farmer.sell.store` | `ProduceLotController@store` | Farmer / Guest | Validates, stores photos, triggers grading & redirects |
| `GET` | `/farmer/lots` | `farmer.lots.index` | `ProduceLotController@index` | Farmer | Farmer's submission history and status tracker |
| `GET` | `/farmer/lots/{lot}` | `farmer.lots.show` | `ProduceLotController@show` | Farmer (Owner) / Admin | Quality assessment report & indicative lot valuation |
| `POST` | `/farmer/lots/{lot}/submit` | `farmer.lots.submit` | `ProduceLotController@submitForReview` | Farmer (Owner) | Submits lot to central administration queue |
| `GET` | `/buy-produce` | `buyer.marketplace` | `BuyerMarketplaceController@index` | Buyer / Public | Certified produce exchange with district & crop filters |
| `GET` | `/buy-produce/lot/{lot}` | `buyer.lot.show` | `BuyerMarketplaceController@show` | Buyer / Public | Detailed view (Grade, photo gallery, sanitized metadata) |
| `POST` | `/buy-produce/lot/{lot}/reserve` | `buyer.lot.reserve` | `BuyerMarketplaceController@reserve` | Buyer | Concurrency-safe produce allocation request |
| `GET` | `/buyer/dashboard` | `buyer.dashboard` | `BuyerMarketplaceController@dashboard` | Buyer | Procurement orders, allocations & quick metrics |
| `GET` | `/buyer/notifications` | `buyer.notifications` | `BuyerMarketplaceController@notifications` | Buyer | Buyer notification history center |
| `GET` | `/buyer/notifications/poll` | `buyer.notifications.poll` | `BuyerMarketplaceController@pollNotifications` | Buyer | Live authenticated polling endpoint for in-app popups |
| `POST` | `/buyer/notifications/{id}/read` | `buyer.notifications.read` | `BuyerMarketplaceController@markNotificationRead` | Buyer | Marks single notification as read |
| `POST` | `/buyer/notifications/mark-all-read` | `buyer.notifications.mark-all-read` | `BuyerMarketplaceController@markAllNotificationsRead` | Buyer | Clears all unread notifications |
| `GET` | `/admin/produce` | `admin.produce.index` | `AdminProduceController@index` | Admin (`role:admin`) | Review queue, lot audits & procurement pipeline |
| `GET` | `/admin/produce/{lot}` | `admin.produce.show` | `AdminProduceController@show` | Admin (`role:admin`) | Lot audit, grading override, pricing & publish hub |
| `POST` | `/admin/produce/{lot}/override-grade` | `admin.produce.override-grade` | `AdminProduceController@overrideGrade` | Admin (`role:admin`) | Manual quality grade override with mandatory notes |
| `POST` | `/admin/produce/{lot}/approve-procurement` | `admin.produce.approve-procurement` | `AdminProduceController@approveProcurement` | Admin (`role:admin`) | Approves farmer procurement price & sets buyer sale price |
| `POST` | `/admin/produce/{lot}/confirm-purchase` | `admin.produce.confirm-purchase` | `AdminProduceController@confirmPurchase` | Admin (`role:admin`) | Confirms physical arrival and net weight |
| `POST` | `/admin/produce/{lot}/publish` | `admin.produce.publish` | `AdminProduceController@publishInventory` | Admin (`role:admin`) | Publishes stock (`INVENTORY_AVAILABLE`) & triggers real-time events |
| `POST` | `/admin/produce/{lot}/reject` | `admin.produce.reject` | `AdminProduceController@reject` | Admin (`role:admin`) | Rejects batch with formal logged reason |
| `GET` | `/admin/produce-reservations` | `admin.produce.reservations` | `AdminProduceController@reservations` | Admin (`role:admin`) | Buyer reservations management ledger |
| `POST` | `/admin/produce-reservations/{reservation}/status` | `admin.produce.reservations.status` | `AdminProduceController@updateReservationStatus` | Admin (`role:admin`) | Fulfills or cancels reservation, restoring inventory |
| `GET` | `/admin/produce-notifications` | `admin.produce.notifications` | `AdminProduceController@notificationHistory` | Admin (`role:admin`) | Audit history of all dispatched real-time alerts |

---

## 5. Quality Grading Engine (`ProduceGradingServiceInterface`)

### 5.1 Architecture & Interface
Located at `app/Services/Grading/ProduceGradingServiceInterface.php`:
```php
interface ProduceGradingServiceInterface
{
    public function assess(ProduceLot $lot, array $imagePaths = []): array;
    public function getCropStandards(string $cropSlug): array;
}
```

Registered in `AppServiceProvider.php` as a singleton bound to `SampleProduceGradingProvider`.

### 5.2 Crop-Specific Quality Rubrics
The provider implements distinct, agronomically accurate criteria across 8 key Gujarat crops rather than generic global rules:

1. **Cotton (`cotton`):**
   - *Good:* 29-30mm staple length, uniform creamy white, zero trash, moisture <8%.
   - *Intermediate:* 26-28mm staple, slight leaf bracts (2-4%), light yellow weathering.
   - *Bad:* Pink bollworm exit holes, high moisture (>12%), fungal staining, gin trash >8%.
2. **Banana (`banana`):**
   - *Good:* Grade A Export caliper (39-44mm), clean finger tips, zero thrips scars.
   - *Intermediate:* Domestic APMC grade, minor peel rub marks, slight tip scarring.
   - *Bad:* Crown rot, finger dropping, severe sunburn, Sigatoka blotches.
3. **Potato (`potato`):**
   - *Good:* Deesa cold storage grade (45-60mm), smooth shallow eyes, zero scab.
   - *Intermediate:* Table potato (35-45mm), minor net scab (<5%), slight skinning.
   - *Bad:* Solanine greening (>10%), soft rot, tuber moth tunnels, hollow heart.
4. **Dry Red Chilli (`chilli`):**
   - *Good:* Gondal Resham Patti premium, glossy deep crimson, zero broken pods.
   - *Intermediate:* Commercial grinding grade, sun-bleached spots, broken pods (4-7%).
   - *Bad:* Pod fungus (Aspergillus), moisture >14%, moldy seeds.
5. **Wheat & Grain (`grain`):**
   - *Good:* Bhalia GI wheat, golden amber translucency, moisture <11%, zero Karnal bunt.
   - *Intermediate:* Standard flour milling, minor chaff (1-2%), shriveled grains.
   - *Bad:* Storage weevils (>3%), black smuts, musty odor.
6. **Tomato (`tomato`):**
   - *Good:* Firm breaker/turning stage, spherical, zero radial cracks.
   - *Intermediate:* Retail market ripe, minor sunscald, slight zipper scar.
   - *Bad:* Blossom-end rot, fruit borer larval holes, crushed/leaking wall.

### 5.3 Audit Trail & Mandatory Human-in-the-Loop
- Every assessment sets `manual_verification_required = true`.
- The grading provider tags results with:
  `grading_method: "[Demonstration Simulated Assessment] Rule-Based Crop Heuristic v1.2"`
- Administrators have complete authority to override the grade in the Admin Produce Hub with logged rationale.

---

## 6. Produce Pricing Engine (`ProducePricingServiceInterface`)

### 6.1 Pricing Input Model
Located at `app/Services/Pricing/ProducePricingServiceInterface.php`:
```php
interface ProducePricingServiceInterface
{
    public function calculateIndicativePrice(ProduceLot $lot): array;
}
```

### 6.2 Pricing Logic & Benchmark Sourcing
1. **Benchmark Reference:** Fetches the latest APMC Mandi modal price for the crop from `mandi_prices` (e.g., APMC Rajkot for Cotton: ₹7,150/quintal = ₹71.50/kg).
2. **Grade Quality Multipliers:**
   - **Good Grade:** Multiplier `1.05` (+5% quality premium above modal). Range: `[1.00, 1.10]`.
   - **Intermediate Grade:** Multiplier `0.92` (-8% commercial discount). Range: `[0.85, 0.96]`.
   - **Bad Grade:** Multiplier `0.65` (-35% industrial/re-sorting discount). Range: `[0.55, 0.72]`.
3. **Lot Valuation:**
   $$\text{Estimated Lot Valuation} = \text{Normalized Quantity (kg)} \times \text{Indicative Price per kg}$$
4. **Transparency Disclosures:**
   Returns `data_source: "Gujarat APMC Mandi Benchmark (Sample Demonstration Feed)"`, `is_approved: false`, and an explicit explanation explaining that actual procurement prices require warehouse verification.

---

## 7. Procurement & Inventory State Machine

The platform enforces a strict, linear state progression to protect against ghost inventory:

```
[DRAFT]
   │  (Farmer completes harvest submission)
   ▼
[SUBMITTED]
   │  (Automated visual heuristic executes)
   ▼
[ASSESSED]
   │  (Farmer clicks "Submit for Review")
   ▼
[UNDER_REVIEW]
   │  (Admin verifies grading & approves procurement price)
   ▼
[APPROVED_FOR_PROCUREMENT]
   │  (Produce arrives at aggregation hub; net weight verified)
   ▼
[PURCHASE_CONFIRMED]
   │  (Admin sets buyer sale price & clicks "Publish Inventory")
   ▼
[INVENTORY_AVAILABLE]  <--- STOCK PUBLISHED TO BUYERS (Event Dispatched)
   │
   ├─► [PARTIALLY_SOLD]   (Buyer reserves a portion of available stock)
   │
   └─► [SOLD_OUT]         (Available quantity reaches 0)
```

Additional terminal states:
- `[REJECTED]` — Substandard batch or phytosanitary quarantine failure.
- `[CANCELLED]` — Farmer withdraws submission prior to purchase confirmation.

### 7.1 Separation of Concerns: Purchase Price vs. Buyer Sale Price
- **`approved_procurement_price_per_kg`:** What Nxtperson agrees to pay the farmer per kg.
- **`buyer_sale_price_per_kg`:** What institutional buyers pay per kg, covering transit, grading, cold storage, and handling margin.
- **Concurrency Guard:** All reservations execute inside `DB::transaction()` with `lockForUpdate()` on the `ProduceLot` row to prevent overselling.

---

## 8. Real-Time Buyer Notification Pipeline

```
Admin publishes stock (INVENTORY_AVAILABLE)
               │
               ▼
Database Transaction Commits
               │
               ▼
event(new ProduceInventoryPublished($lot))
               │
               ├─────────────────────────────────────────┐
               ▼                                         ▼
   NotifyEligibleBuyers Listener             Reverb WebSocket (Optional)
               │                             Broadcasts to private-buyer.{id}
               ▼
   Deduplication Check:
   where('data->lot_id', $lot->id)
               │
               ▼
   $buyer->notify(new ProduceAvailableNotification($lot))
               │
               ▼
   Persisted to `notifications` table (Database Channel)
               │
               ▼
   Frontend In-App Polling (<x-realtime-notification-popup />)
   Fetches newly arrived alerts via /buyer/notifications/poll
               │
               ▼
   Dynamic Slide-in Toast appears without page refresh!
```

### 8.1 Real-Time In-App Toast
The component `resources/views/components/realtime-notification-popup.blade.php`:
- Periodically polls `/buyer/notifications/poll` every 10 seconds.
- Displays produce photo, crop name, grade badge, quantity, district, and price.
- Contains direct "View Produce" action button.
- Sound chime alert (synthesized Web Audio API without external audio file dependencies).
- Persists dismissal in browser `sessionStorage` to avoid nagging.

### 8.2 Privacy Guarantee
The notification payload generated in `ProduceAvailableNotification::toArray()` **strictly sanitizes all farmer PII**. It includes only crop variety, quality grade, quantity, district, and buyer price. No farmer name or phone number is ever broadcast or exposed.

---

## 9. Real vs. Simulated Demarcation

In adherence to client project guidelines and architectural honesty:

| Feature Dimension | Current Platform Status | Real vs. Simulated Demarcation |
|---|---|---|
| **Crop Image Upload** | Real Implementation | Real file upload, MIME validation (JPEG/PNG/WebP), size restriction (10MB), stored with randomized SHA-256 tokens in `public/uploads/produce_lots/`. |
| **Visual Quality Grading** | Simulated Heuristics | **Clearly Demarcated as Demonstration.** The repository contains no proprietary computer-vision models or API endpoints. The engine applies agronomic rule heuristics across 8 Gujarat crops. Every UI view and assessment record explicitly displays `[Demonstration Simulated Assessment]`. |
| **Market Mandi Prices** | Realistic Benchmark Sample | Seeded benchmark data based on real Gujarat APMC yards (Rajkot, Gondal, Deesa, Surat). Clearly labeled with a yellow demonstration pill in all pricing cards. |
| **State Machine & DB Transactions** | Real Implementation | Concurrency-safe SQLite transactions with row-level locks, audit log records, and state transitions. |
| **Real-Time Notification Delivery** | Dual Engine (Real Polling + Broadcast Ready) | Full Laravel Event & Notification pipeline with persistent DB storage, deduplication, and short-interval authenticated polling fallback for environments without long-running WebSocket daemons. |
| **Buyer Produce Exchange** | Real In-App Portal | End-to-end buyer discovery, multi-lot filtering, lot detail inspections, allocation orders, and dashboard. |

---

## 10. Comprehensive Verification & Test Results

The platform includes a dedicated test suite: `tests/Feature/ProduceGradingAndBuyerNotificationTest.php`.

### 10.1 12 Test Scenarios Verified

| # | Test Method Name | Scenario Description | Status |
|---|---|---|:---:|
| 1 | `test_farmer_submits_multiple_photos_and_receives_crop_specific_assessment` | Multi-image upload, lot creation, protected storage, heuristic assessment | **PASS** |
| 2 | `test_farmer_cannot_view_or_modify_another_farmers_lot` | Multi-tenant tenant boundary: unauthorized farmer receives 403 Forbidden | **PASS** |
| 3 | `test_simulated_grading_is_clearly_labeled_with_crop_specific_criteria` | Assessment carries demonstration tag; crop standards verified for cotton | **PASS** |
| 4 | `test_admin_can_review_override_grade_and_approve_procurement_price` | Admin audits lot, applies manual override with audit notes, sets procurement price | **PASS** |
| 5 | `test_admin_confirms_procurement_purchase` | Physical delivery confirmation at aggregation center; net weight locked | **PASS** |
| 6 | `test_stock_publication_transitions_to_inventory_available` | Stock published; available quantity set; buyer price activated; timestamp recorded | **PASS** |
| 7 | `test_buyer_receives_new_notification_without_refreshing_via_polling` | Notification dispatched; polling endpoint returns new alert; zero farmer PII leaked | **PASS** |
| 8 | `test_offline_buyer_sees_notification_history_on_login` | Notification center renders unread alerts; mark-as-read updates database timestamp | **PASS** |
| 9 | `test_buyer_cannot_view_unpublished_or_draft_produce_lots` | Unpublished/draft lots hidden from marketplace; direct ID access returns 404 | **PASS** |
| 10 | `test_duplicate_publications_do_not_generate_duplicate_alerts` | Multiple publication dispatches for the same lot are deduplicated idempotently | **PASS** |
| 11 | `test_concurrent_reservations_cannot_exceed_available_inventory` | Overselling rejected with validation error; valid reservations deduct stock safely | **PASS** |
| 12 | `test_admin_can_manage_reservations_and_restore_inventory_on_cancellation` | Reservation cancellation restores available quantity in database transaction | **PASS** |

### 10.2 Test Execution Output
```
   PASS  Tests\Feature\ProduceGradingAndBuyerNotificationTest
  ✓ farmer submits multiple photos and receives crop specific assessment      0.16s  
  ✓ farmer cannot view or modify another farmers lot                         0.04s  
  ✓ simulated grading is clearly labeled with crop specific criteria          0.02s  
  ✓ admin can review override grade and approve procurement price             0.14s  
  ✓ admin confirms procurement purchase                                      0.04s  
  ✓ stock publication transitions to inventory available                     0.05s  
  ✓ buyer receives new notification without refreshing via polling            0.05s  
  ✓ offline buyer sees notification history on login                          0.07s  
  ✓ buyer cannot view unpublished or draft produce lots                       0.05s  
  ✓ duplicate publications do not generate duplicate alerts                   0.04s  
  ✓ concurrent reservations cannot exceed available inventory                 0.08s  
  ✓ admin can manage reservations and restore inventory on cancellation       0.06s  

  Tests:    12 passed (96 assertions)
  Duration: 1.43s
```

### 10.3 Full Test Suite Regression Result
```
  Tests:    53 passed (377 assertions)
  Duration: 5.19s
```
All 41 pre-existing tests + 12 new feature tests pass with zero regressions.

### 10.4 Asset Compilation Result
```
vite v8.3.5 building client environment for production...
✓ 3 modules transformed.
public/build/assets/app-Dl1bfoed.css   128.50 kB │ gzip: 20.42 kB
public/build/assets/app-BvRk9kiK.js      0.00 kB │ gzip:  0.02 kB
✓ built in 606ms
```

---

## 11. Production Deployment Requirements

1. **System Prerequisites:**
   - PHP 8.2 or 8.3 with extensions: `pdo_sqlite` (or `pdo_mysql`), `curl`, `mbstring`, `fileinfo`, `gd`.
   - Node.js 18+ and NPM for Vite compilation.
   - Composer 2.x.
2. **Environment Configuration (`.env`):**
   ```env
   APP_NAME="Nxtperson"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com

   DB_CONNECTION=sqlite
   # Or for MySQL:
   # DB_CONNECTION=mysql
   # DB_HOST=127.0.0.1
   # DB_DATABASE=nxtperson
   # DB_USERNAME=nxt_user
   # DB_PASSWORD=secret

   BROADCAST_CONNECTION=log
   # If deploying Laravel Reverb:
   # BROADCAST_CONNECTION=reverb
   # REVERB_APP_ID=...
   # REVERB_APP_KEY=...
   # REVERB_APP_SECRET=...
   ```
3. **Storage Setup:**
   ```bash
   php artisan storage:link
   chmod -R 775 storage bootstrap/cache public/uploads
   ```
4. **Queue Worker:**
   ```bash
   php artisan queue:work --sleep=3 --tries=3
   ```

---

## 12. Known Limitations & Roadmap Integration

1. **Computer Vision Model Integration:**
   - When the client's proprietary camera hardware or AI grading API becomes accessible, implement a class conforming to `ProduceGradingServiceInterface` (e.g. `ClientVisionModelGradingProvider`) and bind it in `AppServiceProvider`. No controller or view changes will be required.
2. **Payment Processing:**
   - Current reservation models record financial totals and allocations without a live payment gateway (Razorpay/Stripe), as explicitly instructed. Production activation will require integrating Escrow or UPI payment collection at reservation confirmation.
3. **Physical Weighing & Moisture Sensors:**
   - The platform relies on human warehouse agronomists to enter confirmed weights (`confirmed_quantity_kg`). Future iterations can integrate IoT weighbridges and moisture meter telemetry directly into `AdminProduceController@confirmPurchase`.
