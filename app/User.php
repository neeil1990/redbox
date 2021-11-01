<?php

namespace App;

use App\Notifications\BrokenDomenNotification;
use App\Notifications\BrokenLinkNotification;
use App\Notifications\RegisterPasswordEmail;
use App\Notifications\RegisterVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
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
        'name', 'last_name', 'email', 'lang', 'password', 'last_authorization'
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
    public function brokenDomenNotification($project)
    {
        $this->notify(new BrokenDomenNotification($project));
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
     * @return HasMany
     */
    public function passwords()
    {
        return $this->hasMany(PasswordsGenerator::class)
            ->orderBy('id', 'desc')
            ->latest('created_at')
            ->limit(30);
    }

    public function behaviors()
    {
        return $this->hasMany(Behavior::class);
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
}
