<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    use HasFactory;

    /**
     * Get all of the participants for the Chat
     *
     * @return BelongsToMany
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'participants');
    }

    /**
     * Get all of the messages for the Chat
     *
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get latest message for the Chat
     */
    public function getLatestMessageAttribute()
    {
        return $this->messages()
            ->latest()
            ->first();
    }

    /**
     * Check if the chat unread for the User
     *
     * @param int $userId
     */
    public function isUnreadForUser($userId)
    {
        return (bool) $this->messages()
            ->whereNull('last_read')
            ->where('user_id', '<>', $userId)
            ->count();
    }

    /**
     * Mark Chat as read for the User
     *
     * @param int $userId
     */
    public function markAsReadForUser($userId)
    {
        $this->messages()
            ->whereNull('last_read')
            ->where('user_id', '<>', $userId)
            ->update(['last_read' => now()]);
    }
}
