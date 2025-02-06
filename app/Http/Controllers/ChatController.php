<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    

    public function render()
    {
        $user = User::get();
        $friends = $user->getFriendUsers();
        return view('chat', [
            'user' => $user
        ]);
    }

    public function heartbeat() : void 
    {
        User::get()->heartbeat();
    }

    public function friendsAPI(Request $request)
    {
        $loaded = $request->get('loaded', 0);
        return json_encode(User::get()->getFriendsSerialized($loaded, $loaded));
    }

    public function loadMessages(Request $request)
    {
        $loaded = $request->get('loaded', 0);
        $friendId = $request->get('friend');
        $length = 30;
        $messages = User::get()->getFriendMessages(User::find($friendId), $loaded, $length);
        return json_encode(
            [
                'resquest_length' => $length,
                'messages' => $messages
            ]
        );
    }

}
