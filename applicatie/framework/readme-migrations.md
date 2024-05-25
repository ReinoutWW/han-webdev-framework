# Migrations guide
In this project we've created our own migrations system that tracks the migrations that have been completed, and the onces that have yet to be processed. Here are the commands you should know

## Commands
Help:
- docker compose exec web_server php bin/console database:migrations:migrate 
- docker compose exec web_server php bin/console database:migrations:rollback 
