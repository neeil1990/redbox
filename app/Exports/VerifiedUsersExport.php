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

            if (isset($user->metrics)) {
                $rows[$iterator][8] = isset($user->metrics['utm_campaign'])
                    ? urldecode($user->metrics['utm_campaign'])
                    : '';

                $rows[$iterator][9] = isset($user->metrics['utm_source'])
                    ? urldecode($user->metrics['utm_source'])
                    : '';

                $rows[$iterator][10] = isset($user->metrics['utm_medium'])
                    ? urldecode($user->metrics['utm_medium'])
                    : '';

                $rows[$iterator][11] = isset($user->metrics['utm_term_keyword'])
                    ? urldecode($user->metrics['utm_term_keyword'])
                    : '';

                $rows[$iterator][12] = isset($user->metrics['utm_term_source'])
                    ? urldecode($user->metrics['utm_term_source'])
                    : '';

                $rows[$iterator][13] = isset($user->metrics['utm_content'])
                    ? urldecode($user->metrics['utm_content'])
                    : '';
            } else {
                $rows[$iterator][8] =
                $rows[$iterator][9] =
                $rows[$iterator][10] =
                $rows[$iterator][11] =
                $rows[$iterator][12] =
                $rows[$iterator][13] = '';
            }

            $rows[$iterator][14] = $user->metrics;
            $iterator++;
        }

        return collect($rows);
    }
}
