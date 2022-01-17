@component('component.card', ['title' => __('Manage access')])

    @slot('css')

    @endslot

    <div class="row">
        <div class="col-md-6">
            @include('manage-access.partials._cards', ['id' => 'role', 'title' => 'Роли пользователей', 'items' => $roles])
            <!-- /.card -->
        </div>

        <div class="col-md-6">
        @include('manage-access.partials._cards', ['id' => 'permission', 'title' => 'Разрешения', 'items' => $permissions])
        <!-- /.card -->
        </div>
    </div>

    <div class="row">
        @include('manage-access.partials._content')
    </div>

    @slot('js')

        <!-- jQuery UI 1.11.4 -->
        <script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}"></script>

        <script>

            $('.add-item').click(function(){
                let type = $(this).closest('.card').attr('id');
                if(type){
                    let name = prompt('Введите название: ' + type);
                    if(!name)
                        return false;

                    let letters = /^[a-zA-Z\s]+$/;
                    if (name.match(letters)){

                        axios.post('/manage-access', {
                            type: type,
                            name: name
                        }).then(function (response) {
                            window.location.reload();
                        }).catch(function (error) {
                                alert(error.response.data.message);
                        });
                    }else
                        alert('Только латинские символы!');
                }
            });

            $('.update-item').click(function(){
                let self = $(this);

                let type = self.closest('.card').attr('id');
                let id = self.closest('li').data('id');

                let name = prompt('Введите название: ' + type, self.closest('li').find('.text').text());
                if(!name)
                    return false;

                let letters = /^[a-zA-Z\s]+$/;
                if(type && id && name.match(letters)){

                    axios.patch('manage-access/' + id, {
                        type: type,
                        name: name,
                    }).then(function (response) {
                        if(response.status === 200){
                            window.location.reload();
                        }
                    }).catch(function (error) {
                        alert(error.response.data.message);
                    });
                }else
                    alert('Только латинские символы!');
            });

            $('.delete-item').click(function(){
                let type = $(this).closest('.card').attr('id');
                if(type){
                    let id = $(this).closest('li').data('id');
                    axios.get(`manage-access/destroy/${id}/where/${type}`).then(function (response) {
                        if(response.status === 200){
                            window.location.reload();
                        }
                    });
                }
            });

            $('#role').on('click', '.revoke-permission', function(e){
                e.preventDefault();

                let self = $(this).closest('li');

                let role = self.prevAll('.root').first().find('.text').text();
                let permission = self.find('.text').text();

                console.log(role, permission);

                axios.post('/manage-access/assignPermission', {
                    action: 'revoke',
                    role: role,
                    permission: permission
                }).then(function (response) {
                    console.log(response);
                    if(response.status === 200){
                        self.remove();
                    }
                }).catch(function (error) {
                    alert(error.response.data.message);
                });

            });

            // jQuery UI sortable for the todo list
            $('.todo-list').sortable({
                placeholder: 'sort-highlight',
                handle: '.handle',
                forcePlaceholderSize: true,
                zIndex: 999999,
                connectWith: '#role .todo-list',
                remove: function(event, ui) {
                    let item = ui.item.clone();
                    $(this).append(item);
                },

                receive: function( event, ui ) {
                    ui.item.addClass('sub').css({"padding-left": '20px', "background-color": 'rgb(248 249 250 / 10%)'});
                    ui.item.find('.tools').html($('<i />').addClass(['fas', 'fa-trash', 'revoke-permission']));
                    ui.item.find('.handle').removeClass('handle').html($('<i />').addClass(['fas', 'fa-ellipsis-h']));

                    let root = ui.item.prevAll('.root').first();
                    let role = root.find('.text').text();
                    let permission = ui.item.find('.text').text();

                    console.log(role, permission);

                    axios.post('/manage-access/assignPermission', {
                        action: 'assign',
                        role: role,
                        permission: permission
                    }).then(function (response) {
                        console.log(response);
                    }).catch(function (error) {
                        alert(error.response.data.message);
                    });

                }

            });
        </script>

    @endslot

@endcomponent
