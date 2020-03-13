namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{{ $plural }};

class {{ $plural }}Controller extends Controller
{
    public function get(Request $request, $id){
      return {{ $plural }}::findOrFail($id);
    }
    
    public function list(Request $request){
      return {{ $plural }}::get();
    }
    
    public function create(Request $request){
          
          $validatedData = $request->validate([
          @foreach ($validatorString[0] as $key => $value)
              '{{ $key }}' => '{{ $value }}',
          @endforeach
          ],[
          @foreach ($validatorString[1] as $key => $value)
              '{{ $key }}' => '{{ $value }}',
          @endforeach
          ]);
      
        $input = $request->all();
        ${{ $plural }} = {{ $plural }}::create($input)->save();
        ${{ $plural }}->save();
        
        return ${{ $plural }};
    }
    
    public function update(Request $request, $id){
        
      $validatedData = $request->validate([
      @foreach ($validatorString[0] as $key => $value)
          '{{ $key }}' => '{{ $value }}',
      @endforeach
      ],[
      @foreach ($validatorString[1] as $key => $value)
          '{{ $key }}' => '{{ $value }}',
      @endforeach
      ]);
      
      
        ${{ $plural }} = {{ $plural }}::findOrFail($id);
        $input = $request->all();
        ${{ $plural }}->fill($input)->save();
        return ${{ $plural }};
    }
    
    public function delete(Request $request, $id){
        ${{$plural}} = {{$plural}}::findOrFail($id);
        ${{$plural}}->delete();
    }
}
