<?php

namespace App\Repositories\Eloquent;

use App\Models\Chat;
use App\Models\User;
use App\Repositories\Contracts\ChatInterface;

class ChatRepository extends BaseRepository implements ChatInterface
{
    public function model()
    {
        return Chat::class;
    }

    public function createParticipans(int $chatId, array $data)
    {
        $this->model
            ->find($chatId)
            ->participants()
            ->sync($data);
    }

    public function getUserChats()
    {
        return auth()->user()
            ->chats()
            ->with(['messages', 'participants'])
            ->get();
    }
}
