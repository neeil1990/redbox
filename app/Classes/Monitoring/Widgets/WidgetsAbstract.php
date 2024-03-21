<?php


namespace App\Classes\Monitoring\Widgets;

use App\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

abstract class WidgetsAbstract implements WidgetInterface
{
    protected $user;
    protected $code = 'CODE';
    protected $name = 'element';
    protected $icon = 'fas fa-home';
    protected $bg = 'bg-info';
    protected $link = '';


    public function widget(): Collection
    {
        /** @var User $user */
        $this->user = Auth::user();

        if(!$widget = $this->getWidgetModel()->where('code', $this->code)->first())
            return collect([]);

        return collect([
            'id' => $widget['id'],
            'code' => $widget['code'],
            'sort' => $widget['sort'],
            'active' => $widget['active'],
            'title' => $this->generateTitle(),
            'description' => $this->generateDesc(),
            'icon' => $this->icon,
            'bg' => $this->bg,
            'link' => $this->link,
        ]);
    }

    public function activation(bool $status): void
    {
        $this->getWidgetModel()->updateOrCreate(['code' => $this->code], ['active' => $status]);
    }

    protected function getWidgetModel()
    {
        /** @var User $user */
        $user = Auth::user();
        if(!$user)
            throw new ModelNotFoundException('Auth user not found.');

        return $user->monitoringWidgets();
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


}
