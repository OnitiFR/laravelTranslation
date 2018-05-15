
## Translation ##
 
### Installation ###
 
```
    composer require oniti/translation
```

The next required step is to add the service provider to config/app.php :
```
    Oniti\Translation\TranslationServiceProvider::class,
```

Add middleware to app/Http/Kernel.php
```
    protected $middlewareGroups = [
        ...
        'api' => [
            ....,
            \Oniti\Translation\middlewares\TranslationMiddleWare::class,
        ],
    ];
```
### Publish ###
 
The last required step is to publish views and assets in your application with :
```
    php artisan vendor:publish
```

### Migrate ###
 
Migrate in order to create table:
```
    php artisan migrate
```

### Exemple ###

Configure Model

```
    <?php

    namespace App;

    use Illuminate\Database\Eloquent\Model;
    use Oniti\Translation\Traits\Translate;

    class Article extends Model
    {
        use Translate;

        protected $translate = ['libelle'];
    }

    ?>
```

Route Test 

```
     Route::get('test', function(){
         $article = Article::first();
         // fill méthode
         $article->fill(['libelle' => ['fr' => 'Machin Test update', 'en'=> 'Test Machin English update']]);
         // classic methode
         $article->libelle = ['fr' => 'Machin Test hdhdhdhdh', 'en'=> 'Test Machin English hdhdhdhdh'];
         $article->save();

         return $article;

        // Creation methode
        // $article = Article::create(['libelle' => ['fr' => 'Machin Test', 'en'=> 'Test Machin English']]);
         
     });
```