@component('component.card', ['title' => __('Behavior')])

    @slot('css')
        <!-- CodeMirror -->
        <link rel="stylesheet" href="{{ asset('plugins/codemirror/codemirror.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/codemirror/theme/monokai.css') }}">
        <style>
            .behavior {
                background: oldlace;
            }
        </style>
    @endslot

    <div class="row">
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="far fa-star"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ __('Project') }}</span>
                    <span class="info-box-number">{{ $behavior->domain }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="far fa-star"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ __('Minutes') }}</span>
                    <span class="info-box-number">{{ $behavior->minutes }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="far fa-star"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ __('Clicks') }}</span>
                    <span class="info-box-number">{{ $behavior->clicks }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="far fa-star"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">{{ __('Pages') }}</span>
                    <span class="info-box-number">{{ $behavior->pages }}</span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
    </div>

    <div class="row">
        @include('behavior.buttons')
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title text-bold">{{ $behavior->domain }}</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>{{ __('Phrase') }}</th>
                            <th>{{ __('Code') }}</th>
                            <th style="width: 20px"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($behavior->phrases as $phrase)
                            <tr>
                                <td>{{ $phrase->phrase }}</td>
                                <td><span class="badge @if($phrase->status) bg-success @else bg-danger @endif">{{ $phrase->code }}</span></td>
                                <td class="text-center">
                                    <a href="#" class="text-red phrase-destroy" data-id="{{ $phrase->id }}">
                                        <i class="fas fa-ban"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
        <div class="col-md-6">
            @if (session('update_site_code'))
                <div class="alert alert-info" role="alert">
                    {{ session('update_site_code') }}
                </div>
            @endif
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Insert to your site before closed body') }}</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <textarea id="code">
<!-- PRIME VISIT-->
<script defer>
    $.getScript("{{ request()->getSchemeAndHttpHost() }}/client/js/prime.visit.js")
        .done(function(script, textStatus) {
            let paramVisit = new Visit('{{ $params }}');
            paramVisit.handle();
        }).fail(function(jqxhr, settings, exception) {
        console.log('prime.visit error!');
    });
</script>
                    </textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @include('behavior.buttons')
    </div>

    @slot('js')
        <!-- CodeMirror -->
        <script src="{{ asset('plugins/codemirror/codemirror.js') }}"></script>
        <script src="{{ asset('plugins/codemirror/mode/css/css.js') }}"></script>
        <script src="{{ asset('plugins/codemirror/mode/xml/xml.js') }}"></script>
        <script>
            $(function () {
                // CodeMirror
                CodeMirror.fromTextArea(document.getElementById("code"), {
                    mode: "htmlmixed",
                    theme: "monokai",
                });

                $('.phrase-destroy').click(function (e) {
                    e.preventDefault();

                    let self = $(this);
                    let id = self.data('id');

                    axios.delete(`/behavior/phrase/${id}`).then(function (response) {
                        if(response.status === 200){
                            self.closest('tr').remove();
                        }
                    });
                });

                $('.destroy-project, .destroy-phrases').click(function (e) {
                    e.preventDefault();

                    if (!confirm('{{ __('Are you sure?') }}'))
                        return false;

                    let link = $(this).attr('href');

                    axios.delete(link).then(function (response) {
                        if(!response.data)
                            window.location.reload();
                        else
                            window.location.href = response.data;
                    });
                });

            });

        </script>
        <script src="{{ asset('plugins/codemirror/mode/htmlmixed/htmlmixed.js') }}"></script>
    @endslot


@endcomponent
