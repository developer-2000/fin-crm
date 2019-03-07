<div class="tab-content">
    <div class="tab-pane fade in active">
        <div class="row">
            <div class="col-lg-12">
                <div class="main-box clearfix">
                    @if($procStatuses->count() && $codesStatuses->count())
                        @foreach($procStatuses as $key => $row)
                            <span class=""
                                  style="font-weight: bold;"> Проект: {{!empty($row[0]->project->name ) ? $row[0]->project->name : ''}}</span>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped integrations_table">
                                    <thead>
                                    <tr>
                                        <th width="10%">Код Статуса</th>
                                        <th width="35%">Статус</th>
                                        <th width="25%">Системный статус</th>
                                        <th width="25%">Процессинг статус</th>
                                        <th width="5%"></th>
                                    </tr>
                                    </thead>
                                    <tbody class="">
                                    @foreach($codesStatuses as $codesStatus)
                                        <tr>
                                            <td>
                                                {{$codesStatus->status_code}}
                                            </td>
                                            <td>
                                                {{$codesStatus->status}}
                                            </td>
                                            <td class="system-status">
                                                <select id="system_status" class="system_status" name="system_status"
                                                        style="width: 100%"
                                                        @if (!isset($permissions['set_system_code_status']))
                                                        disabled @endif>
                                                    <option value="">Выберите статус</option>
                                                    @foreach ($systemStatuses as $systemStatus)
                                                        <option value="{{$systemStatus->id}}"
                                                                @if($systemStatus->id == $codesStatus->system_status_id)
                                                                selected
                                                                @endif
                                                        >{{$systemStatus->name}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="proc-status">
                                                <select id="proc_status" class="proc_status" name="proc_status"
                                                        style="width: 100%">
                                                    <option value="">Выберите проц/статус</option>
                                                    @foreach ($row as $procStatuse)
                                                        <option value="{{$procStatuse->id}}"
                                                                @php
                                                                    $projectProcStatus = $codesStatus->projectCodeStatus->where('proc_status_id', $procStatuse->id)->first();
                                                                @endphp
                                                                @if($projectProcStatus)
                                                                selected
                                                                @endif
                                                        >{{$procStatuse->name}}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="save-changes">
                                                <a href="#"
                                                   data-type="text"
                                                   data-pk="1"
                                                   data-title="Вы действительно хотите сохранить изменения?"
                                                   data-code="{{ $codesStatus->status_code }}"
                                                   class="editable editable-click table-link save_changes">Сохранить</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="integrationId" name="integrationId" value="{{!empty($integrationId) ? $integrationId : NULL}}">
<script src="{{ URL::asset('js/integrations/codes-statuses.js') }}"></script>