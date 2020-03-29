<template lang="html">
      <div class="{{ $data['singular'] }}Single">
        <h1>Update {{ $data['singular'] }}</h1>
        
        <form @submit.prevent="update{{$data['singular']}}" v-if="loaded">
          
          <router-link to="/{{ $data['plural_lower'] }}">< Back to {{ $data['plural_lower'] }}</router-link>
          
@foreach($data['fields'] as $field)
            <div class="form-group">
@if($field['name'] == 'id' || $field['name'] == 'updated_at' || $field['name'] == 'created_at' )   
                  <input type="hidden" v-model="form.{{$field['name']}}"></input>
@elseif($field['simplified_type'] == 'text')
                  <label>{{ $field['name'] }}</label>
                  <input type="text" v-model="form.{{$field['name']}}" @if($field['max']) maxlength="{{$field['max']}}" @endif></input>
@if($field['required'] && $field['name'] !== 'id')
                  <has-error :form="form" field="{{$field['name']}}"></has-error>
@endif
@elseif($field['simplified_type'] == 'textarea')
                  <label>{{ $field['name'] }}</label>
                  <textarea v-model="form.{{$field['name']}}" @if($field['max']) maxlength="{{$field['max']}}" @endif></textarea>
@if($field['required'] && $field['name'] !== 'id')
                  <has-error :form="form" field="{{$field['name']}}"></has-error>
@endif
@else
                  <label>{{ $field['name'] }}</label>
                  <input type="number" v-model="form.{{$field['name']}}"></input>
@if($field['required'] && $field['name'] !== 'id')
                  <has-error :form="form" field="{{$field['name']}}"></has-error>
@endif
@endif
            </div>
@endforeach
      
          <div class="form-group">
              <button class="button" type="submit" :disabled="form.busy" name="button">@{{ (form.busy) ? 'Please wait...' : 'Update'}}</button>
              <button @click.prevent="delete{{$data['singular']}}">@{{ (form.busy) ? 'Please wait...' : 'Delete'}}</button>
          </div>
        </form>
        
        <span v-else>Loading {{ $data['singular_lower'] }}...</span>
      </div>
</template>

<script>
import { Form, HasError, AlertError } from 'vform'
export default {
  name: '{{ $data['singular'] }}',
  components: {HasError},
  data: function(){
    return {
      loaded: false,
      form: new Form({
@foreach($data['fields'] as $field)
          "{{$field['name']}}" : "",
@endforeach        
      })
    }
  },
  created: function(){
    this.get{{$data['singular']}}();
  },
  methods: {
    get{{$data['singular']}}: function({{$data['singular']}}){
      
      var that = this;
      this.form.get('{{config('vueApi.vue_url_prefix')}}/{{ $data['plural_lower'] }}/'+this.$route.params.id).then(function(response){
        that.form.fill(response.data);
        that.loaded = true;
      }).catch(function(e){
          if (e.response && e.response.status == 404) {
              that.$router.push('/404');
          }
      });
      
    },
    update{{$data['singular']}}: function(){
      
      var that = this;
      this.form.put('{{config('vueApi.vue_url_prefix')}}/{{ $data['plural_lower'] }}/'+this.$route.params.id).then(function(response){
        that.form.fill(response.data);
      })
      
    },
    delete{{$data['singular']}}: function(){
      
      var that = this;
      this.form.delete('{{config('vueApi.vue_url_prefix')}}/{{ $data['plural_lower'] }}/'+this.$route.params.id).then(function(response){
        that.form.fill(response.data);
        that.$router.push('/{{$data['plural_lower']}}');
      })
      
    }
  }
}
</script>

<style lang="less">
.{{ $data['singular'] }}Single{
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