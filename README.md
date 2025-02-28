# Etapas do Setup

1. Criação do docker-compose.yaml e Dockerfile com:
    - PHP 8.3 (fpm)
    - Nginx
    - MySQL

2. Instalação do Symfony utilizando composer dentro do container PHP
```bash
composer create-project symfony/skeleton:"7.2.x" api
```
