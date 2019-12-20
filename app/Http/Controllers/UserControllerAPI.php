<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Jsonable;

use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Wallet;
use Illuminate\Support\Facades\Auth;

//use App\StoreUserRequest;
//use Hash;

class UserControllerAPI extends Controller
{
    public function index(Request $request)
    {
        $user = User::select();
        
        //dd($request->name);
        if($request->has("name")){
            $user->where('name', '=', $request->name);
        }
        if($request->has("type")){
            $user->where('type', '=', $request->type);
        }
        if($request->has("email")){
            //$transfer_email = DB::table('wallets')->select('id')->where('email', $request->transfer_email)->get();
            $user->where('email','=',$request->email);
        }
        if($request->has("active")){
            //$transfer_email = DB::table('wallets')->select('id')->where('email', $request->transfer_email)->get();
            $user->where('active','=',$request->active);
        }
        
        return UserResource::collection($user->paginate(5));
    }

    public function show($id)
    {
        return new UserResource(User::find($id));
    }

    public function addUser(Request $request){
        $request->validate([
            'name' => 'required|min:3|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            'email' => 'required|email|unique:users,email',
            'type' => 'required|in:a,o',
            'password' => 'min:3',
        ]);
        if($request->photo != null && $request->hasFile('photo')){
            $request->validate(['photo' => 'file|image|mimes:jpeg,bmp,png,jpg']);
        }

        $user = new User();
        $user->fill($request->all());
        $user->active = 1;
        //$user->remember_token = str_random(10);
        //$user->password = Hash::make($user->password); //cannot use this: 'password' is not in the 'fillable'
        $user->password = bcrypt($request->password);
        //dd($user->photo);
        if($request->photo != null && $request->hasFile('photo')){
            //setPhoto($user,$request->photo);
            $file = $request->photo;
            $fileNew = \Storage::putFile('public/fotos', $file);
            $filename = basename($fileNew);
            $user->photo = $filename;
        }else{
            $user->photo = null;
        }
        $user->save();

        return response()->json(new UserResource($user), 201);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
            'email' => 'required|email|unique:users,email',
            'password' => 'min:3',
            'nif' => 'min:9'
        ]);
        if($request->photo != null && $request->hasFile('photo')){
            $request->validate(['photo' => 'file|image|mimes:jpeg,bmp,png,jpg']);
        }

        $user = new User();
        $user->fill($request->all());
        $user->type = 'u';
        //$user->password = Hash::make($user->password); //cannot use this: 'password' is not in the 'fillable'
        $user->password = bcrypt($request->password);
        dd($user->photo);
        if($request->photo != null && $request->hasFile('photo')){
            //setPhoto($user,$request->photo);
            $file = $request->photo;
            $fileNew = \Storage::putFile('public/fotos', $file);
            $filename = basename($fileNew);
            $user->photo = $filename;
        }else{
            $user->photo = null;
        }

        //savePhoto($user,$request->photoFile);
        /*
        if($request->photoFile != null){
            //Store the file in the storage
            $photo = $request->file('photoFile');
            $path = basename($photo->store('profile','public'));
            $user->photo = basename($path);
        }
        */

        $user->save();
        $wallet = new Wallet();
        $wallet->id = $user->id;
        $wallet->email = $user->email;
        $wallet->balance = 0;
        $wallet->save();

        return response()->json(new UserResource($user), 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                'name' => 'required|min:3|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
                'password' => 'min:3',
                'nif' => 'min:9'
            ]);
        $user = User::findOrFail($id);
        
        if($request->photo != null && $request->hasFile('photo')){
            $request->validate(['photo' => 'file|image|mimes:jpeg,bmp,png,jpg']);
        }

        $userHasPhoto = $user->photo != null;

        if($request->photo != null && $request->hasFile('photo')){
            if($userHasPhoto){
                $oldPhotoName = $user->photo;
                $file = $request->photo;
                //Parse 3rd parameter as the current filname
                $fileNew = \Storage::putFileAs('public/fotos', $file, $oldPhotoName);
                $filename = basename($fileNew);
                $user->photo = $filename;
            }else{
                //Create a new one
                $file = $request->photo;
                $fileNew = \Storage::putFile('public/fotos', $file);
                $filename = basename($fileNew);
                $user->photo = $filename;
            }
            $request->photo = null;
        }
        if($user->type == 'u'){
            $user->nif = $request->nif;
        }
        $user->name = $request->name;
        $user->password = bcrypt($request->password);
        $user->update();


        return new UserResource($user);
    }

    public function getPhotoByEmail($email){
        $photo = User::where('email', '=', $email)->pluck('photo');
        return $photo;
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if($user->type != "u"){
            $user->forceDelete();
            \Storage::disk('public')->delete('fotos/'.$user->photo);

            return response()->json(null, 204);
        }
        abort(403);
        
    }

    public function activeOrInactive(User $user){
        switch ($user->active) {
            case 0:
                $user->active = 1;
                $user->save();
                return response()->json(null, 200);   
            case 1:
                $user->active = 0;   
                $user->save();
                return response()->json(null, 200);   
        };
    }

    public function emailAvailable(Request $request)
    {
        $totalEmail = 1;
        /*
        if ($request->has('email') && $request->has('id')) {
            $totalEmail = DB::table('users')->where('email', '=', $request->email)->where('id', '<>', $request->id)->count();
        } else if ($request->has('email')) {
            $totalEmail = DB::table('users')->where('email', '=', $request->email)->count();
        }
        */
        if ($request->has('email') && $request->has('id')) {
            $totalEmail = User::where('email', '=', $request->email)->where('id', '<>', $request->id)->count();
        } else  if ($request->has('email')) {
            $totalEmail = User::where('email', '=', $request->email)->count();
        }
        return response()->json($totalEmail == 0);
    }

    public function checkNewPassword(Request $request){
        $passwordDb = User::where('id', '=', $request->userid)->pluck('password')->first();
        $same = password_verify($request->newPassword, $passwordDb);
        return $same ? "true" : "false";
    }
}
