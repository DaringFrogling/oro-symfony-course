COMPOSE_DIR = './devops'

up:
	cd $(COMPOSE_DIR) && docker-compose up -d

kill:
	cd $(COMPOSE_DIR) && docker-compose kill

rr:
	make kill
	make up

logs:
	cd $(COMPOSE_DIR) && docker-compose logs -f --tail 100 oro.symfony-demo-app.php

exec-php:
	cd $(COMPOSE_DIR) && docker-compose exec oro.symfony-demo-app.php bash

exec-nginx:
	cd $(COMPOSE_DIR) && docker-compose exec oro.symfony-demo-app.nginx bash

rebuild:
	make kill
	cd $(COMPOSE_DIR) && docker-compose build
	cd $(COMPOSE_DIR) && docker-compose up