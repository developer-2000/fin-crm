@if($templates->count())
    <div class="table-responsive">
        <table class="table table-striped table-hover all_lists">
            <thead>
            <tr>
                <th class="text-center">ID</th>
                <th class="text-center">Название</th>
                <th class="text-center">Тект сообщения</th>
                <th class="text-center"></th>
            </tr>
            </thead>
            <tbody>
            @foreach($templates as $template)
                <tr>
                    <td class="text-center">
                        {{$template->id}}
                    </td>
                    <td>
                        <a href="#" id="category" data-type="text"
                           data-pk="{{ $template->id }}"
                           data-name="name"
                           data-url="/ajax/sms/templates/name-edit"
                           data-id="{{ $template->id }}"
                           data-title="Введите новое название шаблона"
                           class="editable editable-click template-name"
                           data-original-title=""
                           title="">{{$template->name}}</a>
                    </td>
                    <td>
                        <a href="#" id="category" data-type="textarea"
                           data-pk="{{ $template->id }}"
                           data-name="name"
                           data-url="/ajax/sms/templates/body-edit"
                           data-id="{{ $template->id }}"
                           data-title="Введите новый текст сообщения"
                           class="editable editable-click template"
                           data-original-title=""
                           title="">{{$template->body}}</a>
                    </td>
                    <td>
                        <a href="#"
                           data-type="text"
                           data-pk="{{ $template->id }}"
                           data-title="Вы действительно хотите удалить шаблон?"
                           data-id="{{ $template->id }}"
                           data-url="/ajax/sms/templates/destroy"
                           class="editable editable-click table-link danger  destroy-template">Удалить</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif
