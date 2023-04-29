<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Настройка тарифа</h3>
    </div>


    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th>Название</th>
                    <th>code</th>
                    <th>Лимиты</th>
                    <th style="width: 40px">Удалить</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tariffProperties as $tariff)
                <tr>
                    <td>{{ $tariff['setting']['id'] }}</td>
                    <td>{{ $tariff['setting']['name'] }}</td>
                    <td>{{ $tariff['setting']['code'] }}</td>
                    <td>
                        @foreach($tariff['fields'] as $limit)
                            <p>{{ $limit->field['tariff'] }}: <span class="badge bg-primary">{{ $limit['value'] }}</span></p>
                        @endforeach
                    </td>
                    <td class="text-center">
                        <form action="{{ route('user-tariff.destroy', implode(',', $tariff['ids'])) }}" method="post">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center"> Нет данных </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <a href="{{ route('user-tariff.create') }}" class="btn btn-primary float-right">
            Изменить или добавить
        </a>
    </div>

</div>
