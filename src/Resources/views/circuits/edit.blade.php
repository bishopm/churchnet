@extends('churchnet::templates.frontend')

@section('content_header')
{{ Form::pgHeader('Edit circuit','Circuits',route('admin.circuits.index')) }}
@stop

@section('content')
<div class="container mt-5">
    @include('churchnet::shared.errors')    
    {!! Form::open(['route' => array('admin.circuits.update',$circuit->id), 'method' => 'put']) !!}
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary"> 
                <div class="box-body">
                    <h3>Circuit leaders and preachers</h3>
                    <ul class="list-unstyled">
                        @foreach ($leaders->sortBy('surname') as $leader)
                            <li>
                                <b>{{$leader->surname}}, {{$leader->title}} {{$leader->firstname}}</b> [
                                @foreach ($leader->positions as $pos)
                                    {{$pos->position}}
                                    @if ($loop->last)
                                        ]
                                    @else
                                        , 
                                    @endif
                                @endforeach
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-primary"> 
                <div class="box-body">
                    @include('churchnet::circuits.partials.edit-fields')
                </div>
                <div class="box-footer">
                    {{Form::pgButtons('Update',route('admin.circuits.index')) }}
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
@stop