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

Para geração das entidades, foi utilizado o comando do symfony `bin/console make:entity`, porém, foi necessário posteriormente fazer os ajustes dos nomes das colunas nas entidades geradas.

Após a definição das entidades, foi utilizado o comando do doctrine para geração das migrations `bin/console doctrine:migration:diff` e `bin/console doctrine:migration:migrate`.

### Paginação

Para realizar a paginação foi utilizado o componente Knp Paginator.
```bash
composer require knplabs/knp-paginator-bundle
```

### PHP Unit e PHP Stan
Utilizado PHP Unit para realização dos testes unitários e PHP Stan para análise estática de código.

```bash
composer require --dev symfony/test-pack
composer require phpstan/phpstan
composer require squizlabs/php_codesniffer
```
