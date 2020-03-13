<template lang="html">
      <div class="{{ $singular }}">
        
        <form @submit.prevent="update{{$singular}}">
        {!! $htmlForm !!}
          <div class="form-group">
              <button type="submit" :disabled="form.busy" name="button">@{{ (form.busy) ? 'Please wait...' : 'Update'}}</button>
              <button @click.prevent="delete{{$singular}}">@{{ (form.busy) ? 'Please wait...' : 'Delete'}}</button>
          </div>
        </form>
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
      form: new Form(@json($fields,JSON_PRETTY_PRINT))
    }
  },
  created: function(){
    this.get{{$singular}}();
  },
  methods: {
    get{{$singular}}: function({{$singular}}){
      
      var that = this;
      this.form.get('/{{ $plural }}/'+this.$route.params.id).then(function(response){
        that.form.fill(response.data);
      })
      
    },
    update{{$singular}}: function(){
      
      var that = this;
      this.form.put('/{{ $plural }}/'+this.$route.params.id).then(function(response){
        that.form.fill(response.data);
      })
      
    },
    delete{{$singular}}: function(){
      
      var that = this;
      this.form.delete('/{{ $plural }}/'+this.$route.params.id).then(function(response){
        that.form.fill(response.data);
      })
      
    }
  }
}
</script>

<style lang="less">
.{{ $singular }}{
  
}
</style>