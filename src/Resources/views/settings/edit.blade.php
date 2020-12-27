@extends('churchnet::templates.frontend')

@section('content_header')
{{ Form::pgHeader('Edit setting','Settings',route('admin.settings.index')) }}
@stop

@section('content')
<div class="container mt-5">
    @include('churchnet::shared.errors')    
    {!! Form::open(['route' => array('admin.settings.update',$setting->id), 'method' => 'put']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary"> 
                <div class="box-body">
                    @include('churchnet::settings.partials.edit-fields')
                </div>
                <div class="box-footer">
                    {{Form::pgButtons('Update',route('admin.settings.index')) }}
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
</div>
@stop

@section('js')
<script language="javascript">
    $(document).ready ( function(){
        if ('{{$setting->level}}'=='District'){
            $('#districts').show();
        } else if ('{{$setting->level}}'=='Circuit'){
            $('#circuits').show();
        }
    });

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