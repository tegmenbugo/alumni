FROM php:8.3-apache

# Apache mod_rewrite modülünü aktif et (Yönlendirmeler ve .htaccess için zorunlu)
RUN a2enmod rewrite

# .htaccess dosyalarının okunabilmesi için AllowOverride All ayarını yap
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Çalışma dizini
WORKDIR /var/www/html

# Proje dosyalarını konteynere kopyala
COPY . /var/www/html/

# Gerekli dosya izinlerini ayarla
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
