@extends('churchnet::templates.frontend')

@section('css')
    @parent
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
@stop

@section('content_header')
{{ Form::pgHeader('Edit circuit','Circuits',route('admin.circuits.index')) }}
@stop

@section('content')
<div class="container mt-5">
    @include('churchnet::shared.errors')    
    <div class="row">
        <div class="col-lg-8 col-md-12">
            <div class="card card-primary"> 
                <div class="card-header">
                    <h4 class="text-center">
                        {{$circuit->circuit}} {{$circuit->circuitnumber}} <small>Leaders and preachers</small> 
                        <a href="{{route('admin.people.create', $circuit->id)}}" class="btn btn-primary float-right"><i class="fa fa-pencil"></i> Add a new person</a>
                    </h4>
                </div>
                <div class="card-body">
                    <div class="card-body">
                        <table id="indexTable" class="table table-striped table-hover table-condensed" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Name</th><th>Role</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Name</th><th>Role</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @forelse ($leaders as $leader)
                                    <tr>
                                        <td><a href="{{route('admin.people.edit',array($circuit->id,$leader->id))}}">{{$leader->individual->surname}}, {{$leader->individual->title}} {{$leader->individual->firstname}}</a></td>
                                        <td><a href="{{route('admin.people.edit',array($circuit->id,$leader->id))}}">
                                            @foreach ($leader->tags as $pos)
                                                {{$pos->tag}}
                                                @if (!$loop->last)
                                                    , 
                                                @endif
                                            @endforeach
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td>No leaders or preachers have been added yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="card card-primary"> 
                <div class="card-header">
                    <h4 class="text-center">Circuit details</h4>
                </div>
                <div class="card-body">
                    {!! Form::open(['route' => array('admin.circuits.update',$circuit->id), 'method' => 'put']) !!}
                    @include('churchnet::circuits.partials.edit-fields')
                </div>
                <div class="card-footer">
                    {{Form::pgButtons('Update',route('admin.circuits.index')) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script language="javascript">
$(document).ready(function() {
    $('#indexTable').DataTable();
} );
</script>
@endsection