namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{{ $data['plural'] }};

class {{ $data['plural'] }}Controller extends Controller
{
    public function get(Request $request, $id){
      return {{ $data['plural'] }}::findOrFail($id);
    }
    
    public function list(Request $request){
      return {{ $data['plural'] }}::get();
    }
    
    public function create(Request $request){
        
      $validatedData = $request->validate([
@foreach($data['fields'] as $field)
@if($field['required'] && $field['name'] !== 'id')
        '{{ $field['name'] }}' => 'required @if($field['max'])|max:{{$field['max']}} @endif',
@endif
@endforeach
      ],[
@foreach($data['fields'] as $field)
@if($field['required'] && $field['name'] !== 'id')
        '{{ $field['name'] }}.required' => '{{ $field['name'] }} is a required field.',
@if($field['max'])
        '{{ $field['name'] }}.max' => '{{ $field['name'] }} can only be {{$field['max']}} characters.',
@endif
@endif
@endforeach
      ]);

        ${{ $data['plural_lower'] }} = {{ $data['plural'] }}::create($request->all());    
        return ${{ $data['plural_lower'] }};
    }
    
    public function update(Request $request, $id){
      
      $validatedData = $request->validate([
@foreach($data['fields'] as $field)
@if($field['required'] && $field['name'] !== 'id')
        '{{ $field['name'] }}' => 'required @if($field['max'])|max:{{$field['max']}} @endif',
@endif
@endforeach
      ],[
@foreach($data['fields'] as $field)
@if($field['required'] && $field['name'] !== 'id')
        '{{ $field['name'] }}.required' => '{{ $field['name'] }} is a required field.',
@if($field['max'])
        '{{ $field['name'] }}.max' => '{{ $field['name'] }} can only be {{$field['max']}} characters.',
@endif
@endif
@endforeach
      ]);

        ${{ $data['plural_lower'] }} = {{ $data['plural'] }}::findOrFail($id);
        $input = $request->all();
        ${{ $data['plural_lower'] }}->fill($input)->save();
        return ${{ $data['plural_lower'] }};
    }
    
    public function delete(Request $request, $id){
        ${{$data['plural_lower']}} = {{$data['plural']}}::findOrFail($id);
        ${{$data['plural_lower']}}->delete();
    }
}
