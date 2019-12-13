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
        
        $movements = $user->movements();
        $wallet = $user->wallet();
        $filteredMovementsId = [];
        $filteredMovementsType = [];
        $filteredMovementsDate = [];
        //array_push($filteredMovements, $targetMovements);

        if($request->has("id") && !empty($request->id)){
            $m = $movements->where('id', '=', $request->id)->get();
            array_push($filteredMovementsId, $m);
        }
        
        if(($request->has("type") && !empty($request->type))){
            $m = $movements->where('type', '=', $request->type)->get();
            array_push($filteredMovementsType, $m);
        }
        
        if(($request->has("start_date")) && ($request->has("end_date"))){
            $carbon = new Carbon($request->end_date);
            $carbon->addDays(1);
            $m = $movements->where([['date', '>=', $request->start_date], ['date', '<=', $carbon]])->orderBy('date', 'desc');
            array_push($filteredMovementsDate, $m);
            //where('date', '>=', $request->start_date)->where('date', '<=', $request->end_date)
        }
        dd($request->category);
        if($request->has("category")){
            $category = DB::table('categories')->select('id')->where('name', $request->category)->get();
            $movements = $movements->where('category_id', $category[0]->id);
        }
        
        if(($request->has("type_payment"))){
            $movements->where('type_payment', '=', $request->type_payment)->orderBy('date', 'desc');
        }
        if($request->has("email") && !empty($request->email)){
            //$transfer_email = DB::table('wallets')->select('id')->where('email', $request->transfer_email)->get();
            $walletIds = Wallet::where('email','=',$request->email)->pluck('id');
            if(count($walletIds) > 0){
                $wallet->where('email', '=', $request->email)->orderBy('date', 'desc');
            }
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

    //Done by operators
    public function payment(Request $request){
        $categoryId = Category::where('name','=',$request->categoryName)->pluck('id')->first();
        $movement = new Movement();
        $movement->fill($request->all());
        $movement->category_id = $categoryId;
        $movement->date = date("Y-m-d H:i:s");
        $wallet = Wallet::findOrFail($request->wallet_id);
        $user = User::whereEmail($wallet->email)->first();
        $start_balance = $wallet->balance;
        $end_balance = $start_balance - $movement->value;
        $movement->start_balance = $start_balance;
        $movement->end_balance = $end_balance;
        $wallet->balance = $movement->end_balance;
        $wallet->save();
        $movement->save();
        $obj = (object) [
            'movement' => $movement,
            'wallet' => $wallet,
            'user' => $user
        ];
        $response = json_encode($obj);
        return $response;
    }

    public function expenseTranfer(Request $request){
        $categoryId = Category::where('name','=',$request->categoryName)->pluck('id')->first();
        $movement_src = new Movement();
        $movement_src->fill($request->all());
        $movement_src->category_id = $categoryId;
        $movement_src->date = date("Y-m-d H:i:s");

        $movement_tgt = new Movement();
        $movement_tgt->fill($request->all());
        $movement_tgt->category_id = $categoryId;
        $movement_tgt->date = date("Y-m-d H:i:s");

        $wallet_src = Wallet::where('email','=',$request->srcEmail)->first();
        $start_balance_src = $wallet_src->balance;
        $end_balance_src = $start_balance_src - $movement_src->value;

        $wallet_tgt = Wallet::where('email','=',$request->tgtEmail)->first();
        $start_balance_tgt = $wallet_tgt->balance;
        $end_balance_tgt = $start_balance_tgt + $movement_tgt->value;

        $movement_src->wallet_id = $wallet_tgt->id;
        $movement_tgt->wallet_id = $wallet_src->id;

        $movement_src->transfer = 1;
        $movement_tgt->transfer = 1;

        $movement_src->wallet_id = $wallet_tgt->id;
        $movement_tgt->wallet_id = $wallet_src->id;
        
        $movement_tgt->start_balance = $start_balance_tgt;
        $movement_tgt->end_balance = $end_balance_tgt;

        $movement_src->start_balance = $start_balance_src;
        $movement_src->end_balance = $end_balance_src;

        $wallet_src->balance = $movement_src->end_balance;
        $wallet_tgt->balance = $movement_tgt->end_balance;

        //Im tired, sorry
        $movement_src->start_balance = $start_balance_tgt;
        $movement_src->end_balance = $end_balance_tgt;

        $movement_tgt->start_balance = $start_balance_src;
        $movement_tgt->end_balance = $end_balance_src;

        $user_src = User::whereEmail($wallet_src->email)->first();
        $user_tgt = User::whereEmail($wallet_tgt->email)->first();

        //save data
        $movement_src->save();
        $movement_tgt->save();
        $wallet_src->save();
        $wallet_tgt->save();

        $obj = (object) [
            'movement_src' => $movement_src,
            'wallet_src' => $wallet_src,
            'user_src' => $user_src,
            'movement_tgt' => $movement_tgt,
            'wallet_tgt' => $wallet_tgt,
            'user_tgt' => $user_tgt,
        ];
        $response = json_encode($obj);
        return $response;
    }

    public function expenseExternal(Request $request){
        $categoryId = Category::where('name','=',$request->categoryName)->pluck('id')->first();
        $movement = new Movement();
        $movement->fill($request->all());
        $movement->category_id = $categoryId;
        $movement->date = date("Y-m-d H:i:s");
        $wallet = Wallet::where('email','=',$request->srcEmail)->first();
        $start_balance = $wallet->balance;
        $end_balance = $start_balance - $movement->value;
        $movement->start_balance = $start_balance;
        $movement->end_balance = $end_balance;
        $wallet->balance = $movement->end_balance;
        $wallet->save();
        $movement->wallet_id = $wallet->id;
        $movement->transfer = 0;
        $movement->save();
        $user = User::whereEmail($wallet->email)->first();
        $obj = (object) [
            'movement' => $movement,
            'wallet' => $wallet,
            'user' => $user
        ];
        $response = json_encode($obj);
        return $response;
    }
}
