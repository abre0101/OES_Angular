<?php
/**
 * Password Helper Utility
 * Provides secure password hashing and verification functions
 */

/**
 * Hash a password using PHP's password_hash function
 * Uses bcrypt algorithm (PASSWORD_DEFAULT)
 * 
 * @param string $password The plain text password
 * @return string The hashed password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify a password against a hash
 * 
 * @param string $password The plain text password to verify
 * @param string $hash The hashed password from database
 * @return bool True if password matches, false otherwise
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check if a password needs to be rehashed
 * Useful for upgrading password hashes when algorithm changes
 * 
 * @param string $hash The current password hash
 * @return bool True if rehash is needed
 */
function needsRehash($hash) {
    return password_needs_rehash($hash, PASSWORD_DEFAULT);
}
?>
