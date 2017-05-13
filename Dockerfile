FROM php:7-apache

RUN apt-get update \
	&& apt-get install -y unzip libicu-dev patch vim \
	&& docker-php-ext-install intl \
	&& a2enmod rewrite \
	&& apache2ctl graceful

# Install and configure Composer and Symfony
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
	&& curl -LsS https://symfony.com/installer -o /usr/local/bin/symfony && chmod a+x /usr/local/bin/symfony \
	&& symfony --ansi new /var/www/html 2.8

WORKDIR "/var/www/html"

# Install Kyjoukan into Symfony
COPY . src/Abienvenu/Kyjoukan
RUN composer require doctrine/doctrine-fixtures-bundle stof/doctrine-extensions-bundle \
	&& cp src/Abienvenu/KyjoukanBundle/docker/patches/config.yml app/config/config.yml \
	&& cp src/Abienvenu/KyjoukanBundle/docker/patches/routing.yml app/config/routing.yml \
	&& rm -rf src/AppBundle

# Create database and load example data
RUN app/console assets:install \
	&& mkdir data \
	&& app/console doctrine:schema:create \
	&& app/console doctrine:fixtures:load --append \
	&& chown -R www-data.www-data data \
	&& chown -R www-data.www-data app/cache \
	&& chown -R www-data.www-data app/logs
