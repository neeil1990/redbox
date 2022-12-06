<?php

namespace App;

use App\Classes\Tariffs\Facades\Tariffs;
use App\Notifications\BrokenDomainNotification;
use App\Notifications\BrokenLinkNotification;
use App\Notifications\DomainInformationNotification;
use App\Notifications\RegisterPasswordEmail;
use App\Notifications\RegisterVerifyEmail;
use App\Notifications\RepairDomainNotification;
use App\Notifications\sendNotificationAboutChangeDNS;
use App\Notifications\sendNotificationAboutExpirationRegistrationPeriod;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'balance', 'name', 'last_name', 'email', 'lang', 'password', 'last_authorization', 'telegram_token', 'metrics'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_online_at' => 'datetime'
    ];

    /**
     * Delete no verify users
     * @var int
     */
    protected $delete = 30;

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {

        $this->notify(new RegisterVerifyEmail);
    }

    /**
     * Send the password reset notification.
     *
     * @return void
     */
    public function sendProfilePasswordResetNotification($request)
    {
        $this->notify(new RegisterPasswordEmail($request));
    }

    /**
     * @param $request
     * @param $link
     */
    public function sendBrokenLinkNotification($request, $link)
    {
        $this->notify(new BrokenLinkNotification($request, $link));
    }

    /**
     * @param $project
     */
    public function brokenDomainNotification($project)
    {
        $this->notify(new BrokenDomainNotification($project));
    }

    /**
     * @param $project
     */
    public function repairDomainNotification($project)
    {
        $this->notify(new RepairDomainNotification($project));
    }

    /**
     * @param $project
     */
    public function DomainInformationNotification($project)
    {
        $this->notify(new DomainInformationNotification($project));
    }

    /**
     * @param $project
     */
    public function sendNotificationAboutChangeDNS($project)
    {
        $this->notify(new sendNotificationAboutChangeDNS($project));
    }

    /**
     * @param $project
     * @param $diffInDays
     */
    public function sendNotificationAboutExpirationRegistrationPeriod($project, $diffInDays)
    {
        $this->notify(new sendNotificationAboutExpirationRegistrationPeriod($project, $diffInDays));
    }

    /**
     * Input value roles for edit users
     *
     * @return mixed
     */
    public function getRoleAttribute()
    {
        return $this->roles->pluck('id');
    }

    public function session()
    {
        return $this->hasOne('App\Session')->orderBy('last_activity', 'desc');
    }

    /**
     * @return Classes\Tariffs\Tariff|mixed|null
     */
    public function tariff()
    {
        return (new Tariffs())->getTariffByUser($this);
    }

    /**
     * @return HasMany
     */
    public function passwords()
    {
        return $this->hasMany(PasswordsGenerator::class)
            ->orderBy('id', 'desc')
            ->latest('created_at')
            ->limit(30);
    }

    public function monitoringProjects()
    {
        return $this->hasMany(MonitoringProject::class);
    }

    public function behaviors()
    {
        return $this->hasMany(Behavior::class);
    }

    public function balances()
    {
        return $this->hasMany(Balance::class);
    }

    public function pay()
    {
        return $this->hasMany(TariffPay::class);
    }

    public function metaTags()
    {
        return $this->hasMany(MetaTag::class);
    }

    public function deleteNoVerify()
    {
        $this->where('email_verified_at', '=', null)
            ->where('created_at', '<=', Carbon::now()->subDays($this->delete))
            ->delete();
    }

    public function projects()
    {
        return $this->hasMany(ProjectTracking::class);
    }

    /**
     * @return bool
     */
    public static function isUserAdmin(): bool
    {
        foreach (Auth::user()->role as $role) {
            if ($role == '1' || $role == '3') {
                return true;
            }
        }

        return false;
    }

    /**
     * @return HasMany
     */
    public function project(): HasMany
    {
        return $this->hasMany(ProjectRelevanceHistory::class);
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->last_name}";
    }
}
