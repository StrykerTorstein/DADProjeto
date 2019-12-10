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
        
        $movements = $user->movements()->orderBy('date', 'desc')->paginate(20);

        return MovementResource::collection($movements);
        //return Movement::where("wallet_id", $id)->orderBy('date', 'desc')->paginate(10);

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

    public function payment(Request $request){
        //Todo
        dd($request);
    }
}
