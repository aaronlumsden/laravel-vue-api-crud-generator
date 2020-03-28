<?php

namespace lummy\vueApi;

use Illuminate\Console\Command;
use Str;
use Storage;
use View;
use DB;
use Artisan;


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
    
    public function createModel($data){
      
      $client = Storage::createLocalDriver(['root' => config('vueApi.model_dir')]);
      
      // Check if file already exists. If it does ask if we want to overwrite
      if ($client->exists($data['singular'])) {    
        if (!$this->confirm($data['singular'].' model already exists. Would you like to overwrite this model?')){
            return false;    
        } 
      } 
        
      // Create the file
      $modelTemplate = view::make('vueApi::model',['data' => $data])->render();
      $modelTemplate = "<?php \n".$modelTemplate." ?>";
      $client->put($data['plural'].'.php', $modelTemplate );

      return;
      
    }
    
  
    
    public function createController($data){
      
      $client = Storage::createLocalDriver(['root' => config('vueApi.controller_dir')]);
      
      // Check if file already exists. If it does ask if we want to overwrite
      if ($client->exists($data['plural'].'Controller.php')) {    
        if (!$this->confirm($data['plural'].'Controller.php already exists. Would you like to overwrite this controller?')){
            return false;    
        } 
      } 
    
      
      // Create the file
      $controllerTemplate = view::make('vueApi::controller',['data' => $data])->render();
      $controllerTemplate = "<?php \n".$controllerTemplate." ?>";
      $client->put($data['plural'].'Controller.php', $controllerTemplate );

      return;
      
    }
    
    
    public function createVueListTemplate($data){
      
      $client = Storage::createLocalDriver(['root' => config('vueApi.vue_files_dir')]);
      
      // Check if file already exists. If it does ask if we want to overwrite
      if ($client->exists($data['plural'].'-list.vue')) {
        if (!$this->confirm($data['plural'].'-list.vue already exists. Would you like to overwrite this component?')) {
          return false;
        }
      } 

      // Create the file
      $vueTemplate = view::make('vueApi::vue-list',['data' => $data])->render();
      $client->put($data['plural'].'-list.vue', $vueTemplate );
      
      return;
      
    }
    
    public function createVueSingleTemplate($data){
      
      $client = Storage::createLocalDriver(['root' => config('vueApi.vue_files_dir')]);
      
      // Check if file already exists. If it does ask if we want to overwrite
      if ($client->exists($data['plural'].'-single.vue')) {
        if (!$this->confirm($data['plural'].'-single.vue already exists. Would you like to overwrite this component?')) {
          return false;
        }
      }   
  
      // Create the file
      $vueTemplate = view::make('vueApi::vue-single',['data' =>$data])->render();
      $client->put($data['plural'].'-single.vue', $vueTemplate );
      
      return;
      
      
    }
    
    public function createRoutes($data){
      
      $client = Storage::createLocalDriver(['root' => config('vueApi.routes_dir')]);
      
      $routes = "\nRoute::get('".$data['plural_lower']."', '".$data['plural']."Controller@list');\n";
      $routes .= "Route::get('".$data['plural_lower']."/{id}', '".$data['plural']."Controller@get');\n";
      $routes .= "Route::post('".$data['plural_lower']."', '".$data['plural']."Controller@create');\n";
      $routes .= "Route::put('".$data['plural_lower']."/{id}', '".$data['plural']."Controller@update');\n";
      $routes .= "Route::delete('".$data['plural_lower']."/{id}', '".$data['plural']."Controller@delete');\n";
      
      if ($client->exists(config('vueApi.routes_file'))) {
        $routeFile = $client->get('/'.config('vueApi.routes_file'));
        $appendedRoutes = $routeFile.$routes;
        $client->put(config('vueApi.routes_file'), $appendedRoutes);
      } else {
        $routeFile = $client->get('/'.config('vueApi.routes_file'));
        $client->put(config('vueApi.routes_file'), $routes);
      }
      
      
      
      
    }
    
    public function getFieldsData($singular, $plural){
      
      $data = DB::select('DESCRIBE '.strtolower($plural));
      
      
      $fieldsArray = array();
      $i = 0;
      foreach ($data as $key) {
          
          // Extract if its required
          $required = ($key->Null == 'NO') ? true : false ;
          
          //Extract the field type
          $type = $typeArr = explode("(", $key->Type, 2)[0];

          //extract the number for the max attribute
          preg_match_all('!\d+!', $key->Type, $matches);
          
          // Setup simplified type arrays
          $stringArray = ['char','varchar','tinystring'];
          $textAreaArray = ['text','mediumtext','longtext'];
          $simplifiedType = 'number';
          
          if (in_array($type, $stringArray)) {
            $simplifiedType = 'text';
          } else if(in_array($type, $textAreaArray)){
            $simplifiedType = 'textarea';
          }
          
          $fieldsArray[$i]['name'] = $key->Field;
          $fieldsArray[$i]['type'] = $type;
          $fieldsArray[$i]['simplified_type'] = $simplifiedType;
          $fieldsArray[$i]['required'] = $required;
          $fieldsArray[$i]['max'] = (isset($matches[0][0])) ? (int)$matches[0][0] : false;
        
        $i++;
      };
      
      return array(
        'singular' => $singular,
        'plural' => $plural,
        'singular_lower' => strtolower($singular),
        'plural_lower' => strtolower($plural),
        'fields' => $fieldsArray
      );
      
      
      
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
      
      
        
        $singular = strtolower(Str::camel($this->argument('model')));
        $singular = Ucfirst(Str::singular($singular));
        $plural = Ucfirst(Str::plural($singular));
        
        $data = $this->getFieldsData($singular, $plural);
        
    
        $this->createRoutes($data);
        $this->createModel($data);
        $this->createController($data);
        $this->createVueListTemplate($data);
        $this->createVueSingleTemplate($data);
        
        return $this->info('Created '.$singular.'Controller.php, '.$singular.'.vue and the routes in '.config('vueApi.routes_file'));
    
    }
}
