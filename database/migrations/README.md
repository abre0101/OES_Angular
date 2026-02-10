# Database Migrations

## How to Run Migrations

### Option 1: Using phpMyAdmin
1. Open phpMyAdmin in your browser
2. Select the `oes_professional` database
3. Click on the "SQL" tab
4. Copy and paste the contents of `enhance_technical_issues.sql`
5. Click "Go" to execute

### Option 2: Using MySQL Command Line
```bash
mysql -u root -p oes_professional < enhance_technical_issues.sql
```

### Option 3: Using the Web Interface
Navigate to: `http://your-domain/run-migration.php?file=enhance_technical_issues`

## Available Migrations

### enhance_technical_issues.sql
Enhances the technical_issues table with:
- Issue type categorization
- Browser and OS information capture
- Screen resolution tracking
- IP address logging
- Screenshot path support
- Admin notes field
- Resolution tracking (resolved_by, resolved_at)
- Priority levels (low, medium, high, critical)
- Performance indexes

**Status:** Ready to run
**Required:** Yes (for technical issue reporting feature)
