@extends('churchnet::templates.frontend')

@section('title',$resource->resource)

@section('content')
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <h4>{{$resource->resourcenumber}} {{$resource->resource}} <small><a href="{{url('/')}}/methodist/districts/{{$resource->district_id}}">{{$resource->district->district}} District</a></small></h4>
            @if ($resource->office_contact)
                {{$resource->office_contact}} 
            @endif
            @if ($plan)
                <a href="{{url('/')}}/plan/{{$resource->slug}}" target="_blank">Current preaching plan</a>
            @endif
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <h4>Societies</h4>
            <ul class="list-unstyled">
            @foreach ($resource->societies as $society)
                <li><a href="{{url('/')}}/methodist/{{$resource->slug}}/{{$society->slug}}">{{$society->society}}</a></li>
            @endforeach
            </ul>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div style="width: 100%; height: 400px;">
                {!! Mapper::render() !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-6">
            @if (isset($Minister))
                <h3>Ministers</h3>
                <ul class="list-unstyled">
                    @foreach ($Minister as $min)
                        <li>{{$min->title}} {{$min->firstname}} {{$min->surname}}</li>
                    @endforeach
                </ul>
            @endif
            @if (isset($Supernumerary))
                <h3>Supernumerary Ministers</h3>
                <ul class="list-unstyled">
                    @foreach ($Supernumerary as $sup)
                        <li>{{$sup->title}} {{$sup->firstname}} {{$sup->surname}}</li>
                    @endforeach
                </ul>
            @endif
            <ul class="list-unstyled">
                @if ((isset($settings['superintendent'])) and ($settings['superintendent']<>""))
                    <li><i>Superintendent Minister:</i> {{$settings['superintendent']}}</li>
                @endif
                @if ((isset($settings['resource_stewards'])) and ($settings['resource_stewards']<>""))
                    <li><i>Resource Stewards:</i>
                     @foreach(explode(',',$settings['resource_stewards']) as $cs)
                        <li>- {{$cs}}</li>
                     @endforeach
                    </li>
                @endif
                @if ((isset($settings['resource_secretary'])) and ($settings['resource_secretary']<>""))
                    <li><i>Resource Secretary:</i> {{$settings['resource_secretary']}}</li>
                @endif
                @if ((isset($settings['resource_treasurer'])) and ($settings['resource_treasurer']<>""))
                    <li><i>Resource Treasurer:</i> {{$settings['resource_treasurer']}}</li>
                @endif
                @if ((isset($settings['local_preachers_secretary'])) and ($settings['local_preachers_secretary']<>""))
                    <li><i>Local Preachers Secretary:</i> {{$settings['local_preachers_secretary']}}</li>
                @endif
            </ul>
        </div>
        <div class="col-xs-12 col-sm-6">
            @if (isset($preachers))
                <h3>Local preachers</h3>
                @foreach ($preachers as $lp)
                    @if (!$loop->last)
                        {{$lp}}, 
                    @else
                        {{$lp}}.
                    @endif
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection