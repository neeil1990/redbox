<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Keywords filter') }}</h3>
            </div>

            <div class="card-body">
                <div class="row">
                    <form action="" style="display: contents;">
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('Search engine') }}:</label>
                                <select name="region" class="custom-select" id="searchengines" onchange="this.form.submit()">
                                    @if($project->searchengines->count() > 1)
                                        <option value="">{{ __('All search engine and regions') }}</option>
                                    @endif

                                    @foreach($project->searchengines as $search)
                                        @if($search->id == request('region'))
                                            <option value="{{ $search->id }}" selected>{{ strtoupper($search->engine) }} {{ $search->location->name }} [{{$search->lr}}]</option>
                                        @else
                                            <option value="{{ $search->id }}">{{ strtoupper($search->engine) }} {{ $search->location->name }} [{{$search->lr}}]</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <a class="btn btn-default btn-sm" id="showChartsBlock">Показать графики</a>
                        </div>
                    </form>

                    @if(request('region') || $project->searchengines->count() === 1)
                    <div class="col-4">
                        <div class="form-group">
                            <label>{{ __('Date range') }}:</label>
                            <div class="input-group">

                                <div class="input-group-prepend">
                                      <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                      </span>
                                </div>

                                <input type="text" class="form-control float-right" id="date-range">
                            </div>
                            <!-- /.input group -->
                        </div>
                    </div>
                    @endif

                    <form action="" id="filter" style="display: contents;" onchange='$("#filter").trigger("filtered")'>
                        <div class="col-4">
                            <div class="form-group">
                                <label>{{ __('Groups') }}:</label>
                                {{ Form::select('group', $project->groups->prepend(collect(['name' => __('Selected group'), 'id' => null]))->pluck('name', 'id'), request('group', null), ['class' => 'custom-select']) }}
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
