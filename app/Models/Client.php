<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    // If language can be nullable in the controller, make the column nullable in the migration too.

    /** @return BelongsTo<Language, Client> */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /** @return BelongsToMany<Interest> */
    public function interests(): BelongsToMany
    {
        // Explicit pivot table improves clarity.
        return $this->belongsToMany(Interest::class, 'client_interest', 'client_id', 'interest_id');
    }

    /** Single primary identity record */
    public function identity(): HasOne
    {
        return $this->hasOne(Identity::class);
    }

    /** Optional: keep a collection of identities as well */
    public function identities(): HasMany
    {
        return $this->hasMany(Identity::class);
    }
}
