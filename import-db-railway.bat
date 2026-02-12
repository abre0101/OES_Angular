@echo off
echo ========================================
echo Railway Database Import Script
echo ========================================
echo.
echo This will import the database directly to Railway MySQL
echo.

railway run mysql -h yamanote.proxy.rlwy.net -P 25317 -u root -pWVfbKCqYyoVFszxfuaEgmGkTdSkxaLWk railway < database/oes_professional.sql

echo.
echo ========================================
echo Database import completed!
echo ========================================
echo.
echo Your application is available at:
echo https://web-production-08e8e.up.railway.app
echo.
pause
