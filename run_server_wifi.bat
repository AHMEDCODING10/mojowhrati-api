@echo off
echo ========================================================
echo   Mojawharati Backend Server (Local Wi-Fi Network Mode)
echo ========================================================
echo Computer Wi-Fi IP: 172.19.66.19
echo Server starting on http://0.0.0.0:8000 ...
echo Mobile App can connect to: http://172.19.66.19:8000
echo ========================================================
php artisan serve --host=0.0.0.0 --port=8000
pause
