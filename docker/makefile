# These are a few helpers to help run and develop Kyjoukan

build:
	docker build -t abienvenu/kyjoukan ..

volume:
	docker volume create --name kyjoukan-data

update-schema:
	docker exec kyjoukan app/console doctrine:schema:update --dump-sql

run:
	docker run -p 8043:80 -v kyjoukan-data:/var/www/html/data -d --name kyjoukan abienvenu/kyjoukan

run-dev:
	docker run -p 8043:80 -v kyjoukan-data:/var/www/html/data -v $(shell pwd)/..:/var/www/kyjoukan/src/Abienvenu/kyjoukanBundle -d --name kyjoukan abienvenu/kyjoukan

get-vendor:
	docker cp kyjoukan:/var/www/kyjoukan/vendor ../vendor
