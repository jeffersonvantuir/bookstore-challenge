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
### Validações
Algumas validações foram feitas de forma manual via PHP, utilizando `empty` e `strlen`. Porém, nas validações dos campos referente ao Livro (`AbstractBookValueObject`), utilizei o `Validator` do próprio Symfony para deixar o código mais legível.

```bash
composer require symfony/validator
```

### Relatório
Para geração do relatório, utilizei o componente Snappy, utilizado comumente em conjunto com o Symfony.
```bash
composer require knplabs/knp-snappy-bundle
```

Para simplificar a implementação e utilizar o compontente no qual tenho mais domínio, optei por utilizar a renderização do Twig do próprio Symfony. Para isso, tive que instalar as dependências abaixo.

```bash
composer require symfony/twig-bundle
```

## Front End
Para agilizar o processo de criação do frontend, optei por utilizar o Twig em conjunto com o Bootstrap 5.
Desta forma, o front end acaba ficando dentro do projeto Symfony, porém todas as chamadas acontecem a partir do Javascript/Jquery.

Para as máscaras de valor, utilizei a biblioteca Jquery Mask (https://igorescobar.github.io/).

Para as listagens, optei por utilizar o Datatables.