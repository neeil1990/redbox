<?php

namespace App;

use App\Classes\Tariffs\Facades\Tariffs;
use App\Mail\VerifyEmail;
use App\Notifications\BrokenDomainNotification;
use App\Notifications\BrokenLinkNotification;
use App\Notifications\DomainInformationNotification;
use App\Notifications\MonitoringLimitExhaustedNotification;
use App\Notifications\RegisterPasswordEmail;
use App\Notifications\RepairDomainNotification;
use App\Notifications\sendNotificationAboutChangeDNS;
use App\Notifications\sendNotificationAboutExpirationRegistrationPeriod;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Url\Url as SpatieUrl;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'balance', 'name', 'last_name', 'email', 'lang', 'password', 'last_authorization', 'telegram_token', 'metrics', 'statistic'
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
        'last_online_at' => 'datetime',
        'metrics' => 'json',
    ];

    /**
     *
     *
     * @var array
     */
    protected $with = [
        'pay',
        'roles',
    ];

    /**
     * Delete no verify users
     * @var int
     */
    protected $delete = 30;

    public function getImageAttribute($value)
    {
        $public = Storage::disk('public');
        if(!Storage::exists($value))
            return $public->url('avatar/user-icon.png');

        return $public->url($value);
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $user = User::latest()->first();
        $verificationUrl = $this->verificationUrl($user);
        $verificationCode = $this->verificationCode($verificationUrl);

        Mail::to($user->email)->send(new VerifyEmail($user, $verificationUrl, $verificationCode));
    }

    private function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            \Illuminate\Support\Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            ['id' => $notifiable->getKey()]
        );
    }

    private function verificationCode($code)
    {
        $code = SpatieUrl::fromString($code);
        $code = $code->getQueryParameter('expires');
        session(['verificationCode' => $code]);

        return $code;
    }

    /**
     * Send the password reset notification.
     *
     * @return void
     */
    public function sendProfilePasswordResetNotification($request, $user)
    {
        $this->notify(new RegisterPasswordEmail($request, $user));
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

    public function sendMonitoringLimitExhaustedNotification()
    {
        $this->notify(new MonitoringLimitExhaustedNotification());
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

    public function tariffSettings()
    {
        return $this->hasMany(TariffSettingUserValue::class);
    }

    /**
     * @return HasMany
     */
    public function passwords()
    {
        return $this->hasMany(PasswordsGenerator::class)
            ->orderBy('id', 'desc')
            ->latest('created_at');
    }

    public function monitoringProjects()
    {
        return $this->belongsToMany(MonitoringProject::class)->withPivot('admin', 'approved', 'status');
    }

    public function monitoringProjectsWithDataTable()
    {
        return $this->monitoringProjects()->wherePivot('approved', 1)->with('users')
            ->leftJoin(DB::raw('monitoring_data_table_columns_projects as m_dt'), 'monitoring_projects.id', '=', 'm_dt.monitoring_project_id')
            ->select('monitoring_projects.*',
                'm_dt.words',
                'm_dt.middle',
                'm_dt.top3',
                'm_dt.diff_top3',
                'm_dt.top5',
                'm_dt.diff_top5',
                'm_dt.top10',
                'm_dt.diff_top10',
                'm_dt.top30',
                'm_dt.diff_top30',
                'm_dt.top100',
                'm_dt.diff_top100',
                'm_dt.mastered',
                'm_dt.mastered_percent',
                'm_dt.mastered_info',
                'm_dt.updated_at'
            );
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

    public function backlingProjects()
    {
        return $this->hasMany(ProjectTracking::class);
    }

    /**
     * @return bool
     */
    public static function isUserAdmin(): bool
    {
        if (Auth::check()) {
            foreach (Auth::user()->role as $role) {
                if ($role == '1' || $role == '3') {
                    return true;
                }
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
    public function getFullNameAttribute(): string
    {
        return "{$this->name} {$this->last_name}";
    }
}
