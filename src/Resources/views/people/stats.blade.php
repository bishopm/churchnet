@extends('churchnet::templates.frontend')

@section('content')
<div class="container mt-5">
    <h3>Journey users statistics</h3>
    <div class="row">
        <div class="col-sm">
            @foreach ($data as $district=>$dat)
                <h3>{{$district}}</h3>
                @foreach ($dat as $circuit=>$da)
                    <b>{{$circuit}}</b>
                    <ul style="list-style: none; padding-inline-start: 0px;">
                        @foreach ($da as $society=>$d)
                            <li>
                                {{$society}} 
                                @if (array_key_exists('registered',$d))
                                <a href="#" title="{{implode(', ',$d['registered'])}}">(Inactive: {{count($d['registered'])}}</a>, 
                                @else
                                    (Inactive: 0, 
                                @endif
                                @if (array_key_exists('today',$d))
                                    <a href="#" title="{{implode(', ',$d['today'])}}">Active today: {{count($d['today'])}}</a>
                                @else
                                    Active today: 0, 
                                @endif
                                @if (array_key_exists('thisweek',$d))
                                    <a href="#" title="{{implode(', ',$d['thisweek'])}}">Active this week: {{count($d['thisweek'])}}</a>
                                @else
                                    Active this week: 0, 
                                @endif
                                @if (array_key_exists('thismonth',$d))
                                    <a href="#" title="{{implode(', ',$d['thismonth'])}}">Active this month: {{count($d['thismonth'])}}</a>
                                @else
                                    Active this month: 0, 
                                @endif
                                @if (array_key_exists('ever',$d))
                                    <a href="#" title="{{implode(', ',$d['ever'])}}">Active ever: {{count($d['ever'])}})</a>
                                @else
                                    Active ever: 0)
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            @endforeach
        </div>
    </div>
</div>
@endsection