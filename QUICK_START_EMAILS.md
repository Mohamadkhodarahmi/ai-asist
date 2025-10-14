# 📧 Quick Start: Testing Your Email System

## 🚀 **5-Minute Setup**

### Step 1: Configure Mail Settings

Add to your `.env` file:

```bash
# For Testing (Mailtrap - Free)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS="noreply@withasisstant.ir"
MAIL_FROM_NAME="withasisstant"

# Queue for async sending
QUEUE_CONNECTION=database
```

### Step 2: Set Up Queue

```bash
# Create queue table
php artisan queue:table
php artisan migrate

# Start queue worker (in separate terminal)
php artisan queue:work
```

### Step 3: Test Emails!

```bash
# Test all emails at once
php artisan emails:test

# Test specific email
php artisan emails:test welcome
php artisan emails:test payment
php artisan emails:test plan-upgrade
php artisan emails:test usage-warning
php artisan emails:test export-ready

# Test for specific user
php artisan emails:test --user=1
```

---

## 📬 **Email Triggers**

### Automatic Triggers (Already Integrated)

1. **Welcome Email** → Sent when user registers
2. **Email Verification** → Sent when user registers  
3. **Payment Receipt** → Sent when payment confirmed
4. **Subscription Changed** → Sent when plan changes

### Manual Triggers (Add as Needed)

5. **Usage Warning** → Add to your usage tracking
6. **Export Ready** → Add to export jobs

---

## 🎯 **Test Workflow**

### 1. Register New User
Visit `/register` and create an account → Welcome email sent!

### 2. Make Test Payment
Complete a payment → Payment receipt sent!

### 3. Test via Tinker
```bash
php artisan tinker
```

```php
$user = User::first();

// Welcome email
$user->notify(new App\Notifications\WelcomeNotification());

// Payment receipt (needs an order)
$order = App\Models\Order::first();
$user->notify(new App\Notifications\PaymentReceivedNotification($order));

// Plan upgrade
$user->notify(new App\Notifications\SubscriptionChangedNotification(
    oldPlan: 'Free',
    newPlan: 'Pro',
    type: 'upgrade',
    features: ['Feature 1', 'Feature 2']
));

// Usage warning
$user->notify(new App\Notifications\UsageLimitWarningNotification(
    usageData: ['used' => 850, 'limit' => 1000],
    usagePercentage: 85,
    metrics: []
));

// Export ready
$user->notify(new App\Notifications\ExportReadyNotification(
    format: 'csv',
    downloadUrl: 'https://yourapp.com/download/test',
    dateFrom: '2025-01-01',
    dateTo: '2025-10-12',
    recordCount: 1500,
    fileSize: '2.5 MB'
));
```

---

## 🎨 **Preview Emails in Browser**

Add this to `routes/web.php` for quick previews:

```php
Route::get('/preview-emails', function () {
    $user = App\Models\User::first();
    
    return view('emails.welcome.user-welcome', ['user' => $user]);
})->middleware('auth');
```

Visit: `http://yourapp.test/preview-emails`

---

## ✅ **Checklist**

- [ ] `.env` configured with mail settings
- [ ] Queue table created and migrated
- [ ] Queue worker running (`php artisan queue:work`)
- [ ] Mailtrap account created (for testing)
- [ ] Tested welcome email
- [ ] Tested payment receipt
- [ ] Tested all other emails
- [ ] Emails look good on mobile
- [ ] Ready for production mail service

---

## 🚀 **Go to Production**

### 1. Choose Email Service

**Recommended: Mailgun** (Best for SaaS)
- Free: 5,000 emails/month
- Affordable beyond that
- Excellent deliverability
- Easy setup

```bash
composer require symfony/mailgun-mailer symfony/http-client
```

```.env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.withasisstant.ir
MAILGUN_SECRET=your-api-key
MAILGUN_ENDPOINT=api.mailgun.net
```

### 2. Configure SPF/DKIM

In your DNS:
```
TXT record: v=spf1 include:mailgun.org ~all
```

Add DKIM from Mailgun dashboard

### 3. Set Up Supervisor (Queue Workers)

Create `/etc/supervisor/conf.d/laravel-worker.conf`:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ai-asist/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/ai-asist/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start laravel-worker:*
```

### 4. Monitor Queue Health

Use Laravel Horizon (recommended):
```bash
composer require laravel/horizon
php artisan horizon:install
php artisan migrate
```

---

## 🆘 **Quick Troubleshooting**

### Emails not sending?
```bash
# Check queue
php artisan queue:work --verbose

# Check failed jobs
php artisan queue:failed

# Retry failed
php artisan queue:retry all
```

### Test mail config:
```bash
php artisan tinker
>>> Mail::raw('Test', fn($m) => $m->to('test@example.com')->subject('Test'));
```

### Check logs:
```bash
tail -f storage/logs/laravel.log
```

---

## 📚 **Full Documentation**

See `EMAIL_SYSTEM_GUIDE.md` for complete documentation.

---

## 🎉 **You're Done!**

Your professional email system is ready. Users will receive:

✅ Beautiful welcome emails when they sign up  
✅ Payment confirmations instantly  
✅ Plan change notifications  
✅ Usage warnings before limits  
✅ Export ready notifications  

**All emails are mobile-responsive, professionally designed, and queued for performance!**


