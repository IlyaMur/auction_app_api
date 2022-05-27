<?php

namespace App\Http\Controllers\Chats;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Http\Resources\MessageResource;
use App\Repositories\Contracts\ChatInterface;
use App\Repositories\Contracts\MessageInterface;
use App\Repositories\Eloquent\Criteria\WithTrashed;

class ChatsController extends Controller
{
    public function __construct(
        protected ChatInterface $chats,
        protected MessageInterface $messages
    ) {
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'recipient' => ['required'],
            'body' => ['required'],
        ]);

        $recipient = $request->recipient;
        $user = auth()->user();
        $body = $request->body;

        // Check if there is an existing chat
        // between the auth user and the recipient
        $chat = $user->getChatWithUser($recipient);

        if (!$chat) {
            $chat = $this->chats->create([]);
            $this->chats->createParticipans($chat->id, [$user->id, $recipient]);
        }

        $message = $this->messages->create([
            'user_id' => $user->id,
            'chat_id' => $chat->id,
            'body' => $body,
            'last_read' => null
        ]);

        return new MessageResource($message);
    }

    public function getUserChats()
    {
        return ChatResource::collection(
            $this->chats->getUserChats()
        );
    }

    public function getChatMessages($id)
    {
        $this->authorize($this->chats->find($id));

        // Get messages include soft deleted
        $messages = $this->messages->withCriteria([
            new WithTrashed()
        ])->findWhere('chat_id', $id);

        return MessageResource::collection($messages);
    }

    public function markAsRead($id)
    {
        $this->chats
            ->find($id)
            ->markAsReadForUser(auth()->id());

        return response()->json(['message' => 'successful']);
    }

    public function destroyMessage($id)
    {
        $message = $this->messages->find($id);

        $this->authorize('delete', $message);

        $message->delete();

        return response()->json(['message' => 'successful']);
    }
}
