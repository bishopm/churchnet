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
                            <div class="col-md-3"><h4>Role / status</h4></div>
                            <div class="col-md-9">
                                <form action="{{route('admin.roles.store')}}" method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-5 text-right">
                                            <input class="form-control" placeholder="Role / Status" name="position">                                  
                                        </div>                                        
                                        <div class="col-5 text-right">
                                            <select class="form-control" name="namespace">
                                                <option value="Bishopm\Churchnet\Models\Person">Leader</option>
                                                <option value="Bishopm\Churchnet\Models\Minister">Minister</option>
                                                <option value="Bishopm\Churchnet\Models\Preacher">Preacher</option>
                                            </select>
                                        </div>
                                        <div class="col-2 text-right">
                                            <button class="form-control btn-primary" type="submit">Add</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="indexTable" class="table table-striped table-hover table-condensed" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Status / role</th><th>Scope</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Status / role</th><th>Scope</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @forelse ($roles as $role)
                                    <tr>
                                        <td><a href="{{route('admin.roles.edit',$role->id)}}">{{$role->name}}</a></td>
                                        <td><a href="{{route('admin.roles.edit',$role->id)}}">
                                            @if ($role->namespace == 'Bishopm\Churchnet\Models\Person')
                                                Leaders
                                            @elseif ($role->namespace == 'Bishopm\Churchnet\Models\Preacher')
                                                Preachers
                                            @else
                                                Ministers
                                            @endif
                                        </a></td>
                                    </tr>
                                @empty
                                    <tr><td>No roles have been added yet</td></tr>
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