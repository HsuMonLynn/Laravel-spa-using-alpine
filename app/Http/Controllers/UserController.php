<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(){
        $users = User::all()->toArray();
        return $users;
    }

    public function store(Request $request){

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,jfif|max:2048'
        ]);

        $user = new User([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make("password")
        ]);
        $user->image = $request->file('image')->store('images', 'public');
        $user->save();

        session()->flash('success', 'A User was created.');
        return response()->json([
            'success', 'A User was created.',
        ]);
;
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'email' =>  'required|string|email|max:255|',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,jfif|max:2048'
        ]);
        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make('password');

        if ($request->file('image')) {
          $user->image = $request->file('image')->store('images', 'public');
        }
        $user->update();

        session()->flash('success', 'A User was updated.');
        return response()->json([
            'success', 'A User was updated.',
        ]);
    }

    public function destroy($id){
  
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['data' => $user], 200);
    
      }
    
}
