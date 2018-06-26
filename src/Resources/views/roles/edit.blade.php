@extends('churchnet::templates.frontend')

@section('css')
<meta id="token" name="token" value="{{ csrf_token() }}" />
@stop

@section('content_header')
{{ Form::pgHeader('Edit role / status','roles',route('admin.roles.index')) }}
@stop

@section('content')
<div class="container mt-5">
    @include('churchnet::shared.errors')    
    {!! Form::open(['route' => array('admin.roles.update', $tag->id), 'method' => 'put']) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary"> 
                <div class="box-body">
                    <div class="form-group">
                        <input class="form-control" placeholder="Role / Status" name="position" value="{{$tag->name}}">                                  
                    </div>                                        
                    <div class="form-group">
                        <select class="form-control" name="type">
                            @if ($tag->type == "leader")
                                <option selected value="leader">Leader</option>
                                <option value="preacher">Preacher</option>
                                <option value="minister">Minister / Deacon / Biblewoman / Evangelist</option>
                            @elseif ($tag->type == "preacher")
                                <option value="leader">Leader</option>
                                <option selected value="preacher">Preacher</option>
                                <option value="minister">Minister / Deacon / Biblewoman / Evangelist</option>
                            @else
                                <option value="leader">Leader</option>
                                <option value="preacher">Preacher</option>
                                <option selected value="minister">Minister / Deacon / Biblewoman / Evangelist</option>
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