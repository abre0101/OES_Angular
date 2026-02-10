# Technical Issue Reporting - Setup Checklist

Use this checklist to ensure everything is set up correctly.

## ☐ Step 1: Database Migration

Choose ONE method and complete it:

### Option A: Web Interface (Recommended)
- [ ] Navigate to: `http://localhost/database/migrations/run-migration-web.php`
- [ ] Click "Run Migration Now" button
- [ ] Verify "Migration completed successfully!" message appears
- [ ] Click "Go to Technical Issues Dashboard" link to test

### Option B: phpMyAdmin
- [ ] Open phpMyAdmin in browser
- [ ] Select `oes_professional` database from left sidebar
- [ ] Click "SQL" tab at the top
- [ ] Open file: `database/migrations/enhance_technical_issues.sql`
- [ ] Copy entire contents and paste into SQL box
- [ ] Click "Go" button
- [ ] Verify "Query executed successfully" message

### Option C: Command Line
- [ ] Open terminal/command prompt
- [ ] Navigate to project directory
- [ ] Run: `mysql -u root -p oes_professional < database/migrations/enhance_technical_issues.sql`
- [ ] Enter your MySQL password
- [ ] Verify no errors appear

## ☐ Step 2: Add Admin Menu Link

- [ ] Open file: `Admin/sidebar-component.php`
- [ ] Find the menu items section (look for other `<li>` tags)
- [ ] Add this code:
```php
<li>
    <a href="TechnicalIssues.php">
        <i class="fas fa-exclamation-triangle"></i> Technical Issues
    </a>
</li>
```
- [ ] Save the file

## ☐ Step 3: Add Student Menu Links

- [ ] Open file: `Student/index.php`
- [ ] Find where other dashboard buttons/links are
- [ ] Add these buttons:
```php
<a href="ReportIssue.php" class="btn btn-warning">
    <i class="fas fa-exclamation-circle"></i> Report Technical Issue
</a>

<a href="MyReportedIssues.php" class="btn btn-info">
    <i class="fas fa-list-alt"></i> My Reported Issues
</a>
```
- [ ] Save the file

## ☐ Step 4: Test Student Reporting

- [ ] Login as a student
- [ ] Navigate to: `Student/ReportIssue.php`
- [ ] Verify the form loads correctly
- [ ] Check that system info is auto-detected (Browser, OS, Screen)
- [ ] Select an exam from dropdown
- [ ] Choose an issue type
- [ ] Enter a test description
- [ ] Click "Submit Issue Report"
- [ ] Verify success message with Issue ID appears

## ☐ Step 5: Test Admin Dashboard

- [ ] Login as admin
- [ ] Navigate to: `Admin/TechnicalIssues.php`
- [ ] Verify dashboard loads with statistics
- [ ] Check that the test issue from Step 4 appears
- [ ] Click "View" button on the test issue
- [ ] Verify all details are shown (student info, system info, etc.)
- [ ] Try updating the status to "Resolved"
- [ ] Add some admin notes
- [ ] Click "Update Issue"
- [ ] Verify success message appears

## ☐ Step 6: Test Student View Issues

- [ ] Login as the same student from Step 4
- [ ] Navigate to: `Student/MyReportedIssues.php`
- [ ] Verify your test issue appears
- [ ] Check that admin notes are visible
- [ ] Verify status shows as "Resolved"

## ☐ Step 7: Integrate Quick Reporter (Optional but Recommended)

If you have an exam interface page (e.g., `Student/TakeExam.php` or `Student/exam-interface.php`):

- [ ] Open your exam interface file
- [ ] Find the closing `</body>` tag
- [ ] Add this code BEFORE the `</body>` tag:
```html
<!-- Technical Issue Reporter -->
<script src="exam-issue-reporter.js"></script>
<script>
    var currentExamId = <?php echo $exam_id; ?>; // Make sure $exam_id variable exists
    window.addEventListener('load', addFloatingReportButton);
</script>
```
- [ ] Save the file
- [ ] Test by starting an exam
- [ ] Verify red "Report Issue" button appears at bottom-right
- [ ] Click it and verify the popup form appears
- [ ] Submit a test issue
- [ ] Verify success message appears

## ☐ Step 8: Final Verification

- [ ] Test filtering in admin dashboard (by status, priority)
- [ ] Test search functionality in admin dashboard
- [ ] Verify statistics update correctly
- [ ] Check that priority badges show correct colors
- [ ] Verify timestamps are recorded correctly
- [ ] Test with different issue types
- [ ] Verify email addresses and student codes display correctly

## ☐ Step 9: Clean Up (Optional)

- [ ] Delete test issues from database if desired
- [ ] Review and customize priority logic if needed
- [ ] Add email notifications (see guide for instructions)
- [ ] Customize button colors/positions if desired

## 🎉 Completion

Once all checkboxes are marked:
- ✅ Your technical issue reporting system is fully operational!
- ✅ Students can report issues during exams
- ✅ Admins can manage and resolve issues
- ✅ System information is automatically captured
- ✅ Everything is tracked and logged

## 📝 Notes

Write any issues or customizations here:

_______________________________________________
_______________________________________________
_______________________________________________
_______________________________________________

## 🆘 Troubleshooting

If something doesn't work:

1. **Migration fails:** Check if columns already exist, may need to drop and recreate
2. **Page not found:** Verify file paths are correct
3. **No exams in dropdown:** Make sure student is enrolled in courses with active exams
4. **System info not captured:** Check browser console for JavaScript errors
5. **Admin can't see issues:** Verify database foreign keys are correct

For detailed troubleshooting, see: `docs/TECHNICAL_ISSUE_REPORTING_GUIDE.md`

---

**Date Completed:** _______________
**Tested By:** _______________
**Status:** ☐ Complete ☐ Needs Review
