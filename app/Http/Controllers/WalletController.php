<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Wallet;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function count()
    {
        //Variável com o numero de wallets
        $nr_wallet = Wallet::count();
        //enviar a resposta em formato json
        return response()->json([
            'total' => $nr_wallet
        ], 200);
    }

    public function getBalance($id){

        $balance = DB::table('wallets')->select('balance')->where('id', $id)->get();
        return $balance[0]->balance;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //criar uma nova wallet
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //mostrar uma wallet expecifica é como se fosse um where
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
        //atualizar um wallet
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //apagar uma wallet
    }

    public function exists($email){
        //$wallet = Wallet::where('email', '=', $email)->first();
        $wallet = Wallet::whereEmail($email)->first();
        return $wallet;
    }
}
