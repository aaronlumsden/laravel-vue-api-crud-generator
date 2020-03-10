namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\{{ $name }};

class {{ $name }} extends Controller
{
    public function get(Request $request, $id){
      return {{ $name }}::findOrFail($id);
    }
    
    public function list(Request $request){
      return {{ $name }}::get();
    }
    
    public function create(Request $request){
        $input = $request->all();
        ${{ $name }} = {{ $name }}::create($input)->save();
        return ${{ $name }};
    }
    
    public function update(Request $request, $id){
        ${{ $name }} = {{ $name }}::findOrFail($id);
        $input = $request->all();
        ${{ $name }}->fill($input)->save();
        return ${{ $name }};
    }
    
    public function delete(Request $request, $id){
        ${{$name}} = {{$name}}::findOrFail($id);
        ${{$name}}->destroy();
    }
}
