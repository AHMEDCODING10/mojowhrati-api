# استخدام نسخة PHP مع Apache المناسبة لـ Laravel
FROM php:8.2-apache

# تثبيت الإضافات المطلوبة لـ Laravel و MySQL
# السطر الصحيح
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev
# تفعيل موديل Rewrite لـ Apache (ضروري لروابط Laravel)
RUN a2enmod rewrite

# تثبيت إضافات PHP لـ MySQL و GD
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# نسخ ملفات المشروع للمجلد الافتراضي في السيرفر
COPY . /var/www/html

# ضبط الصلاحيات لمجلدات Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# تغيير الـ Document Root ليكون مجلد public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/apache2/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# تشغيل Composer install
RUN composer install --no-dev --optimize-autoloader

# فتح المنفذ 80
EXPOSE 80

# أمر التشغيل الافتراضي لـ Apache
CMD ["apache2-foreground"]