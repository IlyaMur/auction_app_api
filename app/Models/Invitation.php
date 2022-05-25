<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipient_email',
        'sender_id',
        'team_id',
        'token',
    ];

    /**
     * Get the team that owns the Invitation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the Invitation's recipient
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function recipient(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'email', 'recipient_email');
    }

    /**
     * Get the sender that sends the Invitation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function sender(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'sender_id');
    }
}
