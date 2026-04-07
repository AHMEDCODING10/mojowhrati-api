@echo off
cd /d C:\Users\MC\Desktop\mo_V_24\backend
git add -A
git commit -m "Cleanup: Remove unused scripts and temporary files"
git push origin main
echo.
echo === Final Cleanup Pushed ===
