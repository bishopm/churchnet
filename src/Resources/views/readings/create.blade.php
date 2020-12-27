@extends('churchnet::templates.frontend')

@section('css')
<meta id="token" name="token" value="{{ csrf_token() }}" />
<link href="{{ asset('/vendor/bishopm/css/selectize.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('content_header')
{{ Form::pgHeader('Add reading','Readings',route('admin.readings.index')) }}
@stop

@section('content')
<div class="container mt-5">
    @include('churchnet::shared.errors')    
    {!! Form::open(['route' => array('admin.readings.store'), 'method' => 'post']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary"> 
                <div class="box-body">
                    @include('churchnet::readings.partials.create-fields')
                </div>
                <div class="box-footer">
                    {{Form::pgButtons('Create',route('admin.readings.index')) }}
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
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="token"]').attr('value')
    }
});    
$( document ).ready(function() {
    $('.tag-select').selectize({
        plugins: ['remove_button'],
        openOnFocus: 0,
        maxOptions: 30,
        dropdownParent: "body",
        create: function(value) {
            return {
                value: value,
                text: value
            }
        }
    });
    $('#body').summernote({ 
        height: 250,
        toolbar: [
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['superscript', 'subscript']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['table', ['table']],
        ['link', ['linkDialogShow', 'unlink']],
        ['view', ['fullscreen', 'codeview']],
        ['para', ['ul', 'ol', 'paragraph']]
        ]
    });
});
</script>
@stop    