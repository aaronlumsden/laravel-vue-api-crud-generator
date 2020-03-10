<template lang="html">
      <div class="posts">
        
        <h1>Get {{ $plural }}</h1>
        
        <ul v-if="{{ $plural }}">
          <li v-for="({{ $singular }},index) in {{ $plural }}" :key="{{ $singular }}.id">
            
            {{ $singular }}
            <a @click.prevent="delete{{$singular}}({{ $singular }})" href="#">Delete</a>
          </li>
        </ul>
        
        <h2></h2>
        
      </div>
</template>

<script>
import { Form, HasError, AlertError } from 'vform'
export default {
  name: '{{ $singular }}',
  components: {HasError},
  data: function(){
    return {
      {{ $plural }} : false
    }
  },
  created: function(){
    this.list{{$plural}}();
  },
  methods: function(){
    list{{ $plural }}: function(){
      
      var that = this;
      this.form.get('/{{ $plural }}').then(function(response){
        that.{{ $plural }} = response.data;
      })
      
    },
    create{{ $singular }}: function(){
      
      var that = this;
      this.form.post('/{{ $plural }}').then(function(response){
        that.form.fill(response.data);
      })
      
    },
    delete{{$singular}}: function({{ $singular }}){
      
      var that = this;
      this.form.delete('/{{ $plural }}/'+{{ $singular }}.id).then(function(response){
        that.form.fill(response.data);
      })
      
    }
  }
}
</script>

<style lang="less">
</style>