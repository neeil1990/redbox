<?php

namespace App;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class TariffSetting extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'message',
    ];

    public function fields()
    {
        return $this->hasMany(TariffSettingValue::class);
    }

    /**
     * @param User $user
     * @return bool
     */
    public static function checkDomainInformationLimits(User $user): bool
    {
        if (isset($request->domains)) {
            $countNewRecords = count(explode("\r\n", $request->domains));
        } else {
            $countNewRecords = 0;
        }

        if ($tariff = $user->tariff()) {

            $tariff = $tariff->getAsArray();
            $count = DomainInformation::where('user_id', '=', $user->id)->count();

            if (array_key_exists('DomainInformation', $tariff['settings'])) {

                if ($count + $countNewRecords >= $tariff['settings']['DomainInformation']['value']) {

                    return true;
                }
            }
        }

        return false;
    }

}
