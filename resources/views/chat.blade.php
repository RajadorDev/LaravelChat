@extends('layouts.base')

@section('title', 'Chat')

@section('head_content')
    @vite(['resources/css/chat.css', 'resources/js/chat.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection

@section('content')


<div class="chat-selector-nav secondary-bg">
    <div class="perfil-info">
        <img src="{{ $user->getPerfilImage() }}" alt="perfil-image" class="user-perfil-icon">
        <p class="perfil-name">
            {{ $user->name }}
            @if ($user->isOnline())
                <x-profile-span-online></x-profile-span-online>
            @else 
                <x-profile-span-offline></x-profile-span-offline>
            @endif
        </p>
    </div>
    <nav class="chat-friends-nav">
        <ul class="chat-nav-list">
            <li class="chat-nav-option nav-item" navid="friends">Amigos</li>
            <li class="chat-nav-option nav-item" navid="requests">Pendente</li>
            <li class="chat-nav-option nav-item" navid="addfriends">Adicionar</li>
        </ul>
        <div class="nav-box">
                
        </div>
    </nav>
    
</div>

<div class="chat-box">
    <div class="chat-nav secondary-bg">
        <div class="chat-info">

        </div>
    </div>
    <div class="chat-messages">
        <div class="bubble-box">
            <p class="user-message chat-bubble">Oi tudo bem?</p>
        </div>
        <div class="bubble-box">
            <p class="friend-message chat-bubble">Eae?</p>
        </div>
    </div>
    <div class="sender-box secondary-bg">
        <div class="input-box">
            <input type="text" class="text-input" placeholder="Type a message">
            <button class="send-message-button btn">
                <i class="bi bi-send"></i>
            </button>
        </div>
    </div>
</div>

@endsection

