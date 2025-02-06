<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public static function get() : User
    {
        return Auth::user();
    }

    public function unserializeFriendsIds() : void
    {
        if (is_string($this->friends) && $this->friends != 'null')
        {
            $this->friends = json_decode($this->friends, true);
        } else {
            $this->friends = [];
        }
    }

    public function serializeFriendsIds() : void 
    {
        $this->friends = json_encode($this->friends);
    }

    public function addFriend(User $friend) : bool  
    {
        $friendId = $friend->id;
        if (!$this->hasFriend($friendId))
        {
            $this->friends[] = $friendId;
            $this->save();
            return true;
        }
        return false;
    }

    public function removeFriend(User $friend) : void 
    {
        if ($friend->id != $this->id)
        {
            $friend->removeFriend($friend);
        }
        $friendId = $friend->id;
        $friendIndex = array_search($friendId, $this->friends);
        if ($friendIndex !== false)
        {
            unset($this->friends[$friendIndex]);
            $this->friends = array_values($this->friends);
            $this->save();
        }
    }

    public function hasFriend(int $friendId) : bool 
    {
        return in_array($friendId, $this->friends);
    }

    /** @return User[] */
    public function getFriendUsers(int $start = 0, int $limit = 20) : array 
    {
        $friends = $this->friends;
        $friends = array_slice($friends, $start, $limit);
        return array_filter(
            array_map(
                fn (int | string $id) : User => User::find($id),
                $this->friends
            ), fn (? User $user) : bool => $user instanceof User
        );
    }

    public function save(array $options = [])
    {
        $this->serializeFriendsIds();
        return parent::save($options);
    }

    public function canTalkWith(User $user) : bool 
    {
        return Gate::allows('canSendMessage', [$this, $user]);
    }

    protected static function booted()
    {
        static::retrieved(
            function (User $model) : void {
                $model->unserializeFriendsIds();
            }
        );
    }

    public function getPerfilImage() : string 
    {
        if ($profilePath = $this->getAttribute('profile_image'))
        {
            return asset('account/perfil/' . $profilePath);
        }
        return self::getDefaultPerfilImage();
    }

    public static function getDefaultPerfilImage() : string 
    {
        return asset('account/perfil/blank.png');
    }

    public function getStatus() : Status
    {
        return Status::getFromTime($this->heartbeat);
    }

    public function getStatusName() : string 
    {
        return $this->getStatus()->value;
    }

    public function isOnline() : bool 
    {
        return $this->getStatus() === Status::ONLINE;
    }

    public function heartbeat() : void 
    {
        $this->heartbeat = microtime(true);
        $this->save();
    }

    /** @return Message[] */
    public function getFriendMessages(User $with, int $start, int $limit) : array 
    {
        return Message::getMessagesFrom($with, $this, $start, $limit);
    }

    /** @return Message[] */
    public function getUnreadMessages(int $skip, int $max) : array 
    {
        return Message::getUnreadMessages(
            $this,
            $skip,
            $max
        );
    }

    public function getUnreadCount() : int 
    {
        return Message::getUnreadMessagesCount($this);
    }

    public function serializeUserInfoAPI() : array 
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'lastheartbeat' => $this->heartbeat,
            'isOnline' => $this->isOnline(),
            'perfil_image' => $this->getPerfilImage()
        ];
    }

    public function getFriendsSerialized(int $skip, int $limit) : array {
        $friends = $this->getFriendUsers($skip, $limit);
        return array_map(
            function (User $user) : array {
                return [
                    'user' => $user->serializeUserInfoAPI(),
                    'messages' => $user->getFriendMessages($this, 0, 20)
                ];
            }, $friends
        );
    }

}
