# photo [![StyleCI](https://github.styleci.io/repos/188075423/shield?branch=master)](https://github.styleci.io/repos/188075423?branch=master)
Laravel Photo Manager
### Installation
**Step one**
```php
 composer require digitaldream/photo
```
**Step Two**
Run Migration
```php
 php artisan migrate
```
**Step Three**
```php
 php artisan vendor:publish --provider="Photo/PhotoServiceProvider"
```

It will publish config, views file. Feel free to edit these files according to your project need.

**Step Four**

Browse **/photo/photos** to start using this library
