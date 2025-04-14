<div class="col-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Подтвердить получение внешних проектов</h3>
        </div>

        <div class="card-body p-0">
            <table class="table">
                <thead>
                <tr>
                    <th>Название</th>
                    <th>Сайт</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($foreignProject as $project)
                    <tr data-id="{{$project['id']}}">
                        <td>{{ $project['name'] }}</td>
                        <td>{{ $project['url'] }}</td>
                        <td class="text-right py-0 align-middle">
                            <div class="btn-group btn-group-sm">
                                <a href="#" class="btn btn-info approve-project">{{ __('Approve') }}</a>
                                <a href="#" class="btn btn-danger cancel-project">{{ __('Cancel') }}</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
