<?php

namespace App\Models;

use App\Models\Design;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmail;
use App\Notifications\ResetPassword;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\SpatialBuilder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tagline',
        'about',
        'username',
        'location',
        'available_to_hire',
        'formatted_address'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     *
     * @var array<int, string>
     */
    protected $appends = [
        'photo_url',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'location' => Point::class,
    ];

    /**
     * Get all of the comments for the User
     *
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get all of the teams for the User
     *
     * @return BelongsToMany
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)->withTimeStamps();
    }

    /**
     * Get all of the invitations for the User
     *
     * @return HasMany
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'recipient_email', 'email');
    }

    /**
     * Get all of the designs for the User
     *
     * @return HasMany
     */
    public function designs(): HasMany
    {
        return $this->hasMany(Design::class);
    }

    /**
     * The chats that belong to the User
     *
     * @return BelongsToMany
     */
    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class, 'participants');
    }

    /**
     * Get all of the messages for the User
     *
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get the chat with specific user
     *
     * @param int $userId
     */
    public function getChatWithUser($userId)
    {
        return $this->chats()
            ->whereHas(
                'participants',
                fn ($query) => $query->where('user_id', $userId)
            )
            ->first();
    }

    /**
     * Get all of the teams owned by the User
     */
    public function ownedTeams()
    {
        return $this->teams()->where('owner_id', $this->id);
    }

    /**
     * Check if the User is owner of the team
     *
     * @return bool
     */
    public function isOwnerOfTeam($team)
    {
        return $team->owner_id === $this->id;
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function newEloquentBuilder($query): SpatialBuilder
    {
        return new SpatialBuilder($query);
    }

    function calcDistanceBetweenUsers($unit = 'km')
    {
        if (is_null($this->location)) {
            return 'User has no data';
        }

        $lon1 = $this->location->longitude;
        $lon2 = request()->longitude;

        $lat1 = $this->location->latitude;
        $lat2 = request()->latitude;

        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2))
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * cos(deg2rad($lon1 - $lon2));

        $miles = rad2deg(acos($dist)) * 60 * 1.1515;

        return $unit === "km"
            ? round($miles * 1.609344, 2) . ' км'
            : round($miles) . ' миль';
    }

    public function getPhotoUrlAttribute()
    {
        return 'https://www.gravatar.com/avatar/'
            . md5(strtolower($this->email)) . 'jpg?s=200&d=mm';
    }
}
