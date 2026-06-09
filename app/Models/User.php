<?php

namespace App\Models;

use App\Enums\GenderEnum;
use App\Utilities\AvatarGenerator;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'prefix',
        'phone',
        'code',
        'code_expires_at',
        'gender',
        'profession_id',
        'country_id',
        'password',
        'google_id',
        'apple_id',
        'linkedin_id',
        'avatar',
        'verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];
    public function playerIds()
    {
        return $this->hasMany(PlayerId::class);
    }
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified_at'     => 'datetime',
        'code_expires_at' => 'datetime',
        'password'        => 'hashed',
        'gender'          => GenderEnum::class,
        'profession_id'   => 'integer',
        'country_id'      => 'integer',
    ];

    protected $appends = ['full_phone', 'gender_label'];

    /**
     * Get the profession associated with the user.
     */
    public function profession(): BelongsTo
    {
        return $this->belongsTo(Profession::class);
    }

    /**
     * Get the country associated with the user.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the freelancer profile associated with the user.
     */
    public function freelancer(): HasOne
    {
        return $this->hasOne(Freelancer::class);
    }

    /**
     * Get the device tokens for the user.
     */
    public function deviceTokens(): HasMany
    {
        return $this->hasMany(DeviceToken::class);
    }

    /**
     * Get the user notifications for the user.
     */
    // public function userNotifications(): HasMany
    // {
    //     return $this->hasMany(UserNotification::class);
    // }

    public function userWishlist(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get all notifications for the user, including general notifications.
     */
    public function notifications()
    {
        return Notification::where('is_general', true)
            ->orWhereHas('userNotifications', function ($query) {
                $query->where('user_id', $this->id);
            });
    }

    public function getFullPhoneAttribute(): string
    {
        return $this->prefix . $this->phone;
    }

    public function getGenderLabelAttribute(): string
    {
        return $this->gender?->label() ?? '-';
    }
    public function languages(): HasMany
    {
        return $this->hasMany(UserLanguage::class);
    }
    public function notification(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function ticketMessages()
    {
        return $this->morphMany(TicketMessage::class, 'messageable');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }
    public function quotationComments()
    {
        return $this->hasMany(QuotationComment::class);
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'freelancer_cateogries', 'user_id', 'category_id');
    }
    public function certificates()
    {
        return $this->hasMany(FreelancerCertificate::class, 'user_id');
    }


    public function categories1()
    {
        return $this->belongsToMany(Category::class, 'freelancer_cateogries');
    }

    public function sentMessages()
    {
        return $this->hasMany(ChatMessage::class, 'sender_id');
    }

    public function chatsAsUserOne()
    {
        return $this->hasMany(Chat::class, 'user_id_one');
    }

    public function chatsAsUserTwo()
    {
        return $this->hasMany(Chat::class, 'user_id_two');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'user_id');
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class, 'user_id');
    }
    public function bank()
    {
        return $this->hasOne(FreelancerBank::class, 'freelancer_id');
    }

    public function getChatProfile()
    {
        // Company account (freelancer with company)
        if ($this->freelancer && $this->freelancer->company_id) {
            $company = $this->freelancer->company;

            return [
                'id'       => $this->id,
                'username' => $company->translation->name,
                'avatar'   => $company->logo ? url($company->logo) : null,
                'role'     => 'company',
            ];
        }

        // Freelancer
        if ($this->freelancer) {
            return [
                'id'       => $this->id,
                'username' => $this->username,
                'avatar'   => $this->avatar ? url($this->avatar) : null,
                'role'     => 'freelancer',
            ];
        }

        // Client
        return [
            'id'       => $this->id,
            'username' => $this->username,
            'avatar'   => $this->avatar ? url($this->avatar) : null,
            'role'     => 'client',
        ];
    }

    public function ratingsGiven()
    {
        return $this->hasMany(UserRating::class, 'rater_id');
    }

    public function ratingsReceived()
    {
        return $this->hasMany(UserRating::class, 'ratee_id');
    }
}
