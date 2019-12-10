<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Jsonable;

use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Wallet;
use App\StoreUserRequest;
use Hash;

class UserControllerAPI extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('page')) {
            return UserResource::collection(User::paginate(5));
        } else {
            return UserResource::collection(User::all());
        }

        /*Caso não se pretenda fazer uso de Eloquent API Resources (https://laravel.com/docs/5.5/eloquent-resources), é possível implementar com esta abordagem:
        if ($request->has('page')) {
            return User::with('department')->paginate(5);;
        } else {
            return User::with('department')->get();;
        }*/
    }

    public function show($id)
    {
        return new UserResource(User::find($id));
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

        if($request->photo != null && $request->hasFile('photo')){
            //setPhoto($user,$request->photo);
            $file = $request->photo;
            $fileNew = \Storage::putFile('public/fotos', $file);
            $filename = basename($fileNew);
            $user->photo = $filename;
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

    public function setPhoto($user, $file)
    {
        //$targetDir = storage_path('app/fotos');
        //$newfilename = $user->id . "_" . uniqid(). '.jpg';
        //File::copy( $file, $targetDir.'/'.$newfilename);
        //$user->photo = $newfilename;
        
        $fileNew = \Storage::putFile('fotos', $file);
        $filename = basename($fileNew);
        $user->photo = $filename;
    }

    public function update(Request $request, $id)
    {
        $request->validate([
                'name' => 'required|min:3|regex:/^[A-Za-záàâãéèêíóôõúçÁÀÂÃÉÈÍÓÔÕÚÇ ]+$/',
                'email' => 'required|email|unique:users,email,'.$id,
                'password' => 'min:3',
                'nif' => 'min:9'
            ]);
        if($request->photo != null && $request->hasFile('photo')){
            $request->validate(['photo' => 'file|image|mimes:jpeg,bmp,png,jpg']);
        }
        $user = User::findOrFail($id);


        $user->update($request->all());
        return new UserResource($user);
    }

    public function getPhotoByEmail($email){
        $photo = User::where('email', '=', $email)->pluck('photo');
        return $photo;
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
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
}
