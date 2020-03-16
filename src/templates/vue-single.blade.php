<template lang="html">
      <div class="{{ $data['singular'] }}">
        
        <form @submit.prevent="update{{$data['singular']}}">
      
          <div class="form-group">
              <button type="submit" :disabled="form.busy" name="button">@{{ (form.busy) ? 'Please wait...' : 'Update'}}</button>
              <button @click.prevent="delete{{$data['singular']}}">@{{ (form.busy) ? 'Please wait...' : 'Delete'}}</button>
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
      {{ $data['singular_lower'] }}: false,
      {{ $data['plural_lower'] }} : false,
      form: new Form(
        
      )
    }
  },
  created: function(){
    this.get{{$data['singular']}}();
  },
  methods: {
    get{{$data['singular']}}: function({{$data['singular']}}){
      
      var that = this;
      this.form.get('/{{ $data['plural_lower'] }}/'+this.$route.params.id).then(function(response){
        that.form.fill(response.data);
      })
      
    },
    update{{$data['singular']}}: function(){
      
      var that = this;
      this.form.put('/{{ $data['plural_lower'] }}/'+this.$route.params.id).then(function(response){
        that.form.fill(response.data);
      })
      
    },
    delete{{$data['singular']}}: function(){
      
      var that = this;
      this.form.delete('/{{ $data['plural_lower'] }}/'+this.$route.params.id).then(function(response){
        that.form.fill(response.data);
      })
      
    }
  }
}
</script>

<style lang="less">
.{{ $data['singular'] }}{
  
}
</style>