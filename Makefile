up: docker-up
init: docker-down docker-pull docker-build docker-up manager-init
manager-init: manager-composer-install

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

manager-composer-install:
	docker-compose run --rm projector-php-cli composer install --quiet

cli:
	docker-compose run --rm projector-php-cli php bin/app.php

build-production:
	docker build --pull --file=manager/docker/production/nginx.docker --tag ${REGISTRY_ADDRESS}/projector-nginx:${IMAGE_TAG} manager/docker/production
	docker build --pull --file=manager/docker/production/php-fpm.docker --tag ${REGISTRY_ADDRESS}/projector-php-fpm:${IMAGE_TAG} manager/docker/production
	docker build --pull --file=manager/docker/production/php-cli.docker --tag ${REGISTRY_ADDRESS}/projector-php-cli:${IMAGE_TAG} manager/docker/production

push-production:
	docker push ${REGISTRY_ADDRESS}/projector-nginx:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/projector-php-fpm:${IMAGE_TAG}
	docker push ${REGISTRY_ADDRESS}/projector-php-cli:${IMAGE_TAG}

deploy-production:
	ssh ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'rm -rf docker-compose.yml .env'
	scp -P ${PRODUCTION_PORT} docker-compose-production.yml ${PRODUCTION_HOST}:docker-compose.yml
	ssh ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "REGISTRY_ADDRESS=${REGISTRY_ADDRESS}" >> .env'
	ssh ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'echo "IMAGE_TAG=${IMAGE_TAG}" >> .env'
	ssh ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose pull'
	ssh ${PRODUCTION_HOST} -p ${PRODUCTION_PORT} 'docker-compose up --build -d'
