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
        $users = User::whereNotNull('email_verified_at')->get(['id', 'name', 'last_name', 'email', 'created_at', 'last_online_at']);

        $rows[] = [
            '№',
            'id',
            'имя',
            'фамилия',
            'почтовый адрес',
            'роли',
            'дата регистрации',
            'последний раз был в сети',
        ];

        $iterator = 1;
        foreach ($users as $user) {
            $roles = '';

            foreach ($user->getRoleNames() as $role) {
                $roles = __($role) . "\n";
            }

            $rows[] = [
                $iterator,
                $user->id,
                $user->name,
                $user->last_name,
                $user->email,
                $roles,
                $user->created_at->format('d.m.Y H:m:s'),
                $user->last_online_at->format('d.m.Y H:m:s'),
            ];

        }

        return collect($rows);
    }

}
