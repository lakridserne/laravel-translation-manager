## Laravel 5 Translation Manager

This is a translation manager, that can both replace the current translation manger,
that comes with laravel, so that it runs of a datanbase, or it can be used to export
translation files, so they can be checked into GIT.

As of now, there is no webinterface for this, but it is comming.

## Installation

Require this package in your composer.json and run composer update (or run `composer require lakridserne/laravel-translation-manager` directly):

    "lakridserne/laravel-translation-manager": "^1"

After updating composer, add the ServiceProvider to the providers array in config/app.php

    'Lakridserne\TranslationManager\ManagerServiceProvider',

You need to run the migrations for this package.

    $ php artisan vendor:publish --provider="Lakridserne\TranslationManager\ManagerServiceProvider" --tag=migrations
    $ php artisan migrate

You need to publish the config file for this package. This will add the file `config/translation-manager.php`, where you can configure this package.

    $ php artisan vendor:publish --provider="Lakridserne\TranslationManager\ManagerServiceProvider" --tag=config

### Laravel >= 5.2

The configuration file by default only includes the `auth` middleware, but the latests changes in Laravel 5.2 makes it that session variables are only accessible when your route includes the `web` middleware. In order to make this package work on Laravel 5.2, you will have to change the route/middleware setting from the default 

```
    'route' => [
        'prefix' => 'translations',
        'middleware' => 'auth',
    ],
```

to

```
    'route' => [
        'prefix' => 'translations',
        'middleware' => [
	        'web',
	        'auth',
		],
    ],
```

**NOTE:** *This is only needed in Laravel 5.2 (and up!)*

## Usage

### Web interface

To use this translation manager, you need to make your own webinterface. Though a webinterface will come soon.

### Import command

The import command will search through app/lang and load all strings in the database, so you can easily manage them.

    $ php artisan translations:import

Translation strings from app/lang/locale.json files will be imported to the __json_ group.
    
Note: By default, only new strings are added. Translations already in the DB are kept the same. If you want to replace all values with the ones from the files, 
add the `--replace` (or `-R`) option: `php artisan translations:import --replace`

### Find translations in source

The Find command/button will look search for all php/twig files in the app directory, to see if they contain translation functions, and will try to extract the group/item names.
The found keys will be added to the database, so they can be easily translated.
This can be done through the webinterface, or via an Artisan command.

    $ php artisan translations:find
    
If your project uses translation strings as keys, these will be stored into then __json_ group. 

### Export command

The export command will write the contents of the database back to app/lang php files.
This will overwrite existing translations and remove all comments, so make sure to backup your data before using.
Supply the group name to define which groups you want to publish.

    $ php artisan translations:export <group>

For example, `php artisan translations:export reminders` when you have 2 locales (en/nl), will write to `app/lang/en/reminders.php` and `app/lang/nl/reminders.php`

To export translation strings as keys to JSON files , use the `--json` (or `-J`) option: `php artisan translations:import --json`. This will import every entries from the __json_ group.

### Clean command

The clean command will search for all translation that are NULL and delete them, so your interface is a bit cleaner. Note: empty translations are never exported.

    $ php artisan translations:clean

### Reset command

The reset command simply clears all translation in the database, so you can start fresh (by a new import). Make sure to export your work if needed before doing this.

    $ php artisan translations:reset



### Detect missing translations

Most translations can be found by using the Find command (see above), but in case you have dynamic keys (variables/automatic forms etc), it can be helpful to 'listen' to the missing translations.
To detect missing translations, we can swap the Laravel TranslationServiceProvider with a custom provider.
In your config/app.php, comment out the original TranslationServiceProvider and add the one from this package:

    //'Illuminate\Translation\TranslationServiceProvider',
    'Lakridserne\TranslationManager\TranslationServiceProvider',

This will extend the Translator and will create a new database entry, whenever a key is not found, so you have to visit the pages that use them.
This way it shows up in the webinterface and can be edited and later exported.
You shouldn't use this in production, just in development to translate your views, then just switch back.

## TODO

This package is still in development, but will go out of beta, when the webinterface is done.

    - Add a webinterface
    - Seed existing languages (https://github.com/caouecs/Laravel-lang)
    - Suggestions are welcome :)
