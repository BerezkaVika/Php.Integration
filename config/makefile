IMAGE :=a application-backend

serve: up
	composer run --timeout=0 serve

up: install build
	docker-compose up -d

install: 
	composer install

	composer development-enable

build: 
	docker build -t ${IMAGE} .