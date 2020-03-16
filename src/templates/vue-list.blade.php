<template lang="html">
      <div class="posts">
        
        <h1>Get {{ $data['plural'] }}</h1>
        
        <ul v-if="{{ $data['plural'] }}">
          <li v-for="({{ $data['singular'] }},index) in {{ $data['plural'] }}" :key="{{ $data['singular'] }}.id">
            
            {{ $data['singular'] }} 
            <a @click.prevent="delete{{$data['singular']}}({{ $data['singular'] }},index)" href="#">Delete</a>
          </li>
        </ul>
        
        <h2>Create {{$data['singular']}}</h2>
        
        <form @submit.prevent="create{{ $data['singular'] }}">
      
          <div class="form-group">
              <button type="submit" :disabled="form.busy" name="button">@{{ (form.busy) ? 'Please wait...' : 'Submit'}}</button>
          </div>
        </form>
        
      </div>
</template>

<script>
import { Form, HasError, AlertError } from 'vform'
export default {
  name: '{{ $data['singular'] }}',
  components: {HasError},
  data: function(){
    return {
      {{ $data['plural_lower'] }} : [],
      form: new Form({
        
      })
    }
  },
  created: function(){
    this.list{{$data['plural']}}();
  },
  methods: {
    list{{ $data['plural'] }}: function(){
      
      var that = this;
      this.form.get('/{{ $data['plural_lower'] }}').then(function(response){
        that.{{ $data['plural_lower'] }} = response.data;
      })
      
    },
    create{{ $data['singular'] }}: function(){
      
      var that = this;
      this.form.post('/{{ $data['plural_lower'] }}').then(function(response){
        that.{{ $data['plural_lower'] }}.push(response.data);
      })
      
    },
    delete{{$data['singular']}}: function({{ $data['singular'] }}, index){
      
      var that = this;
      this.form.delete('/{{ $data['plural_lower'] }}/'+{{ $data['singular'] }}.id).then(function(response){
        
        that.{{ $data['plural_lower'] }}.splice(index,1);
        
      })
      
    }
  }
}
</script>

<style lang="less">
.{{ $data['plural'] }}{
  
}
</style>