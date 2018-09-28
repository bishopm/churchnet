@extends('churchnet::templates.frontend')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-offset-3 col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <form action="{{route('login')}}" method="post">{{ csrf_field() }}
                        <h3>Login to ChurchNet</h3>
                        <div class="row">
                            <div class="col-sm-12">Login with your email address and password</div>
                            <br><br>
                            <div class="col-sm-6 offset-sm-3">
                                <div class="form-group has-feedback {{ $errors->has('email') ? 'has-error' : '' }}">
                                    <input id="email" name="email" class="form-control" value="{{ old('email') }}"
                                        placeholder="Email">
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 offset-sm-3">
                                <div class="form-group has-feedback {{ $errors->has('password') ? 'has-error' : '' }}">
                                    <input type="password" name="password" class="form-control"
                                        placeholder="Password">
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4 offset-sm-4">
                            <button type="submit"
                                    class="btn btn-default btn-block btn-flat">Login
                            </button>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-12 mt-3">
                            Or using one of your other accounts
                        </div>
                        <div class="col-md-12">
                            <a href="{{ url('redirect/facebook') }}" style="color:#3b5998;"><i class="fab fa-facebook fa-4x"></i></a>&nbsp;
                            <a href="{{ url('redirect/google') }}" style="color:#db3236;"><i class="fab fa-google-plus-square fa-4x"></i></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
