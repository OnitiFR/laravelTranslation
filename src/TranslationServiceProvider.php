<?php 

namespace Oniti\Translation;

use Illuminate\Support\ServiceProvider;
use App;
use Illuminate\Foundation\AliasLoader;

class TranslationServiceProvider extends ServiceProvider{
 
 
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
 
    public function boot(){
        $loader = AliasLoader::getInstance();
        $loader->alias('Translation', \Oniti\Translation\TranslationFacade::class);

        $this->publishes([
            __DIR__.'/config' => base_path('config'),
            __DIR__.'/migrations' => base_path('database/migrations')
        ]);
    }
 
    public function register() {
        App::bind('Translation', function()
        {
            return new \Oniti\Translation\Translation;
        });
    }
 
}