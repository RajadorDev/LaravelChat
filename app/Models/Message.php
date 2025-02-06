<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    public function getSender() : User 
    {
        return User::find($this->sender);
    }

    /** @return Message[] */
    public static function getMessagesFrom(User $userA, User $userB, int $start, int $limit) : array 
    {
        return Message::where 
        (
            [
                ['sender', '=', $userA->id],
                ['target', '=', $userB->id]
            ]
        )->orWhere(
            [
                ['sender', '=', $userB->id],
                ['target', '=', $userA->id]
            ]
        )->skip($start)->limit($limit)->get();
    }

    /** @return Message[] */
    public static function getUnreadMessages(User $user, int $start, int $max) : array
    {
        return self::getUnreadQuery($user)->skip($start)->limit($max)->get();
    }

    /** @return int */
    public static function getUnreadMessagesCount(User $user) : int 
    {
        return self::getUnreadQuery($user)->count();
    }

    public static function getUnreadQuery(User $user)
    {
        $unreadQuery = ['read', '=', false];
        return Message::where(
            [
                ['sender', '=', $user->id],
                $unreadQuery
            ]
        )->orWhere(
            [
                ['target', '=', $user->id],
                $unreadQuery
            ]
        );
    }

    public static function serializeToAPIList(array $messages) : array 
    {
        return array_map(
            fn (Message $message) : array => $message->serializeToAPI(),
            $messages
        );
    }

    /** @return array{sender: int, target: int, message: string, read: bool} */
    public function serializeToAPI() : array 
    {
        return [
            'id' => $this->id,
            'sender' => $this->sender,
            'target' => $this->target,
            'message' => $this->message,
            'read' => $this->read
        ];
    }

}
