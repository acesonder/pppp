# PPPP Testing Guide

## Overview

This document provides a comprehensive testing checklist for the PPPP application.

## Test Accounts

After running `database/demo-data.sql`, you'll have these accounts:

| Username | Email | Password | Role |
|----------|-------|----------|------|
| admin | admin@pppp.com | admin123 | Admin |
| john_doe | john@example.com | admin123 | User |
| jane_smith | jane@example.com | admin123 | Manager |
| bob_wilson | bob@example.com | admin123 | User |
| alice_brown | alice@example.com | admin123 | User |

## Testing Checklist

### 1. Authentication Tests

- [ ] **Registration**
  - [ ] Register new user with valid data
  - [ ] Try duplicate username (should fail)
  - [ ] Try duplicate email (should fail)
  - [ ] Try weak password (should fail)
  - [ ] Verify auto-login after registration
  - [ ] Check welcome tour appears

- [ ] **Login**
  - [ ] Login with username
  - [ ] Login with email
  - [ ] Try wrong password (should fail)
  - [ ] Test "Remember Me" functionality
  - [ ] Verify redirect to dashboard

- [ ] **Forgot Password**
  - [ ] Request password reset
  - [ ] Check token generation
  - [ ] Follow reset link (demo shows link)
  - [ ] Reset password successfully

- [ ] **Logout**
  - [ ] Click logout
  - [ ] Verify session cleared
  - [ ] Try accessing protected pages (should redirect)

### 2. Dashboard Tests

- [ ] **Layout**
  - [ ] Dashboard loads properly
  - [ ] All widgets display correctly
  - [ ] Quick stats show accurate counts
  - [ ] Responsive on mobile

- [ ] **Widgets**
  - [ ] Tasks widget shows recent tasks
  - [ ] Calendar widget shows upcoming events
  - [ ] Weather widget displays
  - [ ] Inbox widget shows message count
  - [ ] Click widget actions work

- [ ] **Navigation**
  - [ ] All navbar links work
  - [ ] Dropdowns open/close correctly
  - [ ] Mobile menu toggles
  - [ ] Profile menu accessible

### 3. Task Management (CRUD) Tests

- [ ] **Create**
  - [ ] Click "+ New Task"
  - [ ] Fill all fields
  - [ ] Create with minimal data (title only)
  - [ ] Verify task appears in list
  - [ ] Check task on dashboard

- [ ] **Read**
  - [ ] View all tasks
  - [ ] See task details
  - [ ] Check status badges
  - [ ] Check priority badges
  - [ ] View due dates

- [ ] **Update**
  - [ ] Click "Edit" on task
  - [ ] Modify title
  - [ ] Change status
  - [ ] Change priority
  - [ ] Update due date
  - [ ] Save changes
  - [ ] Verify updates appear

- [ ] **Delete**
  - [ ] Click "Delete" button
  - [ ] Confirm deletion
  - [ ] Verify task removed
  - [ ] Check dashboard updated

- [ ] **Filter**
  - [ ] Filter by "All Tasks"
  - [ ] Filter by "Pending"
  - [ ] Filter by "In Progress"
  - [ ] Filter by "Completed"

### 4. Calendar & Events (CRUD) Tests

- [ ] **Create Event**
  - [ ] Click "+ New Event"
  - [ ] Fill all fields
  - [ ] Create with date only
  - [ ] Add time and location
  - [ ] Save event

- [ ] **View Calendar**
  - [ ] See current month
  - [ ] Events display on dates
  - [ ] Navigate to previous month
  - [ ] Navigate to next month
  - [ ] View event list below calendar

- [ ] **Update Event**
  - [ ] Click event
  - [ ] Edit details
  - [ ] Change date/time
  - [ ] Save changes
  - [ ] Verify on calendar

- [ ] **Delete Event**
  - [ ] Delete event
  - [ ] Confirm deletion
  - [ ] Check removed from calendar

### 5. Messenger Tests

- [ ] **Conversation List**
  - [ ] See all users
  - [ ] View last message
  - [ ] Check unread counts
  - [ ] Search conversations

- [ ] **Sending Messages**
  - [ ] Select user
  - [ ] Type message
  - [ ] Send with Enter key
  - [ ] Send with button
  - [ ] Verify message appears

- [ ] **Receiving Messages**
  - [ ] Login as different user
  - [ ] Send message to first user
  - [ ] Check notification badge
  - [ ] View message

- [ ] **Message Features**
  - [ ] Online status indicators
  - [ ] Timestamps display
  - [ ] Unread badges update
  - [ ] Conversation search works

### 6. Profile Tests

- [ ] **View Profile**
  - [ ] Click profile menu
  - [ ] Select "My Profile"
  - [ ] View all tabs
  - [ ] Check information accuracy

- [ ] **Update Profile**
  - [ ] Change name
  - [ ] Change email
  - [ ] Save changes
  - [ ] Verify navbar updates

- [ ] **Change Password**
  - [ ] Go to Security tab
  - [ ] Enter current password
  - [ ] Enter new password
  - [ ] Confirm new password
  - [ ] Save and verify

- [ ] **Activity Log**
  - [ ] View Activity tab
  - [ ] Check recent actions
  - [ ] Verify timestamps

### 7. Settings Tests

- [ ] **Dashboard Widgets**
  - [ ] Toggle widgets on/off
  - [ ] Save settings
  - [ ] Return to dashboard
  - [ ] Verify changes applied

- [ ] **Appearance**
  - [ ] Toggle dark mode
  - [ ] Check colors change
  - [ ] Verify saved in localStorage

- [ ] **Notifications**
  - [ ] Toggle notification settings
  - [ ] Check all options

### 8. Admin Portal Tests (Admin Only)

- [ ] **Access**
  - [ ] Login as admin
  - [ ] Click "Admin" in navbar
  - [ ] Verify access granted

- [ ] **Dashboard Tab**
  - [ ] View statistics
  - [ ] Check user count
  - [ ] Check task count
  - [ ] View recent activity

- [ ] **Diagnostics Tab**
  - [ ] Check system health
  - [ ] View PHP version
  - [ ] Check database connection
  - [ ] Check disk space

- [ ] **Users Tab**
  - [ ] View all users
  - [ ] See user details
  - [ ] Check roles
  - [ ] Check status

- [ ] **Settings Tab**
  - [ ] View system settings
  - [ ] Toggle maintenance mode
  - [ ] Toggle user registration

- [ ] **Tools Tab**
  - [ ] Test all tool buttons
  - [ ] Verify alerts appear

### 9. Responsive Design Tests

- [ ] **Desktop (1920x1080)**
  - [ ] All pages render correctly
  - [ ] No horizontal scroll
  - [ ] Images load properly

- [ ] **Tablet (768x1024)**
  - [ ] Layout adjusts
  - [ ] Navigation accessible
  - [ ] Widgets stack properly

- [ ] **Mobile (375x667)**
  - [ ] Mobile menu works
  - [ ] Forms are usable
  - [ ] Touch targets adequate
  - [ ] No elements cut off

### 10. Security Tests

- [ ] **Authentication**
  - [ ] Cannot access dashboard without login
  - [ ] Session expires properly
  - [ ] SQL injection protected (try: ' OR '1'='1)
  - [ ] XSS protected (try: <script>alert('XSS')</script>)

- [ ] **Authorization**
  - [ ] Non-admin cannot access /admin
  - [ ] Users can only see own tasks
  - [ ] Users can only edit own data

- [ ] **Data Validation**
  - [ ] Required fields enforced
  - [ ] Email format validated
  - [ ] Date format validated

### 11. API Endpoint Tests

- [ ] **Messages API**
  - [ ] GET /api/messages.php returns unread count
  - [ ] Returns recent messages
  - [ ] JSON format correct

- [ ] **Notifications API**
  - [ ] GET /api/notifications.php returns count
  - [ ] Returns recent notifications
  - [ ] JSON format correct

- [ ] **Send Message API**
  - [ ] POST /api/send-message.php works
  - [ ] Validates input
  - [ ] Returns success/error

### 12. Browser Compatibility

Test on:
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

### 13. Performance Tests

- [ ] **Page Load Times**
  - [ ] Homepage loads < 2s
  - [ ] Dashboard loads < 3s
  - [ ] Calendar loads < 2s

- [ ] **Database Queries**
  - [ ] No N+1 queries
  - [ ] Indexes used properly
  - [ ] Queries optimized

### 14. Error Handling

- [ ] **Form Errors**
  - [ ] Display user-friendly messages
  - [ ] Highlight invalid fields
  - [ ] Preserve form data on error

- [ ] **404 Errors**
  - [ ] Try /nonexistent-page.php
  - [ ] Verify error handling

- [ ] **Database Errors**
  - [ ] Graceful degradation
  - [ ] No sensitive data exposed

## Automated Testing (Future)

Consider adding:
- Unit tests for functions
- Integration tests for workflows
- End-to-end tests with Selenium
- API tests with Postman

## Bug Reporting

When you find a bug:
1. Note the steps to reproduce
2. Record browser/OS
3. Take screenshot
4. Check console for errors
5. Report on GitHub Issues

## Test Data Cleanup

To reset test data:
```sql
-- Run schema.sql again to reset
-- Then run demo-data.sql for sample data
```

## Success Criteria

All tests pass ✅
- No critical bugs
- No security vulnerabilities
- Responsive on all devices
- Good performance
- User-friendly interface

---

**Happy Testing! 🧪**
