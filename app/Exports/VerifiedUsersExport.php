<?php

namespace App\Exports;

use App\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class VerifiedUsersExport implements FromCollection
{

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        $users = User::whereNotNull('email_verified_at')->get(['id', 'name', 'last_name', 'email', 'created_at', 'last_online_at', 'metrics']);

        $rows[] = [
            '№',
            'id',
            'имя',
            'фамилия',
            'почтовый адрес',
            'роли',
            'дата регистрации',
            'последний раз был в сети',
            'utm_source',
            'utm_campaign',
            'utm_medium',
            'utm_term_keyword',
            'utm_term_source',
            'utm_content',
            'массив с метриками, на случай если что-то пошло не так'
        ];

        $iterator = 1;
        foreach ($users as $user) {
            $roles = '';

            foreach ($user->getRoleNames() as $role) {
                $roles .= __($role) . "\n";
            }

            $rows[$iterator] = [
                $iterator,
                $user->id,
                $user->name,
                $user->last_name,
                $user->email,
                $roles,
                $user->created_at->format('d.m.Y'),
                $user->last_online_at->format('d.m.Y'),
            ];

            $metrics = $user->metrics;
            if (isset($metrics)) {
                if ($user->metrics !== null) {
                    $arr = json_decode($metrics, true);
                    if (isset($arr['utm_campaign'])) {
                        $rows[$iterator][8] = urldecode($arr['utm_campaign']);
                    } else {
                        $rows[$iterator][8] = '';
                    }

                    if (isset($arr['utm_source'])) {
                        $rows[$iterator][9] = urldecode($arr['utm_source']);
                    } else {
                        $rows[$iterator][9] = '';
                    }

                    if (isset($arr['utm_medium'])) {
                        $rows[$iterator][10] = urldecode($arr['utm_medium']);
                    } else {
                        $rows[$iterator][10] = '';
                    }

                    if (isset($arr['utm_term_keyword'])) {
                        $rows[$iterator][11] = urldecode($arr['utm_term_keyword']);
                    } else {
                        $rows[$iterator][11] = '';
                    }

                    if (isset($arr['utm_term_source'])) {
                        $rows[$iterator][12] = urldecode($arr['utm_term_source']);
                    } else {
                        $rows[$iterator][12] = '';
                    }

                    if (isset($arr['utm_content'])) {
                        $rows[$iterator][13] = urldecode($arr['utm_content']);
                    } else {
                        $rows[$iterator][13] = '';
                    }
                }
            } else {
                $rows[$iterator][8] = '';
                $rows[$iterator][9] = '';
                $rows[$iterator][10] = '';
                $rows[$iterator][11] = '';
                $rows[$iterator][12] = '';
                $rows[$iterator][13] = '';
            }

            $rows[$iterator][14] = $user->metrics;
            $iterator++;
        }

        return collect($rows);
    }

}
