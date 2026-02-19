FROM php:8.2-apache

# نسخ ملفات المشروع
COPY . /var/www/html/

# تفعيل mod_rewrite لو محتاجة
RUN a2enmod rewrite
