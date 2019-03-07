@forelse ($senders as $sender)
    <tr>
        <td>{{ $sender->id }}</td>
        <td>
            <a href="#" class="sub_project"
               data-title="Выберите под проект"
               data-type="select2"
               data-pk="{{$sender->id}}"
               data-name="sub_project_id">
                {{$sender->subProject->name ?? ''}}
            </a>
        </td>
        <td>
            <a href="#" class="edit"
               data-title="Введите название"
               data-pk="{{$sender->id}}"
               data-name="name">
                {{$sender->name}}
            </a>
        </td>
        <td>
            <a href="#" class="edit"
               data-title="Введите extra"
               data-pk="{{$sender->id}}"
               data-name="extra">
                {{$sender->extra}}
            </a>
        </td>
        <td>
            <a href="#" class="edit"
               data-title="Введите login"
               data-pk="{{$sender->id}}"
               data-name="login">
                {{$sender->login}}
            </a>
        </td>
        <td>
            <a href="#" class="edit"
               data-title="Введите password"
               data-pk="{{$sender->id}}"
               data-name="password">
                {{$sender->password}}
            </a>
        </td>
        <td>
            <a href="#" class="table-link danger delete pull-right"
               data-title="Удалить отправителя?"
               data-pk="{{$sender->id}}"
               data-name="delete"
               style="border-bottom: 0;">
                                                                    <span class="fa-stack ">
                                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                                        <i class="fa fa-trash-o fa-stack-1x fa-inverse"></i>
                                                                    </span>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td class="text-center" colspan="7">Нет отправителя</td>
    </tr>
@endforelse