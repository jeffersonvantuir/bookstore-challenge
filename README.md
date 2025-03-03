# Etapas do Setup

1. Criação do docker-compose.yaml e Dockerfile com:
    - PHP 8.3 (fpm)
    - Nginx
    - MySQL

2. Instalação do Symfony utilizando composer dentro do container PHP
```bash
composer create-project symfony/skeleton:"7.2.x" api
```
### Entidades

Para agilizar o processo de criação das entidades, foi utilizado o bundle Make do Symfony, que é utilizado via CLI.

```bash
composer require --dev symfony/maker-bundle
composer require orm
```