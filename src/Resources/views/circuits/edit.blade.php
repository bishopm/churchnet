@extends('churchnet::templates.frontend')

@section('content_header')
{{ Form::pgHeader('Edit circuit','Circuits',route('admin.circuits.index')) }}
@stop

@section('content')
<div class="container mt-5">
    @include('churchnet::shared.errors')    
    {!! Form::open(['route' => array('admin.circuits.update',$circuit->id), 'method' => 'put']) !!}
    <div class="row">
        <div class="col-md-12">
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