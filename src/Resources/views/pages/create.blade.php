@extends('churchnet::templates.frontend')

@section('content_header')
{{ Form::pgHeader('Add resource','Resources',route('admin.resources.index')) }}
@stop

@section('content')
<div class="container mt-5">
    @include('churchnet::shared.errors')    
    {!! Form::open(['route' => array('admin.resources.store'), 'method' => 'post']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary"> 
                <div class="box-body">
                    @include('churchnet::resources.partials.create-fields')
                </div>
                <div class="box-footer">
                    {{Form::pgButtons('Create',route('admin.resources.index')) }}
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
@stop