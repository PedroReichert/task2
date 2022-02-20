# Task2

## Installation

###### Create .env change the following attributes:

```sh
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=task2
DB_USERNAME=root
DB_PASSWORD=root
```

###### Run these commands on terminal:
```sh
git clone https://github.com/PedroReichert/task2.git
cd task2
docker-compose build app
docker-compose up -d
docker exec -it app bash
composer install 
php artisan config:clear
php artisan migrate
```
###### Run this for testing:
```sh
 ./vendor/bin/phpunit
```

