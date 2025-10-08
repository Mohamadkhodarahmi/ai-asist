# 🛠️ Admin Panel Guide

## Overview

The admin panel provides comprehensive management and monitoring capabilities for your AI Assistant platform. Only users with admin privileges can access these features.

## Features

### 📊 Analytics Dashboard (`/admin/analytics`)
- **Real-time Metrics**: Monitor key performance indicators
  - Total users, new users, active users
  - Monthly Recurring Revenue (MRR)
  - API call statistics
  - Average response times
- **Visual Charts**:
  - User registration trends
  - Revenue growth over time
  - Plan distribution (pie chart)
  - Feature usage statistics
- **Time Range Selection**: View data for 7, 30, 90, or 365 days
- **Export Functionality**: Download analytics data in CSV format

### 👥 User Management (`/admin/users`)
- **User Overview**:
  - Total users, admin users, active users
  - New users today
- **Search & Filter**: Find users by name or email
- **User Actions**:
  - View detailed user profiles
  - Toggle admin privileges
  - Update user information
  - Delete users (with safeguards)
- **User Details**:
  - Chat statistics
  - Document usage
  - Order history
  - Account information

### 📋 Activity Logs (`/admin/logs`)
- **Activity Types**:
  - Chat interactions
  - Document uploads
  - User activities
- **Filtering**: View all activities or filter by type
- **Real-time Updates**: See recent user actions
- **Detailed Information**: View context and metadata for each activity

### ⚙️ System Health (`/admin/system`)
- **Health Monitoring**:
  - Database connectivity
  - Storage availability
  - Cache status
  - Queue status
- **System Information**:
  - PHP version
  - Laravel version
  - Environment settings
  - Database configuration
- **Quick Actions**:
  - Clear application cache
  - Refresh health status

## How to Grant Admin Access

### Using Artisan Command (Recommended)

```bash
# Grant admin privileges
php artisan user:make-admin email@example.com

# Or run without arguments for interactive mode
php artisan user:make-admin

# Revoke admin privileges
php artisan user:revoke-admin email@example.com

# List all admin users
php artisan user:list-admins
```

### Using Database

```sql
-- Grant admin access
UPDATE users SET is_admin = 1 WHERE email = 'email@example.com';

-- Revoke admin access
UPDATE users SET is_admin = 0 WHERE email = 'email@example.com';
```

## Access Control

### Middleware Protection
All admin routes are protected by the `admin` middleware, which:
1. Verifies user authentication
2. Checks if the user has `is_admin = true`
3. Returns 403 error for unauthorized access

### Current Admin User
- **Email**: mohamad@gmail.com
- **ID**: 1
- **Granted**: Initially during setup

## Navigation

### Desktop
- Admin panel link appears in the user dropdown menu (top right)
- Only visible to users with admin privileges

### Mobile
- Admin panel link appears in the mobile menu
- Same access restrictions apply

### Admin Panel Sidebar
Once in the admin panel, use the sidebar to navigate between:
- Analytics Dashboard
- User Management
- Activity Logs
- System Health
- Back to App (return to main application)

## API Endpoints

### User Management
- `GET /admin/users` - List all users
- `GET /admin/users/{user}` - View user details
- `POST /admin/users/{user}/toggle-admin` - Toggle admin status
- `PUT /admin/users/{user}` - Update user information
- `DELETE /admin/users/{user}` - Delete user
- `GET /admin/users-stats` - Get user statistics

### Analytics
- `GET /admin/analytics` - Main dashboard
- `GET /admin/analytics/data` - Analytics data (JSON)
- `GET /admin/analytics/users` - User metrics
- `GET /admin/analytics/revenue` - Revenue metrics
- `GET /admin/analytics/engagement` - Engagement metrics
- `GET /admin/analytics/growth` - Growth metrics
- `GET /admin/analytics/usage` - Usage metrics
- `GET /admin/analytics/cohorts` - Cohort analysis
- `GET /admin/analytics/features` - Feature usage
- `GET /admin/analytics/export` - Export data

### System
- `GET /admin/system` - System health dashboard
- `GET /admin/system/health` - Health status (JSON)
- `POST /admin/system/clear-cache` - Clear all caches

### Activity Logs
- `GET /admin/logs` - View activity logs
- Query parameter: `type` (all, chat, documents, activity)

## Security Considerations

1. **Admin Safeguards**:
   - Cannot delete the last admin user
   - Admin status changes are logged
   - Requires confirmation for destructive actions

2. **Access Control**:
   - All admin routes require authentication
   - Non-admin users receive 403 errors
   - CSRF protection on all POST/PUT/DELETE requests

3. **Best Practices**:
   - Limit admin access to trusted users only
   - Regularly review admin user list
   - Monitor admin activity logs
   - Use strong passwords for admin accounts

## Troubleshooting

### Cannot Access Admin Panel
- Verify your user has `is_admin = true` in the database
- Check if you're logged in
- Clear browser cache and cookies
- Run: `php artisan cache:clear`

### Charts Not Loading
- Ensure JavaScript is enabled
- Check browser console for errors
- Verify Chart.js is loaded
- Try refreshing the page

### No Data Showing
- This is normal for new installations
- Data will appear as users interact with the system
- Generate test data using seeders if needed

### Permission Errors
- Verify file permissions: `storage/` and `bootstrap/cache/`
- Check `.env` file configuration
- Ensure database connection is working

## Development

### Adding New Admin Features

1. Create controller in `app/Http/Controllers/Admin/`
2. Add routes in `routes/web.php` under the admin group
3. Create views in `resources/views/admin/`
4. Update sidebar navigation in `resources/views/admin/layout.blade.php`

### Testing Admin Features

```bash
# Run all tests
php artisan test

# Test specific feature
php artisan test --filter AdminTest
```

## Support

For issues or feature requests related to the admin panel:
1. Check the application logs: `storage/logs/laravel.log`
2. Review error messages in the browser console
3. Verify database connectivity and migrations
4. Contact the development team

## Changelog

### Version 1.0.0 (Current)
- Initial admin panel implementation
- Analytics dashboard with charts
- User management system
- Activity logs viewer
- System health monitoring
- Admin privilege management
- Export functionality

