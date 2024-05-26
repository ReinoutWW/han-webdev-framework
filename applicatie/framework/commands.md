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
- docker exec -it 6e008569d33b sh -c "cd framework && vendor/bin/phpunit tests --colors"
- docker exec -it 6e008569d33b sh -c "cd framework && composer dump-autoload"




docker exec -it 6e008569d33b sh -c "cd framework && composer dump-autoload --dev"
docker exec -it 6e008569d33b sh -c "cd framework && vendor/bin/phpunit tests --colors --filter services_can_be_recursively_autowired"