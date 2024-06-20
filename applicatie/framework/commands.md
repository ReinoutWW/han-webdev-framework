# Handy commands while programming

## Get into a container
Usefull commands:
- docker exec -it <ContainerName> sh -c "cd <Directory ? optional> && composer <Composer command>"
- docker ps
- vendor/bin/phpunit tests --colors
- docker compose exec web_server composer update
- docker compose exec web_server composer dump-autoload

## Docker containers
- docker-compose up -d
- docker-compose down
- docker-compose build

## Framework testing
- docker exec -it {container id} sh -c "cd framework && vendor/bin/phpunit tests --colors"
- docker exec -it {container id} sh -c "composer dump-autoload && cd framework && composer dump-autoload"

### Full autoload for framework + repo
- docker exec -it {container id} sh -c "composer dump-autoload --dev && composer dump-autoload && cd framework && composer dump-autoload --dev && composer dump-autoload"


docker exec -it {container id} sh -c "cd framework && composer dump-autoload --dev"
docker exec -it {container id} sh -c "cd framework && vendor/bin/phpunit tests --colors"
docker exec -it {container id} sh -c "cd framework && vendor/bin/phpunit tests --colors --filter services_can_be_recursively_autowired"