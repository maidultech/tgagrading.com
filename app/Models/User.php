<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\ResetPasswordNotification as CustomResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{

    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    public function sendPasswordResetNotification($token)
    {
        $siteName = getSetting()->site_name;
        $this->notify(new CustomResetPasswordNotification($token, $siteName));
    }

    public function sendEmailVerificationNotification()
    {
        $siteName = getSetting()->site_name ?? 'TGA Grading';
        $this->notify(new VerifyEmailNotification($siteName));
    }
//    protected $guarded = [];

//    protected $guard = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'category_id',
        'dob',
        'phone',
        'username',
        'user_code',
        'password',
        'current_plan_id',
        'current_plan_name',
        'current_pan_valid_date',
        'provider',
        'email_verified_at',
        'provider_id',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Always encrypt password when it is updated.
     *
     * @param $value
     * @return string
     */
    // public function setPasswordAttribute($value)
    // {
    //     $this->attributes['password'] = bcrypt($value);
    // }

    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }


    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    function defaultAddress(){
        return $this->hasOne(Address::class,'user_id');
    }
    function addresses(){
        return $this->hasMany(Address::class,'user_id');
    }

    /**
     * Get the fullName
     *
     * @param  string  $value
     * @return string
     */
    public function getFullNameAttribute($value)
    {
        return ucwords($this->name.' '.$this->last_name);
    }

    function order(){
        return $this->hasMany(Order::class,'user_id')->latest();
    }

    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'user_id');
    }

    public function getAvailableCardLimit()
    {
        $subscription = UserSubscription::where('user_id', $this->id)
            ->where('year_start', '<', now())
            ->where('year_end', '>', now())
            ->latest()
            ->first();

        return $subscription ? $subscription->subscription_card_peryear - $subscription->order_card_peryear : 0;
    }

    public function getCurrentSubscription()
    {
        $subscription = UserSubscription::where('user_id', $this->id)
            ->where('year_start', '<', now())
            ->where('year_end', '>', now())
            ->latest()
            ->first();

        return $subscription ? $subscription : null;
    }

    /**
     * Scope a query to only include active
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status',1);
    }

    public function getNextYearSubscriptionInfo()
    {
        $subscription = UserSubscription::where('user_id', $this->id)
            ->where('year_start', '<', now())
            ->where('year_end', '>', now())
            ->latest()
            ->first();

        $availableCardLimit = $subscription ? $subscription->subscription_card_peryear - $subscription->order_card_peryear : 0;

        if ($availableCardLimit <= 0) {
            $nextYearStart = now()->startOfYear()->addYear();
            $nextYearEnd = now()->endOfYear()->addYear();
            
            $nextSubscription = UserSubscription::where('user_id', $this->id)
                ->where('year_start', '<=', $nextYearEnd)
                ->where('year_end', '>=', $nextYearStart)
                ->orderBy('year_start', 'desc')
                ->first();
            
            return $nextSubscription;
        }

        return $subscription;
    }

    function wallets(){
        return $this->hasMany(UserWallet::class,'customer_id');
    }
}
