FROM php:7-apache

RUN apt-get update \
	&& apt-get install -y unzip libicu-dev patch vim \
	&& docker-php-ext-install intl \
	&& a2enmod rewrite \
	&& apache2ctl graceful

# Configure Apache
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/kyjoukan/web|' /etc/apache2/sites-enabled/000-default.conf

# Install and configure Composer and Symfony
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
	&& curl -LsS https://symfony.com/installer -o /usr/local/bin/symfony && chmod a+x /usr/local/bin/symfony \
	&& symfony --ansi new /var/www/kyjoukan 2.8

WORKDIR "/var/www/kyjoukan"

# Install PHP dependencies
RUN composer require doctrine/doctrine-fixtures-bundle stof/doctrine-extensions-bundle

# Install Kyjoukan into Symfony
COPY . src/Abienvenu/KyjoukanBundle
RUN cp src/Abienvenu/KyjoukanBundle/docker/patches/config.yml app/config/config.yml \
	&& cp src/Abienvenu/KyjoukanBundle/docker/patches/routing.yml app/config/routing.yml \
	&& patch -p1 -i src/Abienvenu/KyjoukanBundle/docker/patches/AppKernel.php.diff app/AppKernel.php \
	&& patch -p1 -i src/Abienvenu/KyjoukanBundle/docker/patches/app_dev.php.diff web/app_dev.php \
	&& rm -rf src/AppBundle

# Create database and load example data
RUN app/console assets:install \
	&& mkdir data \
	&& app/console doctrine:schema:create \
	&& app/console doctrine:fixtures:load --append \
	&& chown -R www-data.www-data data \
	&& chown -R www-data.www-data app/cache \
	&& chown -R www-data.www-data app/logs
