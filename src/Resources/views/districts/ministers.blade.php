@extends('churchnet::templates.frontend')

@section('title',$title)

@section('css')
@stop


@section('content')
<div class="container mt-5">
    <h3>{{$title}}</h3>

        <table>
        @forelse ($ministers as $minister)
            <tr>
                <td>{!!$minister['name']!!}</b></td>
                <td>{{json_encode($minister)}}
                </td>
                <td><a href="{{url('/')}}/circuits/{{$minister['circuit']['id']}}">{{$minister['circuit']['name']}}</a></td>
            </tr>
        @empty
            No ministers have been added yet.
        @endforelse
        </table>

</div>
@endsection

@section('js')
@stop