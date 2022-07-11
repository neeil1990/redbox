<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Keywords filter') }}</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <form action="" style="display: contents;">
                        <div class="col-3">
                            <div class="form-group">
                                <label>{{ __('Search engine') }}:</label>
                                <select name="region" class="custom-select" id="searchengines" onchange="this.form.submit()">
                                    @foreach($project->searchengines as $search)
                                        @if($search->id == request('region'))
                                            <option value="{{ $search->id }}" selected>[{{$search->lr}}] {{ $search->location->name }}</option>
                                        @else
                                            <option value="{{ $search->id }}">[{{$search->lr}}] {{ $search->location->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>

                    <form action="" id="filter" style="display: contents;">
                        <div class="col-3">
                            <div class="form-group">
                                <label>{{ __('Groups') }}:</label>
                                {{ Form::select('group', $project->groups->prepend(collect(['name' => __('Selected group'), 'id' => null]))->pluck('name', 'id'), null, ['class' => 'custom-select', 'onchange' => '$("#filter").trigger("filtered")']) }}
                            </div>
                        </div>

                        <div class="col-3">
                            <div class="form-group">
                                <label>{{ __('Date range') }}:</label>
                                <div class="input-group">

                                    <div class="input-group-prepend">
                                      <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                      </span>
                                    </div>

                                    <input name="checkbox" type="text" class="form-control float-right" id="date-range">
                                </div>
                                <!-- /.input group -->
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
