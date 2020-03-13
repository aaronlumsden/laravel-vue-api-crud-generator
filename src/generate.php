<?php

namespace lummy\vueApi;

use Illuminate\Console\Command;
use Str;
use Storage;
use View;
use Log;
use DB;
use Artisan;

//composer dump-autoload
// To publish config file 
// php artisan vendor:publish --provider="lummy\vueApi\vueApiServiceProvider" --tag="config"

// generate Model 
// Generates a Controller (list,get,create,update,delete)
// Generates a model 
// Generates the routes
// Generates the vue.js template with axios and validation

class generate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vueapi:generate {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generated the routes, controller & Vue.js single file templates';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    public function createModel($singular, $plural){
      
      $client = Storage::createLocalDriver(['root' => config('vueApi.model_dir')]);
      
      // Check if file already exists. If it does ask if we want to overwrite
      if ($client->exists($singular)) {    
        if (!$this->confirm($singular.' model already exists. Would you like to overwrite this model?')){
            return false;    
        } 
      } 
        
      // Create the file
      $modelTemplate = view::make('vueApi::model',['singular' => $singular, 'plural' => $plural])->render();
      $modelTemplate = "<?php \n".$modelTemplate." ?>";
      $client->put($plural.'.php', $modelTemplate );

      return;
      
    }
    
  
    
    public function createController($singular, $plural){
      
      $client = Storage::createLocalDriver(['root' => config('vueApi.controller_dir')]);
      
      // Check if file already exists. If it does ask if we want to overwrite
      if ($client->exists($plural.'Controller.php')) {    
        if (!$this->confirm($plural.'Controller.php already exists. Would you like to overwrite this controller?')){
            return false;    
        } 
      } 
      
      $formData = $this->createFormData($plural);
      
      // Create the file
      $controllerTemplate = view::make('vueApi::controller',['name' => $singular,'plural'=>ucfirst($plural),'validatorString'=>$formData['validator']])->render();
      $controllerTemplate = "<?php \n".$controllerTemplate." ?>";
      $client->put($plural.'Controller.php', $controllerTemplate );

      return;
      
    }
    
    
    public function createVueListTemplate($singular,$plural){
      
      $client = Storage::createLocalDriver(['root' => config('vueApi.vue_files_dir')]);
      
      // Check if file already exists. If it does ask if we want to overwrite
      if ($client->exists($plural.'-list.vue')) {
        if (!$this->confirm($plural.'-list.vue already exists. Would you like to overwrite this component?')) {
          return false;
        }
      } 
      
     $formData = $this->createFormData($plural);
     
  
      
      //['htmlForm'=>$vform,'validator' => $validatorArray,'fields'=>$fieldsArray];
      
      // Create the file
      $vueTemplate = view::make('vueApi::vue-list',['singular' => strtolower($singular),'plural'=>strtolower($plural),'htmlForm'=>$formData['htmlForm'],'fields'=>$formData['fields']])->render();
      $client->put($plural.'-list.vue', $vueTemplate );
      
      return;
      
    }
    
    public function createVueSingleTemplate($singular, $plural){
      
      $client = Storage::createLocalDriver(['root' => config('vueApi.vue_files_dir')]);
      
      // Check if file already exists. If it does ask if we want to overwrite
      if ($client->exists($plural.'-single.vue')) {
        if (!$this->confirm($plural.'-single.vue already exists. Would you like to overwrite this component?')) {
          return false;
        }
      } 
      
     $formData = $this->createFormData($plural);
      
  
  
      // Create the file
      $vueTemplate = view::make('vueApi::vue-single',['singular' => strtolower($singular),'plural'=> strtolower($plural),'htmlForm'=>$formData['htmlForm'],'fields'=>$formData['fields']])->render();
      $client->put($plural.'-single.vue', $vueTemplate );
      
      return;
      
      
    }
    
    public function createRoutes($singular, $plural){
      
      $client = Storage::createLocalDriver(['root' => config('vueApi.routes_dir')]);
      $plural = Ucfirst(strtolower($plural));
      $routes = "\nRoute::get('".$plural."', '".$plural."Controller@list');\n";
      $routes .= "Route::get('".$plural."/{id}', '".$plural."Controller@get');\n";
      $routes .= "Route::post('".$plural."', '".$plural."Controller@create');\n";
      $routes .= "Route::put('".$plural."/{id}', '".$plural."Controller@update');\n";
      $routes .= "Route::delete('".$plural."/{id}', '".$plural."Controller@delete');\n";
      
      if ($client->exists(config('vueApi.routes_file'))) {
        $routeFile = $client->get('/'.config('vueApi.routes_file'));
        $appendedRoutes = $routeFile.$routes;
        $client->put(config('vueApi.routes_file'), $appendedRoutes);
      } else {
        $routeFile = $client->get('/'.config('vueApi.routes_file'));
        $client->put(config('vueApi.routes_file'), $routes);
      }
      
      
      
      
    }
    
    public function createFormData($plural){
      
      $validatorArray = array();
      $vform = '';
      $fieldsArray = array();
      
      $data = DB::select('DESCRIBE '.strtolower($plural));

      foreach ($data as $key) {
        

        
        $vform .= "\t\t\t\t\t<div class='form-group'>\n";
        $vform .= "\t\t\t\t\t\t<label>".$key->Field."</label>\n";
        
        $thisValidations = array();
        ($key->Null == 'NO') ? '' : array_push($thisValidations,'required');
        
        preg_match_all('!\d+!', $key->Type, $matches);
        
        
      
        $lengthValue = (isset($matches[0][0])) ? 'max:'.array_push($thisValidations,'max:'.$matches[0][0]) : '';
        
        $inputLength = (isset($matches[0][0])) ? "maxlength='".$matches[0][0]."'" : '';
        
        $fieldsArray[$key->Field] = '';
        
        
        if ($thisValidations && $key->Field !== 'id' && $key->Field !== 'created_at' && $key->Field !== 'updated_at') {
          $validatorArray[0][$key->Field] = implode('|',$thisValidations);
          
          if ($key->Null == 'YES') {
            $validatorArray[1][$key->Field.'.required'] = 'Please ensure you have filled in '.$key->Field;
          }
        }
        
        $numericArray = ['tinyiny','smallint','mediumint','int','bigint','decimal','float','double','bit'];
        
        $typeArr = explode("(", $key->Type, 2);
        $type = $typeArr[0];
        
        if (in_array($type,$numericArray)) {
          $vform.="\t\t\t\t\t\t<input type='number' ".$inputLength." placeholder='".$key->Field."' v-model='form.".$key->Field."'/>\n";
        }
        
        
        $stringArray = ['char','varchar','tinystring'];
        
        
        if (in_array($type,$stringArray)) {
          $vform.="\t\t\t\t\t\t<input type='text' ".$inputLength." placeholder='".$key->Field."' v-model='form.".$key->Field."'/>\n";
        }
        
        $textAreaArray = ['text','mediumtext','longtext'];
        
        
        if (in_array($type,$textAreaArray)) {
          
          $vform.="\t\t\t\t\t\t<textarea ".$inputLength." placeholder='".$key->Field."' v-model='form.".$key->Field."'/></textarea>\n";
        }
        
        $dateArray = ['date','timestamp','date','datetime','year'];
        
        if (in_array($type,$dateArray)) {
          $vform.="\t\t\t\t\t\t<input type='date' placeholder='".$key->Field."' v-model='form.".$key->Field."'/>\n";
        }
        
        $enumArray = ['enum'];
        
        if ($type == 'enum') {
          $values = str_replace("'",'',str_replace(')','',str_replace('enum(','',$key->Type)));
          $valuesAr = explode(',',$values);
          $vform.="\t\t\t\t\t\t<select v-model='form.".$key->Field."'>";
          foreach ($valuesAr as $key2) {
            $vform.="<option>".$key2."</option>";
          }
          
          $vform.="</select>\n";
        }
        
        if ($thisValidations) {
          $vform.="\t\t\t\t\t\t<has-error :form='form' field='".$key->Field."'></has-error>\n";
        }
        
        
        $vform .= "\t\t\t\t\t</div>\n\n";

      }
      
      
    
      
      //$validatorArray 
      //$vform 
      //$fieldsArray 
      
      return ['htmlForm'=>$vform,'validator' => $validatorArray,'fields'=>$fieldsArray];
      
      
    }
    
    
    

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      
      
        
        $singular = Str::camel($this->argument('model'));
        $singular = Ucfirst(Str::singular($singular));
        $plural = Ucfirst(Str::plural($singular));
        
        $this->createModel($singular, $plural);
        $this->createRoutes($singular, $plural);
        $this->createController($singular, $plural);
        $this->createVueListTemplate($singular, $plural);
        $this->createVueSingleTemplate($singular, $plural);
        
        return $this->info('Created '.$singular.'Controller.php, '.$singular.'.vue and the routes in '.config('vueApi.routes_file'));
    
    }
}
