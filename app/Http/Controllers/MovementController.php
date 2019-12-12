<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Movement;
use App\Wallet; 
use App\User;
use App\Category;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Response;
use App\Http\Resources\Wallet as WalletResource; 
use App\Http\Resources\Movement as MovementResource; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class MovementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->middleware(['auth']);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $user = Auth::guard('api')->user();
        //dd($user -> wallet());
        
        $movements = $user->movements()->orderBy('date', 'desc')->paginate(5);

        return MovementResource::collection($movements);
        //return Movement::where("wallet_id", $id)->orderBy('date', 'desc')->paginate(10);

    }

    public function filter(Request $request){

        //$movements = DB::table('movements');

        $user = Auth::guard('api')->user();
        //dd($user -> wallet());
        
        $movements = $user->movements();

        if(($request->has("id"))){
            $movements->where('id', '=', $request->id)->orderBy('date', 'desc');
        }
        
        if(($request->has("type"))){
            $movements->where('type', '=', $request->type)->orderBy('date', 'desc');
        }

        if(($request->has("start_date")) && ($request->has("end_date"))){
            $carbon = new Carbon($request->end_date);
            $carbon->addDays(1);
            $movements->where([['date', '>=', $request->start_date], ['date', '<=', $carbon]])->orderBy('date', 'desc');
            //where('date', '>=', $request->start_date)->where('date', '<=', $request->end_date)
        }

        

        return MovementResource::collection($movements->paginate(5));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
