<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;

class UsersController extends Controller
{
    public function show(User $user){
        return view('users.show', compact('user'));
    }

    public function edit(User $user){
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request, ImageUploadHandler $uploader, User $user){
        $data = $request->all();
        //if $request contains file (the 'avatar' is the field name on form)
        //$request->avatar will get that file
        //ImageUploadhandler::save($file, $folder, $file_prefix)
        if($request->avatar){
            $result = $uploader->save($request->avatar, 'avatars', $user->id);
            //in ImageUploadHandler, we make it return false if the extension is not image
            //So, we need to check the $result
            if($result){
                //originally, $data['avatar'] is a file which cannot be stored in database,
                //So, we need to change it to file's path
                $data['avatar'] = $result['path'];
            }
        }

        //UserRequest extends the 'FormRequest'
        //we will use 'FormRequest' to validate the data
        //we design the rules it in UserRequest class (created by us)
        $user->update($data);
        return redirect()->route('users.show', $user->id)->with('success', 'Your profiles has been updated');
    }
}
