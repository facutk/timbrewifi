# timbrewifi

https://docs.google.com/document/d/1VAGbpbPSKK6W7qm5cIGIxE_L4cYcMTsOJ_MftIHxY6Q/edit#

https://docs.google.com/spreadsheets/d/1WAJFt9ZyZiy9j5QowcxOFD5H1xpNmHd2iSWrZR9Q3vc/edit#gid=0

## telegram bot
- https://api.telegram.org/bot<TOKEN>/setwebhook?url=https://94fac19e0698.ngrok.io/api/telegramWebhook.php

A barebones PHP app that makes use of the [Silex](http://silex.sensiolabs.org/) web framework, which can easily be deployed to Heroku.

This application supports the [Getting Started with PHP on Heroku](https://devcenter.heroku.com/articles/getting-started-with-php) article - check it out.

## Local Running

```sh
php -t wp -S localhost:8080
```

## Deploying

Install the [Heroku Toolbelt](https://toolbelt.heroku.com/).

```sh
$ git clone git@github.com:heroku/php-getting-started.git # or clone your own fork
$ cd php-getting-started
$ heroku create
$ git push heroku master
$ heroku open
```

or

[![Deploy to Heroku](https://www.herokucdn.com/deploy/button.png)](https://heroku.com/deploy)

## Documentation

For more information about using PHP on Heroku, see these Dev Center articles:

- [Getting Started with PHP on Heroku](https://devcenter.heroku.com/articles/getting-started-with-php)
- [PHP on Heroku](https://devcenter.heroku.com/categories/php)
