FROM php:8.3-apache

# 1. Instalar dependências do sistema e extensões PHP essenciais
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && a2enmod rewrite

# 2. Instalar o Composer v2 (oficial)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Configurar diretório de trabalho
WORKDIR /var/www/html

# 4. Copiar os arquivos do projeto
COPY . .

# 5. Instalar dependências do PHP (ignora requisitos de plataforma para evitar conflitos de extensões no build)
RUN composer install --no-interaction --optimize-autoloader --no-dev

# 6. Configurar permissões para o Laravel (essencial para evitar erro 500)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 7. Ajustar o DocumentRoot do Apache para a pasta /public do Laravel
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 8. Expor a porta 80
EXPOSE 80

# 9. Comando para rodar as migrações, gerar swagger e subir o Apache
# Usamos um shell inline para garantir que o Laravel esteja pronto antes do Apache iniciar
CMD php artisan key:generate --force && \
    php artisan migrate --force && \
    php artisan l5-swagger:generate && \
    apache2-foreground
