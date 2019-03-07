
<table class="table">
    <thead>
    <tr>
        <th> @lang('general.id')</th>
        <th class="text-center"> @lang('general.name')</th>
        <th class="text-center"> @lang('general.status')</th>
        <th class="text-center"> @lang('general.calls')</th>
        <th class="text-center"> @lang('general.country')</th>
        <th class="text-center"> @lang('general.source')</th>
        <th class="text-center"> @lang('general.offer')</th>
        <th class="text-center"> @lang('general.position')</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    @if ($companies)
        @foreach($companies as $company)
            <tr position="{{$company->position}}" id="{{$company->id}}">
                <td>
                    {{$company->id}}
                </td>
                <td class="text-center">
                    {{$company->name}}
                </td>
                <td class="text-center">
                    @if($company->cron_status == 1)
                        @lang('general.on')
                    @else
                        @lang('general.off')
                    @endif
                </td>
                <td class="text-center">
                    <?
                    if ($company->call_time){
                        $callTime = json_decode($company->call_time);
                        foreach ($callTime as $key => $value) {
                            echo $key . ' : ' . (int)$value . '<br>';
                        }
                    }
                    ?>
                </td>
                <td class="text-center">
                    <?
                    $country = json_decode($company->country);
                    $count = 0;
                    if ($country) {
                        foreach ($country as $countryComp) {
                            foreach ($countries as $country) {
                                if ($countryComp[0] == $country->id) {
                                    if ($countryComp[1] == 1) {
                                    ?>
                                        <span class="badge badge-danger error-massage" style="background-color: #2ecc71;margin-left: 5px">
                                            @lang('countries.' . $country->code)
                                        </span><br>
                                    <?php } else { ?>
                                        <span class="badge badge-danger error-massage" style="background-color: #f4786e;margin-left: 5px">
                                            @lang('countries.' . $country->code)
                                        </span><br>
                                    <?php
                                    }
                                }
                            }
                        }
                    }
                    ?>
                </td>
                <td class="text-center">
                    <?
                    $sourceCompany = json_decode($company->source);
                    if ($sourceCompany) {
                        foreach ($sourceCompany as $sourceComp) {
                            foreach ($source as $value) {
                                if ($sourceComp[0] == $value->id) {
                                    if ($sourceComp[1] == 1) {
                                        echo '<span class="badge badge-danger error-massage" style="background-color: #2ecc71;margin-left: 5px">' . $value->name .'</span><br>';
                                    } else {
                                        echo '<span class="badge badge-danger error-massage" style="background-color: #f4786e;margin-left: 5px">' . $value->name .'</span><br>';
                                    }
                                    break;
                                }
                            }
                        }
                    }
                    ?>
                </td>
                <td class="text-center">
                    <?
                    $offersCompany = json_decode($company->offer);
                    if ($offersCompany) {
                        foreach ($offersCompany as $offerComp) {
                            foreach ($offers as $offer) {
                                if ($offerComp[0] == $offer->id) {
                                    if ($offerComp[1] == 1) {
                                        echo '<span class="badge badge-danger error-massage" style="background-color: #2ecc71;margin-left: 5px">' . $offer->title .'</span><br>';
                                    } else {
                                        echo '<span class="badge badge-danger error-massage" style="background-color: #f4786e;margin-left: 5px">' . $offer->title .'</span><br>';
                                    }
                                    break;
                                }
                            }
                        }
                    }
                    ?>
                </td>
                <td style="width: 50px">
                    <a class="table-link position" data="1" style="cursor: pointer">
                                                    <span class="fa-stack">
                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                        <i class="fa fa-long-arrow-up fa-stack-1x fa-inverse"></i>
                                                    </span>
                    </a>
                    <a class="table-link position" data="0" style="cursor: pointer">
                                                    <span class="fa-stack">
                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                        <i class="fa fa-long-arrow-down fa-stack-1x fa-inverse"></i>
                                                    </span>
                    </a>
                </td>
                <td>
                    <a href="{{route('company_elastix_update', $company->id)}}" class="table-link">
                                                    <span class="fa-stack">
                                                        <i class="fa fa-square fa-stack-2x"></i>
                                                        <i class="fa fa-long-arrow-right fa-stack-1x fa-inverse"></i>
                                                    </span>
                    </a>
                </td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>
