## Laravel Vue API Crud Generator

### Installation
`composer require lummy/laravel-vue-api-crud-generator`

#### Overview
I created this package because I was tired of doing the same things over and over again. Each time I created a new model on a Laravel/Vue project I would create:

- A Model
- A Controller
- Add the routes 
- Create a Vue.js single file component to list all the items for my model 
- Create a Vue.js single file component to list a single item
- Create a form in Vue.js to communicate with my Laravel api

This package aims to speed up this process by creating all of the above in a single artisan command.


## Usage

Firstly you should create a new migration in the sam way that you would usally. For example if creating a posts table use the command 

`php artisan make:migration create_posts_table`

Then in your migration file add your fields as usual

```
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title',200);
    $table->text('content')->nullable();
    $table->timestamps();
});
```

Then run the migrate command to create the posts table 

`php artisan migrate`

Once you have done that you just need to run one command. Add the name of your table to the end of the command so in this case it's posts.

`php artisan vueapi:generate posts`

This will then generate a basic skeleton for a CRUD app in both Laravel & Vue.js single file templates.


Once you have run this command, using the `posts` example above, it will create the following skeleton files:

### Routes 

```
Route::get('posts', 'PostsController@list');
Route::get('posts/{id}', 'PostsController@get');
Route::post('posts', 'PostsController@create');
Route::put('posts/{id}', 'PostsController@update');
Route::delete('posts/{id}', 'PostsController@delete');

```

### Controller
```
<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;

class Post extends Controller
{
    public function get(Request $request, $id){
      return Post::findOrFail($id);
    }
    
    public function list(Request $request){
      return Post::get();
    }
    
    public function create(Request $request){
        $input = $request->all();
        $Post = Post::create($input)->save();
        return $Post;
    }
    
    public function update(Request $request, $id){
        $Post = Post::findOrFail($id);
        $input = $request->all();
        $Post->fill($input)->save();
        return $Post;
    }
    
    public function delete(Request $request, $id){
        $Post = Post::findOrFail($id);
        $Post->destroy();
    }
}
 ?>

```
### Model 

```
<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;

class Songs extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var  array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var  array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var  array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
} ?>

```

### Vue (List template)

```

<template lang="html">
      <div class="posts">
        
        <h1>Get Posts</h1>
        
        <ul v-if="Posts">
          <li v-for="(Post,index) in Posts" :key="Post.id">
            
            Post
            <a @click.prevent="deletePost(Post)" href="#">Delete</a>
          </li>
        </ul>
        
        <h2></h2>
        
      </div>
</template>

<script>
import { Form, HasError, AlertError } from 'vform'
export default {
  name: 'Post',
  components: {HasError},
  data: function(){
    return {
      Posts : false
    }
  },
  created: function(){
    this.listPosts();
  },
  methods: function(){
    listPosts: function(){
      
      var that = this;
      this.form.get('/Posts').then(function(response){
        that.Posts = response.data;
      })
      
    },
    createPost: function(){
      
      var that = this;
      this.form.post('/Posts').then(function(response){
        that.form.fill(response.data);
      })
      
    },
    deletePost: function(Post){
      
      var that = this;
      this.form.delete('/Posts/'+Post.id).then(function(response){
        that.form.fill(response.data);
      })
      
    }
  }
}
</script>

<style lang="less">
</style>

```

## Configuration

Here are the configuration settings with their default values.

```
<?php 
return [
    'model_dir' => base_path('app'),
    'controller_dir' => base_path('app/Http/Controllers'),
    'vue_files_dir' => base_path('resources/views/vue'),
    'routes_dir' => base_path('routes'),
    'routes_file' => 'api.php'
];
?>
```
To copy the config file to your working Laravel project. Enter the following artisan command

`php artisan vendor:publish --provider="lummy\vueApi\vueApiServiceProvider" --tag="config"`

##### model_dir
Specifies the location where the generated model files should be stored

#### controller_dir

Specifies the location where the generated controller files should be stored

#### vue_files_dir

Specifies the location where the Vue single file templates should be stored

#### routes_dir
Specifies the location of the routes directory

#### routes_file
Specifies the name of the routes file



