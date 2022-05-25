<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_id',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        // add current user as team member, when team was created
        static::created(function ($team) {
            $team->members()->attach(auth()->user());
        });

        // delete all records from the pivot table when team was deleted
        static::deleting(function ($team) {
            $team->members->sync([]);
        });
    }

    /**
     * Get the owner that owns the Team
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * The members that belong to the Team
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Get all of the designs for the Team
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function designs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Design::class);
    }

    /**
     * Check if the Team has particular User
     *
     * @return bool
     */
    public function hasUser($user)
    {
        return (bool) $this->members()
            ->where('user_id', $user->id)
            ->first();
    }
}
