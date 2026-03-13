@echo off
echo ==========================================
echo    SMART PAKCOY HIDROPONIK - AUTO START
echo ==========================================
echo.
echo [1/4] Membersihkan cache...
php artisan view:clear > nul
php artisan cache:clear > nul
php artisan config:clear > nul

echo [2/4] Resetting Database ^& Seeding Data Dummy...
php artisan migrate:fresh --seed

echo.
echo [3/4] Menyiapkan Akun Login:
echo       Email: admin@smartpakcoy.com
echo       Password: password
echo.

echo [4/4] Membuka Browser...
start http://127.0.0.1:8000/login

echo.
echo [!] Server sedang berjalan... JANGAN TUTUP JENDELA INI.
php artisan serve --port=8000
pause
