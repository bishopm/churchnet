@extends('churchnet::templates.frontend')

@section('css')
<meta id="token" name="token" value="{{ csrf_token() }}" />
<link href="{{ asset('/vendor/bishopm/css/selectize.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('content_header')
{{ Form::pgHeader('Edit role / status','roles',route('admin.roles.index')) }}
@stop

@section('content')
<div class="container mt-5">
    @include('churchnet::shared.errors')    
    {!! Form::open(['route' => array('admin.roles.update', $role->id), 'method' => 'put']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary"> 
                <div class="box-body">
                    <div class="form-group">
                        <input class="form-control" placeholder="Role / Status" name="position" value="{{$role->name}}">                                  
                    </div>                                        
                    <div class="form-group">
                        <select class="form-control" name="namespace">
                            @if ($role->namespace == "Bishopm\Churchnet\Models\Person")
                                <option selected value="Bishopm\Churchnet\Models\Person">Leader</option>
                                <option value="Bishopm\Churchnet\Models\Preacher">Preacher</option>
                                <option value="Bishopm\Churchnet\Models\Minister">Presbyter / Deacon / Evangelist</option>
                            @elseif ($role->namespace == "Bishopm\Churchnet\Models\Minister")
                                <option value="Bishopm\Churchnet\Models\Person">Leader</option>
                                <option selected value="Bishopm\Churchnet\Models\Preacher">Preacher</option>
                                <option value="Bishopm\Churchnet\Models\Minister">Presbyter / Deacon / Evangelist</option>
                            @else
                                <option value="Bishopm\Churchnet\Models\Person">Leader</option>
                                <option value="Bishopm\Churchnet\Models\Preacher">Preacher</option>
                                <option selected value="Bishopm\Churchnet\Models\Minister">Presbyter / Deacon / Evangelist</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="box-footer">
                    {{Form::pgButtons('Update',route('admin.roles.index')) }}
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