<?php
namespace lummy\vueApi;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class AaronsController extends Controller
{
    public static function hello(){
      return ["hello"=>"world"];
    }
}
