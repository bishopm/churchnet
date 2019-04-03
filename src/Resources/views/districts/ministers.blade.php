@extends('churchnet::templates.frontend')

@section('title',$title)

@section('css')
@stop


@section('content')
<div class="container mt-5">
    <h3>{{$title}}</h3>

        <table class="table table-striped">
        <thead class="thead-dark">
            <tr><th></th><th>Minister</th><th>Circuit</th><th>Status</th></tr>
        </thead>
        @forelse ($ministers as $minister)
            <tr>
                <td><img width="50px;" src="{{url('/')}}/vendor/bishopm/images/face.png"/></td>
                <td style="vertical-align:middle">{!!$minister['name']!!}</b></td>
                <td style="vertical-align:middle"><a href="{{url('/')}}/circuits/{{$minister['circuit']['name']['slug']}}">{{$minister['circuit']['name']['circuit']}}</a></td>
                <td style="vertical-align:middle">
                    @if (isset($minister['tags']))
                        {{implode(', ',$minister['tags'])}}
                    @endif
                </td>
            </tr>
        @empty
            No ministers have been added yet.
        @endforelse
        </table>

</div>
@endsection

@section('js')
@stop