@extends('churchnet::templates.frontend')

@section('css')
<meta id="token" name="token" value="{{ csrf_token() }}" />
<link href="{{ asset('/vendor/bishopm/css/selectize.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('content_header')
{{ Form::pgHeader('Edit person','people',route('admin.people.update', ['circuit'=>$person->circuit_id, 'person'=>$person->id])) }}
@stop

@section('content')
<div class="container mt-5">
    @include('churchnet::shared.errors')    
    {!! Form::open(['route' => array('admin.people.update', $person->circuit_id, $person->id), 'method' => 'put']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary"> 
                <div class="box-body">
                    @include('churchnet::people.partials.edit-fields')
                </div>
                <div class="box-footer">
                    {{Form::pgButtons('Update',route('admin.people.update', ['circuit'=>$person->circuit_id, 'person'=>$person->id])) }}
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
@stop

@section('js')
<script src="{{ asset('/vendor/bishopm/js/selectize.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
$( document ).ready(function() {
    $('.selectize').selectize({
        plugins: ['remove_button'],
        openOnFocus: 1,
        maxOptions: 30,
        dropdownParent: "body"
    });
});
</script>
@stop    