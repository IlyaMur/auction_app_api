<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $touches = [
        'chat'
    ];

    protected $fillable = [
        'user_id',
        'chat_id',
        'body',
        'last_read'
    ];

    public function getBodyAttribute($value)
    {
        if ($this->trashed()) {
            return auth()->id() === $this->sender->id
                ? 'You deleted this message'
                : "{$this->sender->name} deleted this message";
        }

        return $value;
    }

    /**
     * Get the chat that owns the Message
     *
     * @return BelongsTo
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Get the sender that sends the Message
     *
     * @return BelongsTo
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
