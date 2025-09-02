<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /**
     *
     * @use HasFactory<\Database\Factories\UserFactory>
     */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token'
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
            'password' => 'hashed'
        ];
    }
    
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<TRelatedModel,
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
    
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<TRelatedModel,
     */
    public function interests()
    {
        return $this->belongsToMany(Interest::class);
    }
    
    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<TRelatedModel,
     */
    public function identities()
    {
        return $this->hasMany(Identity::class);
    }
}
