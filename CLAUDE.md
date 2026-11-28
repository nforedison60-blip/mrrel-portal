# MrRel portal: project notes for Claude Code

MrRel is a South African moving and rentals marketplace. This repo is the **portal** (portal.mrrel.co.za).
The marketing website (www.mrrel.co.za) is a separate WordPress/Elementor site and is NOT in this repo.

## Stack and hosting
- Laravel 13, PHP 8.3, Blade views (no SPA). Tests: Pest on sqlite. Run `php artisan test`.
- Hosted on **Laravel Cloud**. Artisan commands in production are run from the Laravel Cloud "Commands" tab.
  Run `php artisan migrate --force` there after deploying a migration.
- Files (photos, documents) are on the `s3` disk. Provider photos are served through the app (`provider.photo` route).
- The owner is not a developer and works in a GitHub Codespace. Explain things in plain language.

## Owner preferences (always follow)
- Terminal commands go in their **own separate code block**, as **one line**, chained with `&&`.
- Keep replies short and plain. Say what changed and what to check.
- Brand: navy `#003068`, orange `#F86000`, fonts Montserrat (headings) and Open Sans (text). No teal or green accents
  (a small green "online" dot is fine).

## Roles
Customer (tenant or mover customer), Provider (mover), Landlord, Admin. `EnsureRole` middleware sends people who open
another role's page back to their own area; non-GET requests get 403. Registration offers Customer, Provider, Landlord.
Admin accounts are made with `php artisan app:make-admin email@example.com` (the person registers and verifies first).
Admins: makamumihlovo@gmail.com, nforedison60@gmail.com, mavutaninc@gmail.com (the last once registered).

## Money (do not change without asking)
- Tenants: once-off **R99** matching fee to unlock matched homes; optional viewing attendance fee
  (R150 first 10 km round trip + R7.50 per extra km, only shown when the base location is set).
- Landlords: **R250** once-off introduction fee per tenant, released only after payment.
- Movers: MrRel keeps **10%** of each paid job (`services.mrrel.commission_rate`). No monthly fee while launching.
- Customers pay MrRel; MrRel pays the mover after the customer confirms the job. Admin "Mark paid out" only records it.
- Public wording: do not push fees at the front of the website or the first screen of a page.

## Payments
- **Ozow instant EFT** (`PaymentController`, `Tenant/TenantPaymentController`, `Landlord/LandlordIntroController`,
  `InvoiceController`). Env: `OZOW_SITE_CODE`, `OZOW_PRIVATE_KEY`, `OZOW_IS_TEST`. Return URLs are built with `url()`.
  **Only the signed notify message marks something Paid**, never the customer returning to the site.
- **Direct bank transfer** (`BankTransfer` support class, `BankTransferController`, `routes/bank.php`): the person pays into
  MrRel's FNB account using a reference, then emails proof of payment to relocations@mrrel.co.za. An admin confirms under
  Admin > Finance > Bank payments. References: `BKG-0002` (booking), `TPY-0007` (tenant fee), `LIN-0007` (landlord intro).
  Bank details come from `config/mrrel.php` (env `MRREL_BANK_*`, `MRREL_POP_EMAIL`).
- Paystack was discussed as a possible replacement for Ozow but is not built.

## Things that bit us before
- **Eloquent events:** `Model::updated()` registered too early silently does nothing. Notifications use
  `Event::listen('eloquent.updated: '.Model::class, ...)` and a once-only guard `app()->bound('mrrel.notifications')`.
- **Tests and verification:** email verification is required (`MustVerifyEmail`). In tests use `User::factory()->create()`
  (verified by default), never `User::create()` (unverified, so pages redirect).
- **Verification links** are signed and must work logged out and over https (`URL::forceScheme('https')`, trusted proxies).
- Route order matters: the last matching route wins. Several route files are required at the bottom of `routes/web.php`.
- Customer/Provider avatars use the `<x-provider-avatar>` component; photos load via `route('provider.photo', ...)`.
- Mobile bottom bar icons are picked by label text in `public/js/mrrel-ui.js` (`iconFor`). Add an icon there when you add a tab.

## Look and feel
- Layouts in `resources/views/layouts` (`app` = customer, `landlord`, `provider`, `admin`, `guest`).
- Brand CSS in `public/css/mrrel-brand.css`, UI polish in `public/css/mrrel-ui.css`.
- "How it works" step cards use `<x-process-card>` (tenant steps, landlord steps).
- Naming: "Find a home" = tell MrRel what you need; "Browse homes" = search listings. They are different things.

## Emails
- `App\Support\Notifications` sends the "something happened" emails through `App\Mail\Notice`. A failed email is logged
  and never breaks a page or a payment. Admins are emailed when a new account is created.
- Real `MAIL_*` settings must be set in Laravel Cloud for any of this to arrive.

## Admin tools
- Backup and export: Admin > Support & settings > Backup (CSV per list or a ZIP). Never includes passwords or bank numbers.
- `php artisan app:launch-clean --keep=a@x --keep=b@x [--files] [--confirm]` wipes test data and all users except the kept
  admins. Dry run first (no `--confirm`). Back up the database before `--confirm`. Cannot be undone.

## Launch checklist (status at time of writing)
1. Real `MAIL_*` settings in Laravel Cloud, send a test email.
2. Test R99 and R250 payments (Ozow test or bank transfer) BEFORE the clean-up.
3. Switch Ozow to live: live site code and private key, `OZOW_IS_TEST=false`.
4. First deposit into the new FNB account (the bank letter says it is not yet activated).
5. Run `app:launch-clean` (dry run, backup, then `--confirm --files`), keeping every admin email.
6. Register and verify mavutaninc@gmail.com, then `php artisan app:make-admin mavutaninc@gmail.com`.
7. Contact details: WhatsApp 079 361 6334, emails support@mrrel.co.za and info@mrrel.co.za. `config/mrrel.php`
   `business.email` / `business.whatsapp` and the admin notice address may still hold the old makamu@mrrel.co.za and
   075 354 3550. Ask the owner before changing.

## Not in this repo
The website pages (Home, Book a Move, Find a Rental, For Providers, About, Contact, footer) are HTML pasted into Elementor
widgets on WordPress. Their code is not here. Ask the owner to paste a page before editing it.
