<?php

namespace App\Models;

use App\Models\Traits\JsonData;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasRelatedRecords;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property int $country_id
 * @property bool $is_reseller
 * @property string $document_id
 * @property string $name
 * @property string $phone
 * @property string $email
 * @property int $balance
 * @property int|null $user_id
 * @property int|null $consortium_id
 * @property \Illuminate\Support\Carbon|null $birth_date
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zipcode
 * @property array<array-key, mixed>|null $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Consortium|null $consortium
 * @property-read \App\Models\Country $country
 * @property-read mixed $email_verified_at
 * @property-read mixed $mobile_verified_at
 * @property-read int $qty_tickets
 * @property-read int $winner_tickets
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Login> $logins
 * @property-read int|null $logins_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $tickets
 * @property-read int|null $tickets_count
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Ticket> $winnerTickets
 * @property-read int|null $winner_tickets_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer banned()
 * @method static \Database\Factories\CustomerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer resellers()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer unverified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer verified()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereBirthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereConsortiumId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCountryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereIsReseller($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Customer whereZipcode($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bet> $bets
 * @property-read int|null $bets_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FinancialTransaction> $financialTransactions
 * @property-read int|null $financial_transactions_count
 * @property string|null $ban_reason
 * @property \Illuminate\Support\Carbon|null $banned_at
 * @property-read mixed $is_banned
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @mixin \Eloquent
 */
class Customer extends Model implements \OwenIt\Auditing\Contracts\Auditable
{
    use HasFactory;
    use HasRelatedRecords;
    use JsonData;
    use Notifiable;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'country_id',
        'is_reseller',
        'document_id',
        'name',
        'phone',
        'email',
        'balance',
        'user_id',
        'consortium_id',
        'birth_date',
        'ban_reason',
        'banned_at',
        'address',
        'city',
        'state',
        'zipcode',
        'data',
    ];

    protected $casts = [
        'id' => 'integer',
        'country_id' => 'integer',
        'user_id' => 'integer',
        'is_reseller' => 'boolean',
        'consortium_id' => 'integer',
        'birth_date' => 'date',
        'banned_at' => 'datetime',
        'data' => 'json',
    ];


    protected $appends = [
        'qty_tickets',
        'winner_tickets',
        'email_verified_at',
        'mobile_verified_at',
        'is_banned',
    ];


    //----------
    // Finders
    //----------
    public static function findByDocumentId(string $documentId): ?self
    {
        return self::where('document_id', $documentId)->first();
    }

    public static function findByPhone(string $phone): ?self
    {
        return self::where('phone', $phone)->first();
    }

    public static function findByEmail(string $email): ?self
    {
        return self::where('email', $email)->first();
    }

    //----------
    // Scopes
    //----------
    public function scopeUnverified($query)
    {
        return $query->whereRelation('user', function ($query) {
            $query->whereNull('email_verified_at')
                ->whereNull('mobile_verified_at');
        });
    }

    public function scopeVerified($query)
    {
        return $query->whereRelation('user', function ($query) {
            $query->whereNotNull('email_verified_at')
                ->orWhereNotNull('mobile_verified_at');
        });
    }

    public function scopeBanned($query)
    {
        return $query->whereNotNull('banned_at');
    }

    public function scopeResellers($query)
    {
        return $query->where('is_reseller', true);
    }



    //------------
    // Mutators
    //------------
    protected function balance(): Attribute
    {
        return Attribute::make(
            set: fn($value) => bcmul($value, 100),
            get: fn($value) => bcdiv($value, 100, 2)
        );
    }

    protected function setDocumentIdAttribute($value)
    {
        $this->attributes['document_id'] = strtoupper(substr($value, 0, 20));
    }

    protected function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(substr($value, 0, 100));
    }

    protected function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower(substr($value, 0, 100));
    }

    protected function setAddressAttribute($value)
    {
        $this->attributes['address'] = strtoupper(substr($value, 0, 250));
    }

    //-------------
    // Attributes
    //-------------
    public function getIsBannedAttribute()
    {
        return $this->banned_at !== null;
    }

    public function getEmailVerifiedAtAttribute()
    {
        return $this->user?->email_verified_at;
    }

    public function getMobileVerifiedAtAttribute()
    {
        return $this->user?->mobile_verified_at;
    }

    public function getQtyTicketsAttribute(): int
    {
        return $this->tickets()->count();
    }

    public function getWinnerTicketsAttribute(): int
    {
        return $this->tickets()->where('won', true)->count();
    }

    #----------------
    # Relationships
    #----------------
    public function logins(): MorphMany
    {
        return $this->morphMany(Login::class, 'entity');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function bets(): HasMany
    {
        return $this->hasMany(Bet::class);
    }

    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class);
    }

    public function winnerTickets(): HasMany
    {
        return $this->hasMany(Ticket::class)
            ->where('won', true);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function consortium(): BelongsTo
    {
        return $this->belongsTo(Consortium::class);
    }
}
