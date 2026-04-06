# استخدام نسخة PHP مع Apache الرسمية
FROM php:8.2-apache

# تثبيت الإضافات الضرورية فقط
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# تفعيل مود Rewrite لـ Laravel
RUN a2enmod rewrite

# نسخ ملفات المشروع
COPY . /var/www/html

# ضبط صلاحيات المجلدات
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# تثبيت Node.js و NPM لبناء ملفات Vite (النسخة 22 مطلوبة لـ Vite 7)
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && \
    apt-get install -y nodejs

# بناء ملفات الواجهة
RUN npm install && npm run build

# سيتم إعداد منفذ Apache ليتوافق مع Railway عند التشغيل

# تعيين مجلد public كجذر للموقع
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/apache2/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# تفعيل مودات Apache اللازمة للـ WebSockets والـ Proxy
RUN a2enmod proxy proxy_http proxy_wstunnel rewrite

# تهيئة Apache ليعمل كـ Proxy لـ Reverb (للسماح بالبث المباشر على بورت 80/443)
RUN echo "<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    \n\
    # تفعيل نظام إعادة الكتابة (RewriteEngine) لمعالجة الـ WebSockets\n\
    RewriteEngine On\n\
    RewriteCond %{HTTP:Upgrade} websocket [NC]\n\
    RewriteCond %{HTTP:Connection} upgrade [NC]\n\
    RewriteRule ^/app/(.*) ws://127.0.0.1:8080/app/\$1 [P,L]\n\
    \n\
    # تمرير حركة مرور WebSockets و HTTP العادية إلى Reverb\n\
    ProxyPass /app/ ws://127.0.0.1:8080/app/\n\
    ProxyPassReverse /app/ ws://127.0.0.1:8080/app/\n\
</VirtualHost>" > /etc/apache2/sites-available/000-default.conf

# تشغيل الهجرة، تشغيل Reverb في الخلفية، ثم تشغيل Apache
CMD php artisan migrate --force && \
    php artisan reverb:start --host=0.0.0.0 --port=8080 & \
    sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf && \
    sed -i "s/VirtualHost \*:80/VirtualHost \*:$PORT/g" /etc/apache2/sites-available/000-default.conf && \
    apache2-foreground