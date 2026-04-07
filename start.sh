#!/bin/sh

# تنفيذ الهجرات وتجهيز الروابط
php artisan migrate --force
php artisan storage:link

# تأمين توافق Apache مع بورت راندر (مهم جداً)
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/VirtualHost \*:80/VirtualHost \*:$PORT/g" /etc/apache2/sites-available/000-default.conf

# تشغيل محرك Laravel Reverb في الخلفية مع تسجيل سجلاته للتصحيح
echo "Starting Laravel Reverb..."
nohup php artisan reverb:start --host=0.0.0.0 --port=8080 > storage/logs/reverb.log 2>&1 &

# تشغيل موظف الإشعارات في الخلفية
echo "Starting Laravel Queue worker..."
nohup php artisan queue:work --tries=3 --timeout=90 > storage/logs/queue.log 2>&1 &

# الانتظار قليلاً للتأكد من إقلاع الخدمات الخلفية
sleep 2

# تشغيل Apache في الواجهة الأمامية لإبقاء الحاوية تعمل
echo "Starting Apache..."
apache2-foreground
