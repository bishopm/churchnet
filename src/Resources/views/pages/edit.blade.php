@extends('churchnet::templates.frontend')

@section('content_header')
{{ Form::pgHeader('Edit resource','Resources',route('admin.resources.index')) }}
@stop

@section('content')
<div class="container mt-5">
    @include('churchnet::shared.errors')    
    {!! Form::open(['route' => array('admin.resources.update',$resource->id), 'method' => 'put']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary"> 
                <div class="box-body">
                    @include('churchnet::resources.partials.edit-fields')
                </div>
                <div class="box-footer">
                    {{Form::pgButtons('Update',route('admin.resources.index')) }}
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
@stop