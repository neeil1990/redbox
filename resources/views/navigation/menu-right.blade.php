<li class="nav-item">
    <a class="nav-link" href="{{ route('balance.index') }}" role="button">
        {{ __('Your balance') }}: {{ $user->balance }}
    </a>
</li>

@if($name)
    <li class="nav-item">
        <a class="nav-link" href="{{ route('tariff.index') }}" role="button">
            {{ __('Your tariff') }}: {{ $name }}
        </a>
    </li>
@endif

<div class="dropdown p-0 m-0 nav-item">
    <span class="dropdown-toggle nav-link" role="button" data-toggle="dropdown" aria-expanded="false">
        Ваши лимиты
    </span>

    <div class="dropdown-menu p-0 m-0">
        <table class="table table-bordered p-0 m-0">
            <thead>
            <tr>
                <th>Task</th>
                <th style="width: 40px">Label</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Update software</td>
                <td><span class="badge bg-danger">55%</span></td>
            </tr>
            <tr>
                <td>Clean database</td>
                <td><span class="badge bg-warning">70%</span></td>
            </tr>
            <tr>
                <td>Cron job running</td>
                <td><span class="badge bg-primary">30%</span></td>
            </tr>
            <tr>
                <td>Fix and squish bugs</td>
                <td><span class="badge bg-success">90%</span></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

