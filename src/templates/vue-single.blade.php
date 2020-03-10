<template lang="html">
      <div class="{{ $singular }}">
      </div>
</template>

<script>
import { Form, HasError, AlertError } from 'vform'
export default {
  name: '{{ $singular }}',
  components: {HasError},
  data: function(){
    return {
      {{ $singular }}: false,
      {{ $plural }} : false,
      form: new Form{
        
      }
    }
  },
  created: function(){
    this.get{{$singular}}();
  },
  methods: function(){
    get{{$singular}}: function({{$singular}}){
      
      var that = this;
      this.form.get('/{{ $plural }}/'+{{$singular}}.id).then(function(response){
        that.form.fill(response.data);
      })
      
    },
    update{{$singular}}: function(){
      
      var that = this;
      this.form.put('/{{ $plural }}/'+form.id).then(function(response){
        that.form.fill(response.data);
      })
      
    },
    delete{{$singular}}: function(){
      
      var that = this;
      this.form.delete('/{{ $plural }}/'+form.id).then(function(response){
        that.form.fill(response.data);
      })
      
    }
  }
}
</script>

<style lang="less">
</style>