# Password Encryption Implementation Guide

## Overview
This system now uses **bcrypt password hashing** for secure password storage. All passwords are encrypted before being stored in the database, and verified securely during login.

## What Changed

### 1. Password Helper Utility (`utils/password_helper.php`)
A new utility file provides three functions:
- `hashPassword($password)` - Hashes a plain text password
- `verifyPassword($password, $hash)` - Verifies a password against a hash
- `needsRehash($hash)` - Checks if a hash needs updating

### 2. Updated Files

#### Login System (`auth/login.php`)
- Now uses `verifyPassword()` to check passwords
- Queries database by username only, then verifies password
- Works for all user types: Students, Instructors, Exam Committee, Administrators

#### User Creation
- `Admin/InsertStudent.php` - Hashes passwords before inserting
- `Admin/InsertInstructor.php` - Hashes passwords before inserting
- `Admin/InsertECommittee.php` - Hashes passwords before inserting

#### Password Reset (`Admin/ResetPassword.php`)
- Hashes new passwords before updating
- Hashes temporary passwords for approved requests

## Migration Required

### For Existing Databases
If you have existing users with plain text passwords, you MUST run the migration script:

```bash
# Option 1: Command line
php utils/migrate_passwords.php

# Option 2: Browser (if enabled)
http://yoursite.com/utils/migrate_passwords.php
```

**IMPORTANT:** 
- Run this script ONCE after implementing password encryption
- Delete or move the migration script after running for security
- The script is safe to run multiple times (it checks if passwords are already hashed)

## Security Benefits

1. **No Plain Text Storage** - Passwords are never stored in readable form
2. **One-Way Encryption** - Passwords cannot be decrypted, only verified
3. **Bcrypt Algorithm** - Industry-standard, resistant to brute force attacks
4. **Automatic Salt** - Each password gets a unique salt
5. **Future-Proof** - Easy to upgrade to stronger algorithms if needed

## For Developers

### Creating New Users
```php
require_once(__DIR__ . '/utils/password_helper.php');

$plainPassword = $_POST['password'];
$hashedPassword = hashPassword($plainPassword);

// Store $hashedPassword in database
$stmt = $con->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashedPassword);
```

### Verifying Passwords
```php
require_once(__DIR__ . '/utils/password_helper.php');

// Get user from database
$stmt = $con->prepare("SELECT password FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Verify password
if (verifyPassword($plainPassword, $user['password'])) {
    // Password is correct
} else {
    // Password is incorrect
}
```

### Resetting Passwords
```php
require_once(__DIR__ . '/utils/password_helper.php');

$newPassword = $_POST['new_password'];
$hashedPassword = hashPassword($newPassword);

$stmt = $con->prepare("UPDATE users SET password = ? WHERE user_id = ?");
$stmt->bind_param("ss", $hashedPassword, $userId);
```

## Testing

After implementation:
1. Try logging in with existing users (after running migration)
2. Create a new user and verify login works
3. Reset a password and verify the new password works
4. Verify old passwords no longer work after reset

## Troubleshooting

### "Wrong Username or Password" after migration
- Ensure migration script ran successfully
- Check database - passwords should start with `$2y$`
- Verify `password_helper.php` is being included in login files

### New users can't login
- Verify `password_helper.php` is included in insert files
- Check that `hashPassword()` is being called before database insert
- Ensure password field in database is VARCHAR(255) or larger

### Migration script errors
- Check database connection in `Connections/OES.php`
- Verify table and column names match your database
- Ensure PHP has write permissions to database

## Database Requirements

Password columns should be:
- Type: `VARCHAR(255)` or `TEXT`
- Bcrypt hashes are 60 characters, but VARCHAR(255) allows for future algorithms

## Additional Security Recommendations

1. **Enforce Strong Passwords** - Minimum 8 characters, mix of letters/numbers/symbols
2. **Password Expiry** - Consider requiring password changes every 90 days
3. **Account Lockout** - Lock accounts after multiple failed login attempts
4. **Two-Factor Authentication** - Add 2FA for additional security
5. **HTTPS Only** - Always use HTTPS to prevent password interception

## Support

If you encounter issues:
1. Check the error logs
2. Verify all files were updated correctly
3. Ensure migration script was run
4. Test with a new user account first
