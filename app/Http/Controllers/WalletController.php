<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Wallet;
use App\User;

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
        $user = User::findOrFail($id);
        $wallet = Wallet::where('email','=',$user->email)->first();
        return $wallet->balance;
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

    public function allEmails(){
        $emails = Wallet::all()->pluck('email');
        return $emails;
    }
}
