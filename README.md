## Overview
This is a Yii 2‑based web application serving as Short Link Service + QR.
=====================

## Technology Stack
[Composer](https://getcomposer.org/)
[Yii 2](https://www.yiiframework.com/)
[docker](https://www.docker.com/)
[PHP: 8.4.*](https://www.php.net/releases/8.4/en.php)
[Database: MySQL 9.6.0](https://mysql.com)


INSTALLATION
------------

### Clone the repository:
```
git clone https://github.com/sevarostov/short-link-service.git
```

### Install dependencies:
```
composer install
```
### Configure environment:
```
cp .env.example .env.dev
```
### Edit .env.dev with your credentials

### Start the containers with Docker
```
    docker compose up -d
```

### Run migrations
```
    docker compose run --rm php yii migrate
```


### Visit the site
http://localhost:8000

