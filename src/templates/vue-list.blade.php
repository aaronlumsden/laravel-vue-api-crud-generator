<template lang="html">
      <div class="{{ $data['plural_lower'] }}">
        
        <div class="half">
          
          <h1>Create {{$data['singular_lower']}}</h1>
          
          <form @submit.prevent="create{{ $data['singular'] }}">
            
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
                <button class="button" type="submit" :disabled="form.busy" name="button">@{{ (form.busy) ? 'Please wait...' : 'Submit'}}</button>
            </div>
          </form>
          
        </div><!-- End first half -->
        
        <div class="half">
          
          <h1>List {{ $data['plural_lower'] }}</h1>
          
          <ul v-if="{{ $data['plural_lower'] }}.length > 0">
            <li v-for="({{ $data['singular_lower'] }},index) in {{ $data['plural_lower'] }}" :key="{{ $data['singular_lower'] }}.id">
              
            <router-link :to="'/{{ $data['singular_lower'] }}/'+{{ $data['singular_lower'] }}.id">
              
              {{ $data['singular_lower']}} @{{ index }}

              <button @click.prevent="delete{{$data['singular']}}({{ $data['singular_lower'] }},index)" type="button" :disabled="form.busy" name="button">@{{ (form.busy) ? 'Please wait...' : 'Delete'}}</button>
              
            </router-link>
              
            </li>
          </ul>
          
          <span v-else-if="!{{ $data['plural_lower'] }}">Loading...</span>
          <span v-else>No {{ $data['plural_lower'] }} exist</span>
          
        </div><!-- End 2nd half -->
        
      </div>
</template>

<script>
import { Form, HasError, AlertError } from 'vform'
export default {
  name: '{{ $data['singular'] }}',
  components: {HasError},
  data: function(){
    return {
      {{ $data['plural_lower'] }} : false,
      form: new Form({
@foreach($data['fields'] as $field)
          "{{$field['name']}}" : "",
@endforeach
      })
    }
  },
  created: function(){
    this.list{{$data['plural']}}();
  },
  methods: {
    list{{ $data['plural'] }}: function(){
      
      var that = this;
      this.form.get('{{config('vueApi.vue_url_prefix')}}/{{ $data['plural_lower'] }}').then(function(response){
        that.{{ $data['plural_lower'] }} = response.data;
      })
      
    },
    create{{ $data['singular'] }}: function(){
      
      var that = this;
      this.form.post('{{config('vueApi.vue_url_prefix')}}/{{ $data['plural_lower'] }}').then(function(response){
        that.{{ $data['plural_lower'] }}.push(response.data);
      })
      
    },
    delete{{$data['singular']}}: function({{ $data['singular_lower'] }}, index){
      
      var that = this;
      this.form.delete('{{config('vueApi.vue_url_prefix')}}/{{ $data['plural_lower'] }}/'+{{ $data['singular_lower'] }}.id).then(function(response){
        that.{{ $data['plural_lower'] }}.splice(index,1);
      })
      
    }
  }
}
</script>

<style lang="less">
.{{ $data['plural_lower'] }}{
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