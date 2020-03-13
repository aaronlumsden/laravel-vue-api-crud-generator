## Laravel Vue API Crud Generator

### Installation
`composer require lummy/laravel-vue-api-crud-generator`

#### Overview
A Laravel package that lets you generate boilerplate code. Simply enter the name of a database table and based on that it will create 

- A Laravel model
- A Laravel controller (with get, list, create, update, delete as well as validation based on your DB table)
- Laravel routes (get, list, create, update, delete)
- 2 Vue.js single file components to create, update, list, delete and show (using Vform & axios)


This package aims to speed up the process of communicating between backend (Laravel) and frontend (Vue.js).


## Usage

Firstly you should create a new migration in the same way that you usually would. For example if creating a posts table use the command 

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

Once you have done that you just need to run one `vueapi` command. Add the name of your table to the end of the command so in this case it's posts.

`php artisan vueapi:generate posts`

This will then generate all the files mentioned above.

Once you have run this command, using the `posts` example above, it will create the following boilerplate files:

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
          
          $validatedData = $request->validate([
                        'id' => 'size:20',
                        'title' => 'size:200',
                        'content' => 'required',
                        'created_at' => 'required',
                        'updated_at' => 'required',
                    ],[
                        'content.required' => 'Please ensure you have filled in content',
                        'created_at.required' => 'Please ensure you have filled in created_at',
                        'updated_at.required' => 'Please ensure you have filled in updated_at',
                    ]);
      
        $input = $request->all();
        $Post = Post::create($input)->save();
        return $Post;
    }
    
    public function update(Request $request, $id){
        
      $validatedData = $request->validate([
                'id' => 'size:20',
                'title' => 'size:200',
                'content' => 'required',
                'created_at' => 'required',
                'updated_at' => 'required',
            ],[
                'content.required' => 'Please ensure you have filled in content',
                'created_at.required' => 'Please ensure you have filled in created_at',
                'updated_at.required' => 'Please ensure you have filled in updated_at',
            ]);
      
      
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
        
        <h1>Get posts</h1>
        
        <ul v-if="posts">
          <li v-for="(post,index) in posts" :key="post.id">
            
            post
            <a @click.prevent="deletepost(post)" href="#">Delete</a>
          </li>
        </ul>
        
        <h2>Create post</h2>
        
        <form @submit.prevent="createpost">
        					<div class='form-group'>
						<label>id</label>
						<input type='number' 'maxlength=20' placeholder='id' v-model='form.id'/>
					</div>

					<div class='form-group'>
						<label>title</label>
						<input type='text' 'maxlength=200' placeholder='title' v-model='form.title'/>
					</div>

					<div class='form-group'>
						<label>content</label>
						<textarea  placeholder='content' v-model='form.content'/></textarea>
					</div>

					<div class='form-group'>
						<label>created_at</label>
						<input type='date' placeholder='created_at' v-model='form.created_at'/>
					</div>

					<div class='form-group'>
						<label>updated_at</label>
						<input type='date' placeholder='updated_at' v-model='form.updated_at'/>
					</div>


          <div class="form-group">
              <button type="submit" :disabled="form.busy" name="button">{{ (form.busy) ? 'Please wait...' : 'Submit'}}</button>
          </div>
        </form>
        
      </div>
</template>

<script>
import { Form, HasError, AlertError } from 'vform'
export default {
  name: 'post',
  components: {HasError},
  data: function(){
    return {
      posts : false,
      form: new Form({
    "id": "",
    "title": "",
    "content": "",
    "created_at": "",
    "updated_at": ""
})
    }
  },
  created: function(){
    this.listposts();
  },
  methods: function(){
    listposts: function(){
      
      var that = this;
      this.form.get('/posts').then(function(response){
        that.posts = response.data;
      })
      
    },
    createpost: function(){
      
      var that = this;
      this.form.post('/posts').then(function(response){
        that.form.fill(response.data);
      })
      
    },
    deletepost: function(post){
      
      var that = this;
      this.form.delete('/posts/'+post.id).then(function(response){
        that.form.fill(response.data);
      })
      
    }
  }
}
</script>

<style lang="less">
.posts{
  
}
</style>
```

### Vue (Single template)

```
<template lang="html">
      <div class="post">
        
        <form @submit.prevent="updatepost">
        					<div class='form-group'>
						<label>id</label>
						<input type='number' 'maxlength=20' placeholder='id' v-model='form.id'/>
					</div>

					<div class='form-group'>
						<label>title</label>
						<input type='text' 'maxlength=200' placeholder='title' v-model='form.title'/>
					</div>

					<div class='form-group'>
						<label>content</label>
						<textarea  placeholder='content' v-model='form.content'/></textarea>
					</div>

					<div class='form-group'>
						<label>created_at</label>
						<input type='date' placeholder='created_at' v-model='form.created_at'/>
					</div>

					<div class='form-group'>
						<label>updated_at</label>
						<input type='date' placeholder='updated_at' v-model='form.updated_at'/>
					</div>


          <div class="form-group">
              <button type="submit" :disabled="form.busy" name="button">{{ (form.busy) ? 'Please wait...' : 'Submit'}}</button>
          </div>
        </form>
      </div>
</template>

<script>
import { Form, HasError, AlertError } from 'vform'
export default {
  name: 'post',
  components: {HasError},
  data: function(){
    return {
      post: false,
      posts : false,
      form: new Form({
    "id": "",
    "title": "",
    "content": "",
    "created_at": "",
    "updated_at": ""
})
  },
  created: function(){
    this.getpost();
  },
  methods: function(){
    getpost: function(post){
      
      var that = this;
      this.form.get('/posts/'+post.id).then(function(response){
        that.form.fill(response.data);
      })
      
    },
    updatepost: function(){
      
      var that = this;
      this.form.put('/posts/'+form.id).then(function(response){
        that.form.fill(response.data);
      })
      
    },
    deletepost: function(){
      
      var that = this;
      this.form.delete('/posts/'+form.id).then(function(response){
        that.form.fill(response.data);
      })
      
    }
  }
}
</script>

<style lang="less">
.post{
  
}
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
To copy the config file to your working Laravel project enter the following artisan command

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

### Customising the templates

If you use another frontend framework such as React or you want to adjust the structure of the templates then you can customise the templates by publishing them to your working Laravel project

`php artisan vendor:publish --provider="lummy\vueApi\vueApiServiceProvider" --tag="templates"``

They will then appear in

`\resources\views\vendor\vueApi`



