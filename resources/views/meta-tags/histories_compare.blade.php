@component('component.card', ['title' => __('History')])

    @slot('css')
        <!-- Toastr -->
        <link rel="stylesheet" href="{{ asset('plugins/toastr/toastr.min.css') }}">

        <style>
            a.accordion-title {
                color: #212529;
                font-size: 1rem;
            }

            .card-header-accordion:hover {
                background-color: rgba(0,0,0,.075);
            }

            [data-toggle=collapse] i.expandable-accordion-caret {
                transition: -webkit-transform .3s linear;
                transition: transform .3s linear;
                transition: transform .3s linear,-webkit-transform .3s linear;
            }

            [data-toggle=collapse][aria-expanded=true] i.expandable-accordion-caret[class*=right] {
                -webkit-transform: rotate(
                        90deg);
                transform: rotate(
                        90deg);
            }

            .MetaTagsProject,
            .MetaTagsPages {
                background: oldlace;
            }
        </style>

    @endslot

    <div class="row">
        <div class="col-md-12">

            <div class="form-group">
                <label for="filter">{{ __('Filter') }}</label>
                <select class="custom-select" id="filter">
                    <option value="all">{{ __('All') }}</option>
                    @foreach($filter as $f)
                        <option value="{{$f}}">{{$f}}</option>
                    @endforeach
                </select>
            </div>

            <div id="accordion">
                @foreach($collection as $url => $item)
                    <div class="card "
                         @if(isset($item['badge']))
                             data-tags="@foreach($item['badge'] as $name => $errors){!! implode(',', array_keys($errors)) !!},@endforeach"
                         @endif
                    >
                        <div class="card-header">
                            <h4 class="card-title ">
                                <a class="d-block w-100 collapsed accordion-title" data-toggle="collapse" href="#collapse{{$loop->index}}" aria-expanded="false">
                                    <i class="expandable-accordion-caret fas fa-caret-right fa-fw"></i> {{ $url }}
                                </a>
                            </h4>

                            <div class="card-tools">

                                @if(isset($item['badge']))
                                    @foreach($item['badge'] as $name => $errors)
                                        {{ $name }} :
                                        @foreach($errors as $tag => $error)
                                            <span data-tags="{{$tag}}">{!! implode('', $error) !!}</span>
                                        @endforeach
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div id="collapse{{$loop->index}}" class="collapse" data-parent="#accordion" style="">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr align="center">
                                                <th colspan="4">{{$item['card']['date']}} ({{$item['card']['id']}})</th>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Tag') }}</th>
                                                <th>{{ __('Content') }}</th>
                                                <th style="width: 40px">{{ __('Count') }}</th>
                                                <th style="width: 150px">{{ __('Main problems') }}</th>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            @foreach($item['card']['tags'] as $tag => $value)

                                                <tr>
                                                    <td><span class="badge badge-success">< {{ $tag }} ></span></td>
                                                    <td>
                                                        @if(is_array($value))
                                                            <span>
                                                                <textarea class="form-control">{!! implode( ', ' . PHP_EOL, $value ) !!}</textarea>
                                                            </span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $value }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(is_array($value))
                                                            <span class="badge bg-warning">{{ count($value) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {!! implode('<br />', $item['card']['error']->$tag) !!}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @if(isset($item['card_compare']))
                                        <div class="col-md-6">
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr align="center">
                                                <th colspan="4">{{$item['card_compare']['date']}} ({{$item['card_compare']['id']}})</th>
                                            </tr>
                                            <tr>
                                                <th>{{ __('Tag') }}</th>
                                                <th>{{ __('Content') }}</th>
                                                <th style="width: 40px">{{ __('Count') }}</th>
                                                <th style="width: 150px">{{ __('Main problems') }}</th>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            @foreach($item['card_compare']['tags'] as $tag => $value)

                                                <tr @if($item['card']['tags']->$tag !== $value) style="background-color: #00800020" @endif>
                                                    <td><span class="badge badge-success">< {{ $tag }} ></span></td>
                                                    <td>
                                                        @if(is_array($value))
                                                            <span>
                                                                <textarea class="form-control">{!! implode( ', ' . PHP_EOL, $value ) !!}</textarea>
                                                            </span>
                                                        @else
                                                            <span class="badge badge-danger">{{ $value }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(is_array($value))
                                                            <span class="badge bg-warning">{{ count($value) }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {!! implode('<br />', $item['card_compare']['error']->$tag) !!}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <a href="{{ route('meta.history.export_compare', [request('id'), request('id_compare')]) }}" class="btn btn-info btn-sm">
                <i class="fas fa-file-download"></i>
                {{ __('Export') }}
            </a>

        </div>

    </div>

    @slot('js')
        <!-- Toastr -->
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>

        <script>
            toastr.options = {
                "timeOut": "1000"
            };

            $('#filter').change(function(){
                var self = $(this);
                var selected = self.val();

                if(selected === 'all'){
                    $('#accordion .card').show();
                    return;
                }

                $('#accordion .card').hide();

                $('#accordion .card').each(function(i, el){
                    var item = $(el);
                    var str = item.data('tags');
                    if(str){
                        var tags = str.split(',');

                        if(tags.find(el => el === selected))
                            item.show();
                    }
                });

            });

            $('#accordion .card').each(function(i, el){
                let self = $(el);

                if(self.find('tr[style]').length)
                    self.addClass('card-warning')
            });

        </script>


    @endslot


@endcomponent
