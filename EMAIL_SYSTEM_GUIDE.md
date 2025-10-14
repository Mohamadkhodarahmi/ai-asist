# 📧 Transactional Email System Guide

## Overview

This guide covers the complete transactional email system implemented for your SaaS application. All emails are professionally designed, mobile-responsive, and automatically sent at key points in the user journey.

---

## 📬 **Implemented Emails**

### 1. **Welcome Email** (`WelcomeNotification`)
**Trigger**: Sent immediately after user registration  
**Template**: `resources/views/emails/welcome/user-welcome.blade.php`  
**Contains**:
- Welcome message with user's name
- Account details (email, plan, join date)
- Quick start guide (4 steps)
- Call-to-action to start first chat
- Upgrade prompt for free users

**When Sent**:
```php
// In RegisteredUserController after user creation
$user->notify(new WelcomeNotification());
```

---

### 2. **Email Verification** (Built-in Laravel)
**Trigger**: Sent when user registers (via `Registered` event)  
**Template**: `resources/views/emails/auth/verify-email.blade.php`  
**Contains**:
- Verification link (expires in 60 minutes)
- Security notice
- Manual link copy option

**Enabled**: User model now implements `MustVerifyEmail`

**To customize the verification email**, create a custom notification:
```bash
php artisan make:notification VerifyEmailNotification
```

---

### 3. **Payment Receipt** (`PaymentReceivedNotification`)
**Trigger**: Sent when payment is confirmed  
**Template**: `resources/views/emails/subscription/payment-received.blade.php`  
**Contains**:
- Payment confirmation
- Order details (ID, amount, plan, date)
- Transaction ID
- Visual stats (amount paid, plan name)
- What's next section
- Dashboard link

**When Sent**:
```php
// In NOWPaymentsWebhookController when payment confirmed
$order->user->notify(new PaymentReceivedNotification($order));
```

---

### 4. **Subscription Changed** (`SubscriptionChangedNotification`)
**Trigger**: Sent when user upgrades/downgrades plan  
**Template**: `resources/views/emails/subscription/plan-changed.blade.php`  
**Contains**:
- Subscription change confirmation
- Old vs New plan comparison
- List of new features (for upgrades)
- Call-to-action to explore features

**When Sent**:
```php
// In NOWPaymentsWebhookController after plan change
$user->notify(new SubscriptionChangedNotification(
    oldPlan: 'Free',
    newPlan: 'Pro',
    type: 'upgrade', // 'upgrade', 'downgrade', or 'changed'
    features: ['Feature 1', 'Feature 2']
));
```

---

### 5. **Usage Limit Warning** (`UsageLimitWarningNotification`)
**Trigger**: Sent when user reaches 75%, 90%, or 100% of plan limits  
**Template**: `resources/views/emails/usage/limit-warning.blade.php`  
**Contains**:
- Usage percentage alert
- Current usage metrics
- Visual stats (usage level, remaining)
- Upgrade call-to-action
- Benefits of upgrading

**When to Send**:
```php
// In your usage tracking service
if ($usagePercentage >= 75) {
    $user->notify(new UsageLimitWarningNotification(
        usageData: ['used' => 750, 'limit' => 1000],
        usagePercentage: 75,
        metrics: [
            'api_calls' => [
                'name' => 'API Calls',
                'used' => 750,
                'limit' => 1000,
                'percentage' => 75
            ]
        ]
    ));
}
```

---

### 6. **Export Ready** (`ExportReadyNotification`)
**Trigger**: Sent when data export is complete (for async exports)  
**Template**: `resources/views/emails/export/export-ready.blade.php`  
**Contains**:
- Export completion notice
- Export details (format, date range, records, file size)
- Download link (expires in 7 days)
- What's included description
- Create new export link

**When to Send**:
```php
// In ExportController or export job
$user->notify(new ExportReadyNotification(
    format: 'csv',
    downloadUrl: 'https://yourapp.com/exports/download/abc123',
    dateFrom: '2025-01-01',
    dateTo: '2025-10-12',
    recordCount: 1500,
    fileSize: '2.5 MB'
));
```

---

### 7. **Password Reset** (Built-in Laravel)
**Trigger**: Sent when user requests password reset  
**Template**: `resources/views/emails/auth/reset-password.blade.php`  
**Contains**:
- Reset password link (expires in 60 minutes)
- Security tips
- Warning if user didn't request reset
- Manual link copy option

**To customize**, override in `ResetPassword` notification.

---

## 🎨 **Email Design System**

### Base Layout
All emails extend the base layout: `resources/views/emails/layouts/base.blade.php`

**Features**:
- Professional gradient header with logo
- Mobile-responsive design
- Consistent brand colors (#F53003 primary)
- Reusable components (buttons, info boxes, stats cards)
- Footer with links and unsubscribe option

### Design Components

#### **Buttons**
```blade
<a href="{{ $url }}" class="button">
    Primary Action →
</a>

<a href="{{ $url }}" class="button button-secondary">
    Secondary Action →
</a>
```

#### **Info Boxes**
```blade
<div class="info-box">
    Standard info box
</div>

<div class="info-box info-box-success">
    Success message
</div>

<div class="info-box info-box-warning">
    Warning message
</div>

<div class="info-box info-box-danger">
    Danger/important message
</div>
```

#### **Stats Grid**
```blade
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">$99</div>
        <div class="stat-label">Total Paid</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">Pro</div>
        <div class="stat-label">Your Plan</div>
    </div>
</div>
```

---

## ⚙️ **Configuration**

### 1. **Environment Variables**

Add these to your `.env` file:

```bash
# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io  # For testing
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@withasisstant.ir"
MAIL_FROM_NAME="${APP_NAME}"
```

### 2. **Production Email Services**

#### **Option 1: Mailgun** (Recommended)
```bash
composer require symfony/mailgun-mailer symfony/http-client

MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.com
MAILGUN_SECRET=your-api-key
MAILGUN_ENDPOINT=api.mailgun.net
```

#### **Option 2: Amazon SES**
```bash
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
```

#### **Option 3: SendGrid**
```bash
composer require symfony/sendgrid-mailer symfony/http-client

MAIL_MAILER=sendgrid
SENDGRID_API_KEY=your-api-key
```

#### **Option 4: Postmark**
```bash
MAIL_MAILER=postmark
POSTMARK_TOKEN=your-server-token
```

### 3. **Queue Configuration**

All notifications implement `ShouldQueue` for async sending:

```bash
# .env
QUEUE_CONNECTION=database  # or redis

# Run migrations for queue tables
php artisan queue:table
php artisan migrate

# Start queue worker
php artisan queue:work
```

---

## 🧪 **Testing Emails**

### 1. **Test in Development (Mailtrap)**

Sign up for free at [https://mailtrap.io](https://mailtrap.io):

```bash
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
```

### 2. **Test with Artisan Tinker**

```bash
php artisan tinker
```

```php
// Test Welcome Email
$user = App\Models\User::first();
$user->notify(new App\Notifications\WelcomeNotification());

// Test Payment Receipt
$order = App\Models\Order::first();
$user->notify(new App\Notifications\PaymentReceivedNotification($order));

// Test Subscription Change
$user->notify(new App\Notifications\SubscriptionChangedNotification(
    oldPlan: 'Free',
    newPlan: 'Pro',
    type: 'upgrade',
    features: ['Unlimited API calls', 'Priority support']
));

// Test Usage Warning
$user->notify(new App\Notifications\UsageLimitWarningNotification(
    usageData: ['used' => 850, 'limit' => 1000],
    usagePercentage: 85,
    metrics: []
));

// Test Export Ready
$user->notify(new App\Notifications\ExportReadyNotification(
    format: 'csv',
    downloadUrl: 'https://yourapp.com/download/test',
    dateFrom: '2025-01-01',
    dateTo: '2025-10-12',
    recordCount: 1500,
    fileSize: '2.5 MB'
));
```

### 3. **Preview Emails in Browser**

Create a test route in `routes/web.php`:

```php
Route::get('/email-preview/{type}', function ($type) {
    $user = App\Models\User::first();
    
    return match($type) {
        'welcome' => view('emails.welcome.user-welcome', ['user' => $user]),
        'verify' => view('emails.auth.verify-email', [
            'user' => $user,
            'verificationUrl' => 'http://example.com/verify'
        ]),
        'payment' => view('emails.subscription.payment-received', [
            'order' => App\Models\Order::first()
        ]),
        'plan-change' => view('emails.subscription.plan-changed', [
            'user' => $user,
            'oldPlan' => 'Free',
            'newPlan' => 'Pro',
            'type' => 'upgrade',
            'features' => []
        ]),
        'usage' => view('emails.usage.limit-warning', [
            'user' => $user,
            'usageData' => ['used' => 850, 'limit' => 1000],
            'usagePercentage' => 85,
            'metrics' => []
        ]),
        'export' => view('emails.export.export-ready', [
            'user' => $user,
            'format' => 'csv',
            'downloadUrl' => 'http://example.com/download',
            'dateFrom' => '2025-01-01',
            'dateTo' => '2025-10-12',
            'recordCount' => 1500,
            'fileSize' => '2.5 MB'
        ]),
        default => 'Email type not found'
    };
})->middleware('auth');
```

Visit: `http://yourapp.test/email-preview/welcome`

---

## 📊 **Email Tracking & Analytics**

### 1. **Database Notifications Table**

Create notifications table to track sent emails:

```bash
php artisan notifications:table
php artisan migrate
```

Then update notifications to use database channel:

```php
public function via(object $notifiable): array
{
    return ['mail', 'database'];  // Add database
}
```

### 2. **Track Email Opens** (Optional)

Use services like:
- **Mailgun**: Built-in tracking
- **SendGrid**: Built-in tracking
- **Postmark**: Built-in tracking

Or implement custom tracking pixels.

### 3. **Monitor Email Deliverability**

```bash
# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

---

## 🔧 **Customization Guide**

### Create New Email Template

1. **Create notification**:
```bash
php artisan make:notification YourNotification
```

2. **Create template**:
```bash
# Create: resources/views/emails/your-category/your-email.blade.php
```

3. **Use base layout**:
```blade
@extends('emails.layouts.base')

@section('content')
    <h1 class="email-title">Your Title</h1>
    <!-- Your content -->
@endsection
```

4. **Update notification**:
```php
public function toMail($notifiable): MailMessage
{
    return (new MailMessage)
        ->subject('Your Subject')
        ->view('emails.your-category.your-email', [
            'user' => $notifiable,
            // your data
        ]);
}
```

---

## 🚀 **Production Checklist**

- [ ] Configure production mail service (Mailgun/SES/SendGrid)
- [ ] Set correct `MAIL_FROM_ADDRESS` and `MAIL_FROM_NAME`
- [ ] Set up queue worker (`php artisan queue:work`)
- [ ] Configure supervisor for queue workers
- [ ] Test all email templates
- [ ] Set up email monitoring/tracking
- [ ] Configure SPF and DKIM records
- [ ] Add unsubscribe functionality
- [ ] Set up bounce handling
- [ ] Test spam score (use mail-tester.com)
- [ ] Enable email rate limiting if needed
- [ ] Set up alerts for failed emails

---

## 📈 **Performance Tips**

1. **Use Queue Workers**: All notifications are queued by default
2. **Batch Notifications**: Send multiple emails in one job
3. **Email Throttling**: Limit emails per user (prevent spam)
4. **Cache Email Templates**: Use view caching in production
5. **Optimize Images**: Use optimized logo/images
6. **Monitor Queue Health**: Use Laravel Horizon

---

## 🆘 **Troubleshooting**

### Emails not sending?

1. **Check queue is running**:
```bash
php artisan queue:work --verbose
```

2. **Check failed jobs**:
```bash
php artisan queue:failed
```

3. **Test mail configuration**:
```bash
php artisan tinker
>>> Mail::raw('Test email', function ($message) {
...     $message->to('test@example.com')->subject('Test');
... });
```

4. **Check logs**:
```bash
tail -f storage/logs/laravel.log
```

### Emails going to spam?

1. Configure SPF record
2. Configure DKIM
3. Use reputable email service
4. Authenticate your domain
5. Test with mail-tester.com

---

## 📚 **Additional Resources**

- [Laravel Mail Documentation](https://laravel.com/docs/12.x/mail)
- [Laravel Notifications](https://laravel.com/docs/12.x/notifications)
- [Mailgun Documentation](https://documentation.mailgun.com/)
- [SendGrid Documentation](https://docs.sendgrid.com/)
- [Amazon SES Documentation](https://docs.aws.amazon.com/ses/)

---

## ✅ **Summary**

You now have a complete, professional transactional email system with:

✅ 7 beautiful, responsive email templates  
✅ Automated triggers for all user actions  
✅ Queued sending for performance  
✅ Professional design with brand consistency  
✅ Easy customization and extension  
✅ Production-ready configuration  

**Next Steps**: Configure your production email service and start sending!


