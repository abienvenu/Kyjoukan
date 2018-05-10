# These are a few helpers to help run and develop Kyjoukan

build:
	docker build -t abienvenu/kyjoukan ..

build-rpi:
	docker build -t abienvenu/kyjoukan -f ../Dockerfile-rpi ..

volume:
	docker volume create --name kyjoukan-data

update-schema:
	docker exec kyjoukan app/console doctrine:schema:update --dump-sql

run:
	docker run -p 8043:80 -v kyjoukan-data:/var/www/kyjoukan/data -d --name kyjoukan -e APP_ENV=prod -e APP_SECRET=$(shell head /dev/urandom | tr -dc A-Za-z0-9 | head -c 20) abienvenu/kyjoukan

run-dev:
	docker run -p 8043:80 -v kyjoukan-data:/var/www/kyjoukan/data -v $(shell pwd)/..:/var/www/kyjoukan/src/Kyjoukan -d -e APP_ENV=dev --name kyjoukan-dev abienvenu/kyjoukan

get-vendor:
	docker cp kyjoukan:/var/www/kyjoukan/vendor ../vendor
