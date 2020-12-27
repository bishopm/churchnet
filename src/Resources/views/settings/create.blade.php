@extends('churchnet::templates.frontend')

@section('content_header')
{{ Form::pgHeader('Add setting','Settings',route('admin.settings.index')) }}
@stop

@section('content')
<div class="container mt-5">
    @include('churchnet::shared.errors')    
    {!! Form::open(['route' => array('admin.settings.store'), 'method' => 'post']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary"> 
                <div class="box-body">
                    @include('churchnet::settings.partials.create-fields')
                </div>
                <div class="box-footer">
                    {{Form::pgButtons('Create',route('admin.settings.index')) }}
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
@stop

@section('js')
<script language="javascript">
function levelchange(data) {
    if (data.value=='Bishopm\\Churchnet\\Models\\District') {
        $('#districts').show();
        $('#circuits').hide();
    } else if (data.value=='Bishopm\\Churchnet\\Models\\Circuit'){
        $('#districts').hide();
        $('#circuits').show();
    } else if (data.value=='Connexion'){
        $('#districts').hide();
        $('#circuits').hide();
    }
}
</script>
@stop