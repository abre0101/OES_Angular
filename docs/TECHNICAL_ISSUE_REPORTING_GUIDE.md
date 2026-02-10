# Technical Issue Reporting System - Implementation Guide

## Overview
This system allows students to report technical issues during exams, with automatic capture of system information and priority-based tracking for administrators.

## Features Implemented

### ✅ For Students
- **Report Issue Form** - Comprehensive form to report technical issues
- **In-Exam Quick Reporter** - Floating button during exams for immediate reporting
- **Auto-capture System Info** - Browser, OS, screen resolution, IP address
- **Issue Type Selection** - Predefined categories for common issues
- **Real-time Submission** - AJAX-based quick reporting without page reload

### ✅ For Administrators
- **Issue Management Dashboard** - View and manage all reported issues
- **Priority-based Sorting** - Critical issues appear first
- **Filtering & Search** - Filter by status, priority, or search by keywords
- **Detailed Issue View** - Complete information including system details
- **Status Updates** - Mark issues as pending, resolved, or closed
- **Admin Notes** - Add internal notes about resolution

### ✅ Issue Types Supported
- ❌ Connectivity Problems (Internet drops)
- ❌ System Errors (Page freezing/crashing)
- ❌ Submission Failures (Answers not saving)
- ❌ Display Issues (Questions not showing)
- ❌ Timer Malfunctioning
- ❌ Login Problems
- 🔧 Other Technical Issues

### ✅ Priority Levels
- **Critical** - Connectivity issues (exam cannot continue)
- **High** - Submission failures, timer errors, system errors
- **Medium** - Display issues, other problems
- **Low** - Minor issues

## Installation Steps

### Step 1: Run Database Migration
Execute the SQL migration to enhance the technical_issues table:

```bash
# Navigate to your database management tool (phpMyAdmin, MySQL Workbench, etc.)
# Run the migration file: database/migrations/enhance_technical_issues.sql
```

Or run via command line:
```bash
mysql -u root -p oes_professional < database/migrations/enhance_technical_issues.sql
```

### Step 2: Add Menu Link for Admin
Add this to your admin sidebar (`Admin/sidebar-component.php`):

```php
<li>
    <a href="TechnicalIssues.php">
        <i class="fas fa-exclamation-triangle"></i> Technical Issues
    </a>
</li>
```

### Step 3: Add Menu Link for Students
Add this to your student dashboard (`Student/index.php`):

```php
<a href="ReportIssue.php" class="btn btn-warning">
    <i class="fas fa-exclamation-circle"></i> Report Technical Issue
</a>
```

### Step 4: Integrate Quick Reporter in Exam Pages
Add this to your exam interface (e.g., `Student/TakeExam.php`):

```html
<!-- Add before closing </body> tag -->
<script src="exam-issue-reporter.js"></script>
<script>
    // Set the current exam ID
    var currentExamId = <?php echo $exam_id; ?>;
    
    // Add floating report button
    window.addEventListener('load', addFloatingReportButton);
</script>
```

## File Structure

```
├── Student/
│   ├── ReportIssue.php              # Main issue reporting form
│   ├── QuickReportIssue.php         # AJAX endpoint for quick reports
│   └── exam-issue-reporter.js       # JavaScript widget for in-exam reporting
│
├── Admin/
│   ├── TechnicalIssues.php          # Admin dashboard for managing issues
│   └── ViewIssueDetails.php         # AJAX endpoint for viewing issue details
│
├── database/
│   └── migrations/
│       └── enhance_technical_issues.sql  # Database migration
│
└── docs/
    └── TECHNICAL_ISSUE_REPORTING_GUIDE.md  # This file
```

## Usage Examples

### For Students

#### Method 1: From Dashboard
1. Go to student dashboard
2. Click "Report Technical Issue"
3. Select the exam
4. Choose issue type
5. Describe the problem
6. Submit (system info captured automatically)

#### Method 2: During Exam (Quick Report)
1. While taking exam, click the red "Report Issue" button (bottom-right)
2. Select issue type
3. Describe the problem
4. Submit
5. Continue with exam (timer keeps running)

### For Administrators

#### View All Issues
1. Go to Admin → Technical Issues
2. See dashboard with statistics
3. Filter by status, priority, or search
4. Click "View" to see details

#### Resolve an Issue
1. Click "View" on any issue
2. Review all details including system information
3. Update status (Pending → Resolved → Closed)
4. Add admin notes about resolution
5. Click "Update Issue"

## API Endpoints

### POST /Student/QuickReportIssue.php
Quick report submission during exam

**Request:**
```json
{
  "exam_id": "123",
  "issue_type": "connectivity",
  "issue_description": "Internet connection dropped",
  "browser_info": "Mozilla/5.0...",
  "os_info": "Win32",
  "screen_resolution": "1920x1080"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Issue reported successfully. Reference ID: #456",
  "issue_id": 456
}
```

### GET /Admin/ViewIssueDetails.php?issue_id=123
Get detailed information about a specific issue

**Response:** HTML content with issue details and update form

## Database Schema

### technical_issues Table
```sql
CREATE TABLE `technical_issues` (
  `issue_id` int(11) PRIMARY KEY AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `issue_type` ENUM('connectivity', 'system_error', 'submission_failure', 
                    'display_issue', 'timer_error', 'login_problem', 'other'),
  `issue_description` TEXT NOT NULL,
  `browser_info` VARCHAR(255),
  `os_info` VARCHAR(255),
  `screen_resolution` VARCHAR(50),
  `ip_address` VARCHAR(45),
  `screenshot_path` VARCHAR(500),
  `admin_notes` TEXT,
  `resolved_by` INT(11),
  `resolved_at` TIMESTAMP NULL,
  `priority` ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
  `status` ENUM('pending', 'resolved', 'closed') DEFAULT 'pending',
  `reported_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (student_id) REFERENCES students(student_id),
  FOREIGN KEY (exam_id) REFERENCES exams(exam_id),
  FOREIGN KEY (resolved_by) REFERENCES administrators(admin_id)
);
```

## Customization Options

### Change Priority Logic
Edit `Student/ReportIssue.php` and `Student/QuickReportIssue.php`:

```php
// Customize priority assignment
$priority = 'medium';
if (in_array($issue_type, ['submission_failure', 'timer_error'])) {
    $priority = 'high';
}
```

### Add Email Notifications
Add to `Student/QuickReportIssue.php` after successful insert:

```php
// Send email to admin
$to = "admin@example.com";
$subject = "Critical Issue Reported - Issue #" . $issue_id;
$message = "A critical technical issue has been reported...";
mail($to, $subject, $message);
```

### Customize Floating Button Position
Edit `Student/exam-issue-reporter.js`:

```javascript
button.style.cssText = `
    position: fixed;
    bottom: 20px;    // Change this
    right: 20px;     // Change this
    ...
`;
```

## Best Practices

1. **Monitor Critical Issues** - Check dashboard regularly for critical/high priority issues
2. **Respond Quickly** - Students may be waiting during exams
3. **Add Detailed Notes** - Document resolution steps for future reference
4. **Review Patterns** - Look for recurring issues to fix root causes
5. **Test Regularly** - Ensure reporting system works before major exams

## Troubleshooting

### Issue: "Table doesn't exist" error
**Solution:** Run the database migration file

### Issue: Quick reporter not showing
**Solution:** Ensure `exam-issue-reporter.js` is included and `currentExamId` is set

### Issue: System info not captured
**Solution:** Check browser JavaScript console for errors

### Issue: Admin can't see issues
**Solution:** Verify foreign key relationships and student/exam data exists

## Future Enhancements

- [ ] Screenshot upload capability
- [ ] Email notifications to admins
- [ ] SMS alerts for critical issues
- [ ] Exam timer pause option (requires approval workflow)
- [ ] Student notification when issue is resolved
- [ ] Analytics dashboard for issue trends
- [ ] Export issues to CSV/PDF

## Support

For questions or issues with this system, contact your system administrator or refer to the main project documentation.
