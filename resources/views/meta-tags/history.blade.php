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
        </style>

    @endslot

    <div class="row">
        <div class="col-md-12">

            <div id="accordion">

                @foreach($data as $key => $d)
                    <div class="card">
                        <div class="card-header card-header-accordion">
                            <h4 class="card-title">
                                <a class="d-block w-100 collapsed accordion-title" data-toggle="collapse" href="#collapse{{ $key }}" aria-expanded="false">
                                    <i class="expandable-accordion-caret fas fa-caret-right fa-fw"></i> {{ $d->title }}
                                </a>
                            </h4>

                            <div class="card-tools">
                                @foreach($d->error->badge as $error_badge)
                                    {!! implode('', $error_badge) !!}
                                @endforeach
                            </div>

                        </div>

                        <div id="collapse{{ $key }}" class="collapse" data-parent="#accordion" style="">
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>Tag</th>
                                        <th>Content</th>
                                        <th style="width: 40px">Count</th>
                                        <th style="width: 150px">Main problems</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($d->data as $tag => $value)
                                        <tr>
                                            <td><span class="badge badge-success">< {{ $tag }} ></span></td>
                                            <td>
                                                @if(is_array($value))
                                                    <span><textarea class="form-control">{!! implode(', ' . PHP_EOL, $value) !!}</textarea></span>
                                                @else
                                                    <span class="badge badge-danger">false</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(is_array($value))
                                                    <span class="badge bg-warning">{{ count($value) }}</span>
                                                @endif
                                            </td>
                                            <td>{!! implode('<br />', $d->error->main->$tag) !!}</td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>

        </div>

    </div>

    @slot('js')
        <!-- Toastr -->
        <script src="{{ asset('plugins/toastr/toastr.min.js') }}"></script>

        <script>
            toastr.options = {
                "timeOut": "1000"
            };
        </script>


    @endslot


@endcomponent
