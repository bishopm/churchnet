@extends('churchnet::templates.frontend')

@section('content')
<div class="container mt-5">
    <h3>Journey users statistics</h3>
    <table class="table table-striped table-sm">
        @foreach ($data as $district=>$dat)
            <tr><td class="text-center" colspan="6"><b>{{$district}}</b></td></tr>
            @foreach ($dat as $circuit=>$da)
                <tr><td class="text-center" colspan="6"><b>{{$circuit}}</b></td></tr>
                <tr class="text-center thead-dark"><th>Society</th><th>Inactive</th><th>Today</th><th>This week</th><th>This month</th><th>Ever</th></tr>
                @foreach ($da as $society=>$d)
                    <tr>
                        <td>{{$society}}</td><td class="text-center">
                        @if (array_key_exists('registered',$d))
                            <a href="#" title="{{implode(', ',$d['registered'])}}">{{count($d['registered'])}}</a>
                        @else
                            0
                        @endif
                        </td><td class="text-center">
                        @if (array_key_exists('today',$d))
                            {{implode(', ',$d['today'])}}
                            ({{count($d['today'])}})
                        @else
                            0
                        @endif
                        </td><td class="text-center">
                        @if (array_key_exists('thisweek',$d))
                            {{implode(', ',$d['thisweek'])}}
                            ({{count($d['thisweek'])}})
                        @else
                            0
                        @endif
                        </td><td class="text-center">
                        @if (array_key_exists('thismonth',$d))
                            {{implode(', ',$d['thismonth'])}}
                            ({{count($d['thismonth'])}})
                        @else
                            0
                        @endif
                        </td><td class="text-center">
                        @if (array_key_exists('ever',$d))
                            <a href="#" title="{{implode(', ',$d['ever'])}}">{{count($d['ever'])}}</a>
                        @else
                            0
                        @endif
                        </td>
                    </tr>
                @endforeach
            @endforeach
        @endforeach
    </table>
</div>
@endsection