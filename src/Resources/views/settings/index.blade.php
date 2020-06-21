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
                            <div class="col-md-6"><h4>Settings</h4></div>
                            <div class="col-md-6"><a href="{{route('admin.settings.create')}}" class="btn btn-primary float-right"><i class="fa fa-pencil"></i> Add a new setting</a></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="indexTable" class="table table-striped table-hover table-condensed" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Setting Key</th><th>Setting Value</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>Setting Key</th><th>Setting Value</th>
                                </tr>
                            </tfoot>
                            <tbody>
                                @forelse ($settings as $setting)
                                    <tr>
                                        <td><a href="{{route('admin.settings.edit',$setting->id)}}">{{ucwords(str_replace('_',' ',$setting->setting_key))}}</a></td>
                                        <td><a href="{{route('admin.settings.edit',$setting->id)}}">{{$setting->setting_value}}</a></td>
                                    </tr>
                                @empty
                                    <tr><td>No settings have been added for your user yet</td></tr>
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