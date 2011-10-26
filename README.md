# Idea Marketplace

## Intro

The Marketplace is where KNP ninjas share ideas, projects and stuff. It's built on top of [Silex](http://silex.sensiolabs.org/).

## configuration

### Vendors:

```
bin/vendors install
```

### create and edit your config

```
cp src/config.php.dist src/config.php
```

### create a cache directory

```
mkdir cache ; chmod 777 !$
```

You're set!

## About the Marketplace

### Vendors

The Marketplace uses a patched version of Symfony2's `bin/vendors`. Not much to say here, except it makes life easier.

### Authentification

The Marketplace uses Google OpenID for identification, and a custom, dead simple, homebrew authentication system consisting in a simple pimple service, which return value determines whether you can access the app (`true`) or not (`false`). On the KnpLabs instance of the Marketplace, this service's definition looks like that:

    $app['auth']  = $app->share(function() use ($app) {
        return function($username) use ($app) {
            return (bool) preg_match('/@knplabs\.com$/', $username);
        };
    });

Like I said, dead-simple.

### Markdown

The Marketplace uses the [SilexDiscountServiceProvider](https://github.com/geoffrey/SilexDiscountServiceProvider) to provide markdown support. This service provider requires that you set the `markdown.discount.bin` parameter in your configuration file.

### Twig extension

There is a Marketplace Twig extension in `src/Marketplace/Twig` to provide a few specific filters and gravatar support.

### Project categories

Project categories are stored as a pimple parameter, `project.categories`, in `src/config.php`. See `src/config.php.dist` for an example.

### Data repositories

Even though we don't use an ORM in the Marketplace, this is no reason to put your `SQL` *en vrac* inside your controllers. That is why we implemented a very simple way to store data retrieveing logic, in the form of repository classes, that you will find in `src/Repository`. Since we try to avoid magic as much as possible, those repositories need to be declared to the `RepositoryServiceProvider`, inside the bootstrap (`src/bootstrap.php`).

### Migrations

The Marketplace features a simple homebrew schema migration system. Since there is no CLI system in Silex, everything takes place during the `before` filter. A migration consist of a single file, holding a migration class. By design, the migration file must be named something like `<version>_<migration_name>Migration.php` and located in `src/Resources/migrations`, and the class `<migration_name>Migration`. For example, if your migration adds a `bar` field to the `foo` table, and is the 5th migration of your schema, you should name your file `05_FooBarMigration.php`, and the class would be named `FooBarMigration`.

In addition to these naming conventions, your migration class must extends `Marketplace\AbstractMigration`, which provides a few helping method such as `getVersion` and default implementations for migration methods.

The migration methods consist of 4 methods:

* `schemaUp`
* `schemaDown`
* `appUp`
* `appDown`

The names are pretty self-explanatory. Each `schema*` method is fed a `Doctrine\DBAL\Schema\Schema` instance of which you're expected to work to add, remove or modify fields and/or tables. The `app*` method are given a `Silex\Application` instance, actually your very application. You can see an example of useful `appUp` migration in the [CommentMarkdownCacheMigration](https://github.com/knplabs/marketplace/blob/master/src/Resources/migrations/04_CommentMarkdownCacheMigration.php).

There's one last method you should know about: `getMigrationInfo`. This method should return a self-explanatory description of the migration (it is optional though, and you can skip its implementation).