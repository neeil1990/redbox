@component('component.card', ['title' => __('Partners (admins)')])
    @slot('css')
        <link rel="stylesheet" type="text/css"
              href="{{ asset('plugins/keyword-generator/css/font-awesome-4.7.0/css/font-awesome.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/keyword-generator/css/style.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/jqcloud/css/jqcloud.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/common/css/datatable.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/toastr/toastr.css') }}"/>
        <link rel="stylesheet" type="text/css" href="{{ asset('plugins/relevance-analysis/css/style.css') }}"/>
        <style>
            .card-header::after {
                display: none;
            }

            .fa {
                cursor: pointer;
                opacity: .5;
            }

            .fa:hover {
                opacity: 1;
            }

            .card-img-top {
                height: 180px;
                width: 100%;
                display: block;
            }
        </style>
    @endslot
    <div class="modal fade" id="removeGroupModal" tabindex="-1" aria-labelledby="removeGroupModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeGroupModalLabel">{{ __('Deleting a group') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ __('If you delete a group, then all related partners will also be deleted') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                    <button id="removeGroupButton" type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('Remove') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="removeItemModal" tabindex="-1" aria-labelledby="removeItemModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeItemModalLabel">{{ __('Delete partner') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{ __('Confirm the action.') }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Close') }}</button>
                    <button id="removeItemButton" type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{ __('Remove') }}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <a href="{{ route('partners.add.group') }}" class="btn btn-outline-secondary">{{ __('Add group') }}</a>
            <a href="{{ route('partners.add.item') }}" class="btn btn-outline-secondary">{{ __('Add partner') }}</a>
            <a href="{{ route('partners.admin') }}" class="btn btn-outline-secondary">{{ __('Partners (admins)') }}</a>
            <a href="{{ route('partners') }}" class="btn btn-outline-secondary">{{ __('Partners (users)') }}</a>
        </div>
        @foreach($groups as $elem)
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div>
                        <h3 class="text-muted">{{ $elem['name'] }}</h3>
                        <span class="text-muted">{{ __('Group position') }}: {{ $elem['position'] }}</span>
                    </div>
                    <div data-id="{{ $elem['id'] }}"
                         data-position="{{ $elem['position'] }}"
                         data-name="{{ $elem['name'] }}"
                         class="d-flex justify-content-center align-content-center">
                        <a href="{{ route('partners.edit.group', $elem['id']) }}">
                            <i class="fa fa-edit ml-2 mr-2"></i>
                        </a>

                        <i class="fa fa-trash remove-group" data-toggle="modal" style="padding-top: 3px"
                           data-target="#removeGroupModal"></i>
                    </div>
                </div>
                <div class="row d-flex justify-content-center">
                    @foreach($elem['items'] as $item)
                        <div class="card mr-3" style="width: 18rem;">
                            <div class="d-flex justify-content-end m-2 align-items-center">
                                @if($item['auditorium_ru'])
                                    <span class="text-muted mr-2">RU</span>
                                @endif
                                @if($item['auditorium_en'])
                                    <span class="text-muted mr-2">Eng</span>
                                @endif
                                <span class="text-muted">{{ __("Partner's position") }}: {{ $item['position'] }}</span>
                                <a href="{{ route('partners.edit.item', $item['id']) }}" class="ml-2 mr-2"
                                   style="padding-top: 1px">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <i class="fa fa-trash remove-item"
                                   data-id="{{ $item['id'] }}"
                                   data-toggle="modal"
                                   data-target="#removeItemModal"></i>
                            </div>
                            <img class="card-img-top" src="../../storage/{{ $item['image'] }}" alt="image">
                            <div class="card-footer h-100">
                                <h5 class="card-title">{{ $item['name'] }}</h5>
                                <p class="card-text">{{ $item['description'] }}</p>
                                <a href="{{ $item['link'] }}" class="btn btn-secondary" target="_blank"> >>> </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
    @slot('js')
        <script>
            let groupId
            let itemId
            let i

            $('.remove-group').on('click', function () {
                i = $(this)
                groupId = i.parent().attr('data-id');
            })

            $('#removeGroupButton').on('click', function () {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('partners.remove.group') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: groupId
                    },
                    success: function () {
                        i.parents().eq(2).remove();
                    },
                });
            })

            $('.remove-item').on('click', function () {
                i = $(this)
                itemId = i.attr('data-id')
            })

            $('#removeItemButton').on('click', function () {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "{{ route('partners.remove.item') }}",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: itemId
                    },
                    success: function () {
                        i.parents().eq(1).remove();
                    },
                });
            })
        </script>

    @endslot
@endcomponent
