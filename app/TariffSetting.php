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
     * @param int $countNewRecords
     * @return RedirectResponse|void
     */
    public static function checkDomainInformationLimits(User $user, int $countNewRecords = 0)
    {
        if ($tariff = $user->tariff()) {

            $tariff = $tariff->getAsArray();
            $count = DomainInformation::where('user_id', '=', $user->id)->count();

            if (array_key_exists('DomainInformation', $tariff['settings'])) {

                if ($count + $countNewRecords >= $tariff['settings']['DomainInformation']['value']) {

                    if ($tariff['settings']['DomainInformation']['message']) {
                        flash()->overlay($tariff['settings']['DomainInformation']['message'], __('Error'))->error();
                    }

                    return redirect()->route('domain.information');
                }
            }
        }
    }

}
