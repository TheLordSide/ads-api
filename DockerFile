# Utilise une image officielle PHP avec Apache
FROM php:8.2-apache

# Active les modules Apache et installe les extensions PHP nécessaires
RUN apt-get update && apt-get install -y \
    unzip zip curl libzip-dev git && \
    docker-php-ext-install pdo pdo_mysql zip && \
    a2enmod rewrite

# Installe Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copie tous les fichiers dans le conteneur
COPY . /var/www/html

# Définit le répertoire de travail
WORKDIR /var/www/html

# Change le DocumentRoot vers le dossier public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Installe les dépendances PHP sans les paquets de dev
RUN composer install --no-dev --optimize-autoloader

# Donne les bons droits à Apache
RUN chown -R www-data:www-data /var/www/html

# Expose le port utilisé par Apache
EXPOSE 80