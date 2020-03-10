<?php

namespace lummy\vueApi;

use Illuminate\Support\ServiceProvider;

class vueApiServiceProvider extends ServiceProvider
{
  
  protected $commands = [
      'lummy\vueApi\generate'
  ];
  
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
      
      $this->mergeConfigFrom(
          __DIR__ . '/config/vueApi.php', 'vueApi'
      );
      
        $this->app->make('lummy\vueApi\AaronsController');
        $this->commands($this->commands);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/routes.php';
        
         $this->loadViewsFrom(__DIR__.'/templates', 'vueApi');
        
        $this->publishes([
             __DIR__ . '/config/vueApi.php' => config_path('vueApi.php'),
         ], 'config');
    }
    
    
    
}
