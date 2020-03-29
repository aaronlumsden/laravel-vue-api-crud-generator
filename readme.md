## Laravel Vue API Crud Generator

#### Overview
A Laravel package that lets you generate boilerplate code for a Vue.js/Laravel app. Simply enter the name of a database table and based on that it will create:

- A Laravel model
- A Laravel controller (with get, list, create, update, delete as well as validation based on a chosen DB table)
- Laravel routes (get, list, create, update, delete)
- 2 Vue.js single file components to create, update, list, delete and show (using Vform & axios)

This package aims to speed up the process of communicating between backend (Laravel) and frontend (Vue.js).

### Installation
`composer require lummy/laravel-vue-api-crud-generator`


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

Based on a `posts` DB table it will produce these routes

```
Route::get('posts', 'PostsController@list');
Route::get('posts/{id}', 'PostsController@get');
Route::post('posts', 'PostsController@create');
Route::put('posts/{id}', 'PostsController@update');
Route::delete('posts/{id}', 'PostsController@delete');

```

### Controller

Based on a `posts` DB table it will produce this controller

```
<?php 
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Posts;

class PostsController extends Controller
{
    public function get(Request $request, $id){
      return Posts::findOrFail($id);
    }
    
    public function list(Request $request){
      return Posts::get();
    }
    
    public function create(Request $request){
        
      $validatedData = $request->validate([
        'title' => 'required |max:200 ',
        'content' => 'required ',
        'meta_description' => 'required |max:160 ',
      ],[
        'title.required' => 'title is a required field.',
        'title.max' => 'title can only be 200 characters.',
        'content.required' => 'content is a required field.',
        'meta_description.required' => 'meta_description is a required field.',
        'meta_description.max' => 'meta_description can only be 160 characters.',
      ]);

        $posts = Posts::create($request->all());    
        return $posts;
    }
    
    public function update(Request $request, $id){
      
      $validatedData = $request->validate([
        'title' => 'required |max:200 ',
        'content' => 'required ',
        'meta_description' => 'required |max:160 ',
      ],[
        'title.required' => 'title is a required field.',
        'title.max' => 'title can only be 200 characters.',
        'content.required' => 'content is a required field.',
        'meta_description.required' => 'meta_description is a required field.',
        'meta_description.max' => 'meta_description can only be 160 characters.',
      ]);

        $posts = Posts::findOrFail($id);
        $input = $request->all();
        $posts->fill($input)->save();
        return $posts;
    }
    
    public function delete(Request $request, $id){
        $posts = Posts::findOrFail($id);
        $posts->delete();
    }
}
 ?>

```
### Model 

Based on a `posts` DB table it will produce this model

```
<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;

class Posts extends Model
{

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var  array
     */
    protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var  array
     */
    protected $casts = [
        ''
    ];
}?>

```

### Vue (List template)

Based on a `posts` DB table it will produce this Vue.js list single file component (Posts-list.vue)

```
<template lang="html">
      <div class="posts">
        
        <div class="half">
          
          <h1>Create post</h1>
          
          <form @submit.prevent="createPost">
            
            <div class="form-group">
   
                  <input type="hidden" v-model="form.id"></input>
            </div>
            <div class="form-group">
                  <label>title</label>
                  <input type="text" v-model="form.title"  maxlength="200" ></input>
                  <has-error :form="form" field="title"></has-error>
            </div>
            <div class="form-group">
                  <label>content</label>
                  <textarea v-model="form.content" ></textarea>
                  <has-error :form="form" field="content"></has-error>
            </div>
            <div class="form-group">
                  <label>meta_description</label>
                  <input type="text" v-model="form.meta_description"  maxlength="160" ></input>
                  <has-error :form="form" field="meta_description"></has-error>
            </div>
            <div class="form-group">
   
                  <input type="hidden" v-model="form.created_at"></input>
            </div>
            <div class="form-group">
   
                  <input type="hidden" v-model="form.updated_at"></input>
            </div>
        
            <div class="form-group">
                <button class="button" type="submit" :disabled="form.busy" name="button">{{ (form.busy) ? 'Please wait...' : 'Submit'}}</button>
            </div>
          </form>
          
        </div><!-- End first half -->
        
        <div class="half">
          
          <h1>List posts</h1>
          
          <ul v-if="posts.length > 0">
            <li v-for="(post,index) in posts" :key="post.id">
              
            <router-link :to="'/post/'+post.id">
              
              post {{ index }}

              <button @click.prevent="deletePost(post,index)" type="button" :disabled="form.busy" name="button">{{ (form.busy) ? 'Please wait...' : 'Delete'}}</button>
              
            </router-link>
              
            </li>
          </ul>
          
          <span v-else-if="!posts">Loading...</span>
          <span v-else>No posts exist</span>
          
        </div><!-- End 2nd half -->
        
      </div>
</template>

<script>
import { Form, HasError, AlertError } from 'vform'
export default {
  name: 'Post',
  components: {HasError},
  data: function(){
    return {
      posts : false,
      form: new Form({
          "id" : "",
          "title" : "",
          "content" : "",
          "meta_description" : "",
          "created_at" : "",
          "updated_at" : "",
      })
    }
  },
  created: function(){
    this.listPosts();
  },
  methods: {
    listPosts: function(){
      
      var that = this;
      this.form.get('/posts').then(function(response){
        that.posts = response.data;
      })
      
    },
    createPost: function(){
      
      var that = this;
      this.form.post('/posts').then(function(response){
        that.posts.push(response.data);
      })
      
    },
    deletePost: function(post, index){
      
      var that = this;
      this.form.delete('/posts/'+post.id).then(function(response){
        that.posts.splice(index,1);
      })
      
    }
  }
}
</script>

<style lang="less">
.posts{
    margin:0 auto;
    width:700px;
    display:flex;
    .half{
      flex:1;
      &:first-of-type{
        margin-right:20px;
      }
    }
    form{
      .form-group{
        margin-bottom:20px;
        label{
          display:block;
          margin-bottom:5px;
          text-transform: capitalize;
        }
        input[type="text"],input[type="number"],textarea{
          width:100%;
          max-width:100%;
          min-width:100%;
          padding:10px;
          border-radius:3px;
          border:1px solid silver;
          font-size:1rem;
          &:focus{
            outline:0;
            border-color:blue;
          }
        }
        .invalid-feedback{
          color:red;
          &::first-letter{
            text-transform:capitalize;
          }
        }
      }
      .button{
        appearance: none;
        background: #3bdfd9;
        font-size: 1rem;
        border: 0px;
        padding: 10px 20px;
        border-radius: 3px;
        font-weight: bold;
        &:hover{
          cursor:pointer;
          background: darken(#3bdfd9,10);
        }
      }
    }
}
</style>
```

### Vue (Single template)

Based on a `posts` DB table it will produce this Vue.js single file component (Posts-single.vue)

```
<template lang="html">
      <div class="PostSingle">
        <h1>Update Post</h1>
        
        <form @submit.prevent="updatePost" v-if="loaded">
          
          <router-link to="/posts">< Back to posts</router-link>
          
            <div class="form-group">
   
                  <input type="hidden" v-model="form.id"></input>
            </div>
            <div class="form-group">
                  <label>title</label>
                  <input type="text" v-model="form.title"  maxlength="200" ></input>
                  <has-error :form="form" field="title"></has-error>
            </div>
            <div class="form-group">
                  <label>content</label>
                  <textarea v-model="form.content" ></textarea>
                  <has-error :form="form" field="content"></has-error>
            </div>
            <div class="form-group">
                  <label>meta_description</label>
                  <input type="text" v-model="form.meta_description"  maxlength="160" ></input>
                  <has-error :form="form" field="meta_description"></has-error>
            </div>
            <div class="form-group">
   
                  <input type="hidden" v-model="form.created_at"></input>
            </div>
            <div class="form-group">
   
                  <input type="hidden" v-model="form.updated_at"></input>
            </div>
      
          <div class="form-group">
              <button class="button" type="submit" :disabled="form.busy" name="button">{{ (form.busy) ? 'Please wait...' : 'Update'}}</button>
              <button @click.prevent="deletePost">{{ (form.busy) ? 'Please wait...' : 'Delete'}}</button>
          </div>
        </form>
        
        <span v-else>Loading post...</span>
      </div>
</template>

<script>
import { Form, HasError, AlertError } from 'vform'
export default {
  name: 'Post',
  components: {HasError},
  data: function(){
    return {
      loaded: false,
      form: new Form({
          "id" : "",
          "title" : "",
          "content" : "",
          "meta_description" : "",
          "created_at" : "",
          "updated_at" : "",
        
      })
    }
  },
  created: function(){
    this.getPost();
  },
  methods: {
    getPost: function(Post){
      
      var that = this;
      this.form.get('/posts/'+this.$route.params.id).then(function(response){
        that.form.fill(response.data);
        that.loaded = true;
      }).catch(function(e){
          if (e.response && e.response.status == 404) {
              that.$router.push('/404');
          }
      });
      
    },
    updatePost: function(){
      
      var that = this;
      this.form.put('/posts/'+this.$route.params.id).then(function(response){
        that.form.fill(response.data);
      })
      
    },
    deletePost: function(){
      
      var that = this;
      this.form.delete('/posts/'+this.$route.params.id).then(function(response){
        that.form.fill(response.data);
        that.$router.push('/posts');
      })
      
    }
  }
}
</script>

<style lang="less">
.PostSingle{
  margin:0 auto;
  width:700px;
  form{
    .form-group{
      margin-bottom:20px;
      label{
        display:block;
        margin-bottom:5px;
        text-transform: capitalize;
      }
      input[type="text"],input[type="number"],textarea{
        width:100%;
        max-width:100%;
        min-width:100%;
        padding:10px;
        border-radius:3px;
        border:1px solid silver;
        font-size:1rem;
        &:focus{
          outline:0;
          border-color:blue;
        }
      }
      .button{
        appearance: none;
        background: #3bdfd9;
        font-size: 1rem;
        border: 0px;
        padding: 10px 20px;
        border-radius: 3px;
        font-weight: bold;
        &:hover{
          cursor:pointer;
          background: darken(#3bdfd9,10);
        }
      }
      .invalid-feedback{
        color:red;
        &::first-letter{
          text-transform:capitalize;
        }
      }
    }
  }
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

#### vue_url_prefix
Specifies what prefix should be added to the URL in your view files. The default is `/api` ie `/api/posts`

#### routes_dir
Specifies the location of the routes directory

#### routes_file
Specifies the name of the routes file

### Customising the templates

If you use another frontend framework such as React or you want to adjust the structure of the templates then you can customise the templates by publishing them to your working Laravel project

`php artisan vendor:publish --provider="lummy\vueApi\vueApiServiceProvider" --tag="templates"``

They will then appear in

`\resources\views\vendor\vueApi`


### Variables in the templates

Each template file passes a data array with the following fields

##### $data['singular']
The singular name for the DB table eg Post

##### $data['plural']
The plural name for the DB table eg Posts

##### $data['singular_lower']
The singular name for the DB table (lowercase) eg post

##### $data['plural_lower']
The plural name for the DB table eg (lowercase) eg posts

##### $data['fields']
An array of the fields that are part of the model.

 - name (the field name)
 - type (the mysql varchar, int etc)
 - simplified_type (text, textarea, number)
 - required (is the field required)
 - max (the maximum number of characters)
 
### Other things to note
 
I have only tested this on Laravel MYSQL driver so I'm not sure if it will work on other databases.
 
In Vue.js files the routes are presumed to be: using the posts example. You can easily configure these from the templates generated
 
/posts (Posts-list.vue)
/posts/{id} (Posts-single.vue)

Please feel free to contact me with any  feedback or suggestions https://github.com/aarondo





