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
          
      
        $input = $request->all();
        ${{ $data['plural'] }} = {{ $data['plural'] }}::create($input)->save();
        ${{ $data['plural'] }}->save();
        
        return ${{ $data['plural'] }};
    }
    
    public function update(Request $request, $id){

        ${{ $data['plural'] }} = {{ $data['plural'] }}::findOrFail($id);
        $input = $request->all();
        ${{ $data['plural'] }}->fill($input)->save();
        return ${{ $data['plural'] }};
    }
    
    public function delete(Request $request, $id){
        ${{$data['plural']}} = {{$data['plural']}}::findOrFail($id);
        ${{$data['plural']}}->delete();
    }
}
