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

    public function types(){
        $types = Movement::distinct('type')->pluck('type');
        return $types;
    }

    public function filter(Request $request, $id){

        //$movements = DB::table('movements');

        $authenticatedUser = Auth::guard('api')->user();

        if ($id != $authenticatedUser->id) {
            return abort(401);
        }
        
        $user = User::whereId($id)->first();
        //dd($user);
        $movements = $user->movements();
        $wallet = $user->wallet();

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

        if(($request->has("category"))){
            $category = DB::table('categories')->select('id')->where('name', $request->category)->get();
            $movements = $movements->where('category_id', $category[0]->id);
        }

        if(($request->has("type_payment"))){
            $movements->where('type_payment', '=', $request->type_payment)->orderBy('date', 'desc');
        }

        if(($request->has("email"))){

            $transfer_email = DB::table('wallets')->select('id')->where('email', $request->transfer_email)->get();

            if($transfer_email->isEmpty()){
                return 'Transfer e-mail does not exist!';
            }

            $wallet->where('email', '=', $request->email)->orderBy('date', 'desc');
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
        $arrayResponse = [$movement,$wallet];
        $obj = (object) [
            'movement' => $movement,
            'wallet' => $wallet,
            'user' => $user
        ];
        $response = json_encode($obj);
        return $response;
    }
}
