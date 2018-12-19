ToDoList
========

Task management app.

## Installing
Cloning the project repository :
```php
$ git clone https://github.com/Massetho/ToDoList
```

Edit `.env.dist` by adding your **admin mail** in the parameters. Then rename the file `.env`.

Getting all dependencies :
```php
$ composer install
```

Configure a *DATABASE_URL* constant, at the web server level, as explained [here](https://symfony.com/doc/current/configuration/external_parameters.html#configuring-environment-variables-in-production). Or edit the `.env` file and insert your database address :
```dotenv
DATABASE_URL=mysql://[db_user]:[password]@[IPaddress]:[port]/[db_name]
```

Set up your database :
```php
$ php bin/console make:migration
$ php bin/console doctrine:migrations:migrate
```
 
## Built with
  - [Symfony](https://symfony.com/) - PHP framework
  - [Composer](https://getcomposer.org/) - Dependency management

## Original Repository
  https://github.com/saro0h/projet8-TodoList