# Laravl Photo Manager 
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

### Configure Policy
You can configure who can have what permissions on this photo library.
 Create a new class and extends it from `Photo\Policies\PhotoPolicy` like below.
 ```php
  namespace App\Policies;
  
  use Photo\Policies\PhotoPolicy as Policy;

   class PhotoPolicy extends Policy
   {
       /**
        * @param \App\Models\User $user
        *
        * @return bool
        */
       public function viewAny($user): bool
       {
           return $user->isTeacher();
       }
   }
```
As you can see we override viewAny method. Now a Teacher can view list of all photos.
Other methods like `before`,`view`,`create`,`update`,`delete` can be override too.
Now to register this Policy class lets change `policy` key on  `config/photo.php`

    #file config/photo.php
    
    'policy' => \App\Policies\PhotoPolicy::class,

###Features
1.Drag and Drop from Web.

2.Drag and Drop from Local machine

3.Crop and Resize

4.Webp conversion

5.Copy image URL and share to the Web

7.Size configurable and thumbnails generation

8.SEO friendly filename. 

9.Translation

10.PHPunit test classes included. 

