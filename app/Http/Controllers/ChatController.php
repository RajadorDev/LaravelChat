<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    

    public function render()
    {
        $user = User::get();
        $friends = $user->getFriendUsers();
        return view('chat', [
            'user' => $user,
            'friends' => $friends
        ]);
    }

    public function heartbeat() : void 
    {
        User::get()->heartbeat();
    }

}
