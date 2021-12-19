# project starter

This is a blank [Laravel](https://laravel.com) project that will connect to any prismic.io repository. It uses the prismic.io PHP development kit, and provides a few helpers to integrate with a Laravel website.

## Getting started

### Launch the starter project

*(Assuming you've [installed Laravel](https://laravel.com/docs/8.0/installation))*

Fork this repository, then clone your fork, and run this in your newly created directory:

``` bash
composer install
```

Next you need to make a copy of the `.env.example` file and rename it to `.env` inside your project root.

Run the following command to generate your app key:

```
php artisan key:generate
```

Then start your server:

```
php artisan serve
