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

### Features
1.Drag and Drop from Web.

2.Drag and Drop from Local machine

3.Crop and Resize

4.Webp conversion

5.Copy image URL and share to the Web

7.Size configurable and thumbnails generation

8.SEO friendly filename. 

9.Translation

10.PHPunit test classes included. 
<img src="https://i.ibb.co/y47HP3g/Screenshot-2020-11-07-at-11-19-13-PM.png" alt="Drag n Drop from local machine" border="0">
<img src="https://i.ibb.co/2tT1W40/Resize-image.png" alt="Resize-image" border="0">

### How to use in a Model as BelongsTo
First of all you need to put a line on your model migration for example posts.
```php
$table->foreign('photo_id')->references('id')->on('photo_photos')->onDelete('set null');
```
Secondly you need to define relation on your model. 

```php
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function photo()
    {
        return $this->belongsTo(\Photo\Models\Photo::class, 'photo_id');
    }
```
Third. Lets make upload on controller. 
```php
    /**
    * @var \Photo\Repositories\PhotoRepository
    */
    protected $photoRepository;
            
    public function __construct(PhotoRepository $photoRepository)
    {
        $this->photoRepository = $photoRepository;
    }
    
    public function store(StoreRequest $request)
    {
    //Your other code.
        $post->photo_id = $this->photoRepository->create($file, ['caption' => $data['title']])->id;
    }
```
You must resolve `\Photo\Repositories\PhotoRepository` via `__construction` Dependency injection.

Finally. Its time to render image to view. 

```blade
 {!! $post->photo->renderThumbnails() !!}
```
This will render following html code. 
```html
<picture>
    <source type="image/webp" srcset="https://YourSite.com/storage/posts/thumbnails/nice-quietly-their-belong-place-on-it-the-appeared-to.webp">
    <img src="https://YourSite.com/storage/posts/thumbnails/nice-quietly-their-belong-place-on-it-the-appeared-to.jpeg" alt="Nice, quietly their belong, place on. It the appeared to">
</picture>
```
Above code will render thumbnails in both webp and uploaded extension. 
To render larger image do following
```blade
    {!! $post->photo->render('card-img-top') !!}
```
Here render method take class name as first argument and style as second.

### How to upload file and get file path only.

```php
    namesapce App\Repositories;
    
    class PostRepository
    {
       /**
        * @var \Photo\Services\PhotoService
        */
        protected PhotoService $photoService;
        
        public function __construct(PhotoService $photoService)
        {
            $this->photoService = $photoService;
        }
        
        public function store(Request $request)
        {
            $post = new Post();
            $post->fill($request->all());
            
            $mainImageFolder = "posts"
            $thumbnailWidth = 220;
            $thumbnailHheight= 200;
            $crop="no"; // "yes" will resize image automatically based on your maximum height,width.
            $thumbnailPath = "thumbnails"; // Thumbnails path are relative to main Image folder. 
                                            //In this case it will create a folder thumbnails under posts folder.
                        
            $post->image =  $this->photoService
                                    ->setDimension($thumbnailWidth, $thumbnailHheight, $thumbnailPath)
                                    ->store($mainImageFolder, $request->file('file'), $post->title, $crop)
                                    ->convert()
                                    ->getStoredImagePath();
            $post->save();
                         
        }
             
    }
```
