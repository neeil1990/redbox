@component('component.card', ['title' => __('Behavior')])

    @slot('css')
        <!-- CodeMirror -->
        <link rel="stylesheet" href="{{ asset('plugins/codemirror/codemirror.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/codemirror/theme/monokai.css') }}">
    @endslot

    <h3 class="my-3">{{ $behavior->domain }}</h3>

    <div class="row">
        <div class="col-md-3 col-sm-6 col-12">
            @if($behavior->status)
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="far fa-check-square"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('Promo code') }}</span>
                        <span class="info-box-number">{{ __('Applied') }}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            @else
                <div class="info-box">
                    <span class="info-box-icon bg-info"><i class="far fa-window-close"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{ __('Promo code') }}</span>
                        <span class="info-box-number">{{ __('Not applied') }}</span>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            @endif
        </div>
        <!-- /.col -->
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Insert to your site') }}</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <textarea id="code">
<script defer>
    $.getScript("{{ request()->getSchemeAndHttpHost() }}/client/js/prime.visit.js").done(function(script, textStatus) {
        let paramVisit = new Visit();
        if(!paramVisit.getCookie('paramsVisit')){
            paramVisit.update('{{ $params }}');
        }
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
        <div class="col-md-12">
            {!! Form::open(['method' => 'DELETE', 'route' => ['behavior.destroy', $behavior->id]]) !!}
                {!! Form::button( '<i class="fas fa-trash"></i> ' . __('Delete'), ['type' => 'submit', 'class' => 'btn btn-danger']) !!}
            {!! Form::close() !!}
        </div>
    </div>

    @slot('js')
        <!-- CodeMirror -->
        <script src="{{ asset('plugins/codemirror/codemirror.js') }}"></script>
        <script src="{{ asset('plugins/codemirror/mode/css/css.js') }}"></script>
        <script src="{{ asset('plugins/codemirror/mode/xml/xml.js') }}"></script>
        <script src="{{ asset('plugins/codemirror/mode/htmlmixed/htmlmixed.js') }}"></script>
        <script>
            $(function () {
                // CodeMirror
                CodeMirror.fromTextArea(document.getElementById("code"), {
                    mode: "htmlmixed",
                    theme: "monokai",
                });
            })
        </script>
    @endslot


@endcomponent
