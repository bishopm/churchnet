@extends('churchnet::templates.frontend')

@section('css')
    @parent
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css">
@stop

@section('content')
    <div class="container mt-5">
    @include('churchnet::shared.errors') 
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6"><h4>Lectionary readings</h4></div>
                            <div class="col-md-6"><a href="{{route('admin.readings.create')}}" class="btn btn-primary float-right"><i class="fa fa-pencil"></i> Add a new entry</a></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="indexTable" class="table table-striped table-hover table-condensed" cellspacing="0">
                            <thead>
                                <tr>
                                    <th></th><th>Description</th><th>Year A</th><th>Year B</th><th>Year C</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th></th><th>Description</th><th>Year A</th><th>Year B</th><th>Year C</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @forelse ($readings as $reading)
                                    <tr>
                                        <td><a href="{{route('admin.readings.edit',$reading->id)}}">{{$reading->id}}</a></td>
                                        <td><a href="{{route('admin.readings.edit',$reading->id)}}">{{$reading->description}}</a></td>
                                        <td><a href="{{route('admin.readings.edit',$reading->id)}}">{{$reading->a}}</a></td>
                                        <td><a href="{{route('admin.readings.edit',$reading->id)}}">{{$reading->b}}</a></td>
                                        <td><a href="{{route('admin.readings.edit',$reading->id)}}">{{$reading->c}}</a></td>
                                    </tr>
                                @empty
                                    <tr><td>No readings have been added yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
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