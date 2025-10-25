# Testing Guide - CRUD Web Application

## Overview
This document outlines the testing procedures for the CRUD Web Application to ensure all features work correctly.

## Test Environment Setup

### Prerequisites
- Web server running (Apache/Nginx)
- MySQL database with schema imported
- PHP 7.4+ installed
- Modern web browser (Chrome, Firefox, Safari, or Edge)

### Test Data
The application comes with:
- Default admin user: username `admin`, password `admin123`
- Empty database ready for testing

## Manual Testing Checklist

### 1. Authentication Tests

#### Registration
- [ ] Navigate to registration page
- [ ] Fill in all required fields
- [ ] Verify email validation
- [ ] Verify password confirmation matching
- [ ] Submit form and verify account creation
- [ ] Verify redirect to login page
- [ ] Verify success message displayed

**Expected Result**: New user account created successfully

#### Login
- [ ] Navigate to login page
- [ ] Enter valid credentials
- [ ] Verify successful login
- [ ] Verify redirect to dashboard
- [ ] Check session is created
- [ ] Verify last login time updated

**Expected Result**: User logged in and redirected to dashboard

#### Failed Login
- [ ] Enter invalid username
- [ ] Enter invalid password
- [ ] Verify error message displayed
- [ ] Verify no session created

**Expected Result**: Error message shown, login denied

#### Password Recovery
- [ ] Navigate to forgot password page
- [ ] Enter registered email
- [ ] Verify success message
- [ ] Check logs for reset token

**Expected Result**: Password reset instructions generated

### 2. Dashboard Tests

#### Dashboard Display
- [ ] Verify page loads without errors
- [ ] Check all statistics cards display correctly
- [ ] Verify task widget shows recent tasks
- [ ] Verify calendar widget shows upcoming events
- [ ] Verify weather widget displays
- [ ] Verify messages widget shows recent messages
- [ ] Check responsive design on mobile

**Expected Result**: All widgets display properly

#### Dashboard Navigation
- [ ] Click each sidebar menu item
- [ ] Verify correct page loads
- [ ] Check breadcrumb navigation
- [ ] Test mobile menu toggle
- [ ] Verify user menu dropdown

**Expected Result**: Navigation works smoothly

#### Welcome Tour
- [ ] First-time login triggers tour offer
- [ ] Accept tour
- [ ] Navigate through all tour steps
- [ ] Verify tooltip positioning
- [ ] Complete or skip tour
- [ ] Verify tour doesn't show again

**Expected Result**: Tour guides new users effectively

### 3. Task Management Tests

#### Create Task
- [ ] Click "New Task" button
- [ ] Fill in task title
- [ ] Add description
- [ ] Set priority level
- [ ] Set due date
- [ ] Submit form
- [ ] Verify task appears in list
- [ ] Check database entry created

**Expected Result**: Task created successfully

#### Edit Task
- [ ] Click edit button on a task
- [ ] Modify task details
- [ ] Change status
- [ ] Update priority
- [ ] Save changes
- [ ] Verify updates reflected
- [ ] Check database updated

**Expected Result**: Task updated successfully

#### Delete Task
- [ ] Click delete button
- [ ] Confirm deletion
- [ ] Verify task removed from list
- [ ] Check database entry deleted

**Expected Result**: Task deleted successfully

#### Task Filters
- [ ] Click "All" filter
- [ ] Click "Pending" filter
- [ ] Click "Completed" filter
- [ ] Verify correct tasks shown

**Expected Result**: Filters work correctly

#### Toggle Task Status
- [ ] Check task checkbox
- [ ] Verify status changes to completed
- [ ] Uncheck checkbox
- [ ] Verify status changes to pending

**Expected Result**: Status toggles correctly

### 4. Calendar Tests

#### Create Event
- [ ] Click "New Event" button
- [ ] Enter event title
- [ ] Add description
- [ ] Set start date/time
- [ ] Set end date/time
- [ ] Add location
- [ ] Choose color
- [ ] Submit form
- [ ] Verify event appears in list

**Expected Result**: Event created successfully

#### View Events
- [ ] Check upcoming events list
- [ ] Verify events sorted by date
- [ ] Check event details display
- [ ] Verify color coding works

**Expected Result**: Events display correctly

#### Navigate Calendar
- [ ] Click previous month button
- [ ] Click next month button
- [ ] Click "Today" button
- [ ] Verify correct month displayed

**Expected Result**: Calendar navigation works

#### Delete Event
- [ ] Click delete button on event
- [ ] Confirm deletion
- [ ] Verify event removed

**Expected Result**: Event deleted successfully

### 5. Messenger Tests

#### Send Message
- [ ] Navigate to Messages page
- [ ] Select a contact
- [ ] Type message
- [ ] Press Enter or click Send
- [ ] Verify message appears in chat
- [ ] Check database entry created

**Expected Result**: Message sent successfully

#### Receive Message
- [ ] Have another user send message
- [ ] Refresh or wait for auto-refresh
- [ ] Verify message appears
- [ ] Check unread badge updates

**Expected Result**: Messages received in real-time

#### Read Messages
- [ ] Click on unread conversation
- [ ] Verify messages display
- [ ] Check messages marked as read
- [ ] Verify badge count decreases

**Expected Result**: Messages marked as read

#### Message Auto-Refresh
- [ ] Keep conversation open
- [ ] Wait for auto-refresh (3 seconds)
- [ ] Verify new messages appear

**Expected Result**: Auto-refresh works

### 6. Profile Tests

#### Update Profile
- [ ] Navigate to Profile page
- [ ] Change full name
- [ ] Update email
- [ ] Submit form
- [ ] Verify success message
- [ ] Check database updated
- [ ] Verify changes reflected in topbar

**Expected Result**: Profile updated successfully

#### Change Password
- [ ] Enter current password
- [ ] Enter new password
- [ ] Confirm new password
- [ ] Submit form
- [ ] Verify success message
- [ ] Logout and login with new password

**Expected Result**: Password changed successfully

#### Password Validation
- [ ] Try mismatched passwords
- [ ] Try short password
- [ ] Try wrong current password
- [ ] Verify appropriate error messages

**Expected Result**: Validation works correctly

### 7. Admin Panel Tests (Admin Users Only)

#### Access Admin Panel
- [ ] Login as admin user
- [ ] Click "Admin Panel" in sidebar
- [ ] Verify admin dashboard loads
- [ ] Check statistics display

**Expected Result**: Admin panel accessible

#### View Users
- [ ] Check recent users list
- [ ] Verify user details shown
- [ ] Check roles and status display

**Expected Result**: User list displays correctly

#### System Diagnostics
- [ ] Navigate to Diagnostics page
- [ ] Check PHP configuration
- [ ] Verify database status
- [ ] Check table existence
- [ ] Review PHP extensions
- [ ] Check server info
- [ ] Verify disk space info

**Expected Result**: All diagnostics display correctly

#### Activity Log
- [ ] Review activity log entries
- [ ] Verify timestamps
- [ ] Check user attribution
- [ ] Verify IP addresses logged

**Expected Result**: Activity log accurate

### 8. Security Tests

#### Session Management
- [ ] Login successfully
- [ ] Wait for session timeout
- [ ] Try to access protected page
- [ ] Verify redirect to login

**Expected Result**: Session timeout works

#### Role-Based Access
- [ ] Login as regular user
- [ ] Try to access admin panel directly
- [ ] Verify access denied

**Expected Result**: Access control works

#### SQL Injection Prevention
- [ ] Try SQL injection in login form
- [ ] Try SQL injection in task creation
- [ ] Verify no database errors
- [ ] Verify input sanitized

**Expected Result**: SQL injection prevented

#### XSS Prevention
- [ ] Try XSS in task description
- [ ] Try XSS in message
- [ ] Verify scripts don't execute
- [ ] Verify content escaped

**Expected Result**: XSS attacks prevented

### 9. Responsive Design Tests

#### Desktop (1920x1080)
- [ ] All elements visible
- [ ] Proper spacing
- [ ] No horizontal scroll
- [ ] All features accessible

**Expected Result**: Perfect layout

#### Tablet (768x1024)
- [ ] Responsive grid adjusts
- [ ] Navigation accessible
- [ ] All features work
- [ ] Touch-friendly interface

**Expected Result**: Good tablet experience

#### Mobile (375x667)
- [ ] Mobile menu appears
- [ ] Sidebar toggles correctly
- [ ] All features accessible
- [ ] Touch-friendly buttons
- [ ] No layout breaks

**Expected Result**: Excellent mobile experience

### 10. Performance Tests

#### Page Load Times
- [ ] Dashboard loads < 2 seconds
- [ ] Tasks page loads quickly
- [ ] Messages load smoothly
- [ ] No significant delays

**Expected Result**: Fast loading times

#### AJAX Operations
- [ ] Task creation is instant
- [ ] Message sending is quick
- [ ] Updates happen smoothly
- [ ] No lag or freezing

**Expected Result**: Smooth AJAX operations

#### Database Queries
- [ ] No N+1 query problems
- [ ] Proper indexing used
- [ ] Queries optimized
- [ ] Good response times

**Expected Result**: Efficient database usage

## Browser Compatibility Testing

### Chrome
- [ ] All features work
- [ ] UI displays correctly
- [ ] No console errors

### Firefox
- [ ] All features work
- [ ] UI displays correctly
- [ ] No console errors

### Safari
- [ ] All features work
- [ ] UI displays correctly
- [ ] No console errors

### Edge
- [ ] All features work
- [ ] UI displays correctly
- [ ] No console errors

## Test Results Template

```
Test Date: ___________
Tester: ___________
Environment: ___________

Feature: ___________
Test Case: ___________
Expected Result: ___________
Actual Result: ___________
Status: PASS / FAIL
Notes: ___________
```

## Bug Reporting

If you find any issues during testing, document:
1. Steps to reproduce
2. Expected behavior
3. Actual behavior
4. Browser and version
5. Screenshot if applicable
6. Error messages

## Conclusion

All tests should pass before considering the application production-ready. Any failed tests should be documented and fixed before deployment.

---

**Testing Guide Version**: 1.0.0  
**Last Updated**: 2024
