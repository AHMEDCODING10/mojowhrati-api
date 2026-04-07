@echo off
cd /d C:\Users\MC\Desktop\mo_V_24\backend
git add -A
git commit -m "Fix: Admin WebSocket config + Remove 8 unused debug scripts"
git push origin main
echo.
echo === Done ===
