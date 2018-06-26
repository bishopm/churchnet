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
                            <div class="col-md-6"><h4>People</h4></div>
                            <div class="col-md-6"><a href="{{route('admin.circuits.index')}}" class="btn btn-primary float-right"><i class="fa fa-pencil"></i> Add a new person</a></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="indexTable" class="table table-striped table-hover table-condensed" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Name</th><th>Circuit</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Name</th><th>Circuit</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @forelse ($people as $person)
                                    <tr>
                                        <td><a href="{{route('admin.people.edit',[$person->circuit_id,$person->id])}}">{{$person->surname}}, {{$person->title}} {{$person->firstname}}</a></td>
                                        <td><a href="{{route('admin.people.edit',[$person->circuit_id,$person->id])}}">{{$person->circuit->circuit}} {{$person->circuit->circuitnumber}}</a></td>
                                    </tr>
                                @empty
                                    <tr><td>No people have been added yet</td></tr>
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