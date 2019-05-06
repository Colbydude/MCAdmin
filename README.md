# MCAdmin
A bare-bones web interface for running and managing a Minecraft Server.

## Requirements
- Java 8
- PHP >= 7.1.3 with the following extensions:
  - OpenSSL
  - PDO
  - Mbstring
  - Tokenizer
  - XML
  - Ctype
  - JSON
  - BCMath
- [Composer](https://getcomposer.org/)
- MySQL >= 5.7
- [tmux](https://hackernoon.com/a-gentle-introduction-to-tmux-8d784c404340)
- [NPM](https://www.npmjs.com/) or [Yarn](https://yarnpkg.com) to optionally compile assets (Yarn preferred)

## Installation
- Clone or download this repo.
- `cd` into this app's directory
- Run `composer install` to install PHP dependencies.
- Run `cp .env.example .env` to copy the default environment file.
- Run `php artisan key:generate` to set the application encryption key
- Fill out the rest of the .env file as needed.
  - Most importantly the `MINECRAFT_` and `DB_` sections.
- Run `php artisan migrate` to migrate the database.
- Run `php artisan mcadmin:user` to create a new user.
- Login to the app and boot your server!
