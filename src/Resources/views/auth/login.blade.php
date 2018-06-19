@extends('churchnet::templates.frontend')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-offset-3 col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h3>Login to ChurchNet</h3>
                    <div class="row">
                        <div class="col-md-12">
                            <p class="text-center">using Facebook or Google:<br><br>
                            <a href="{{ url('redirect/facebook') }}" style="color:#3b5998;"><i class="fab fa-facebook fa-4x"></i></a>&nbsp;
                            <a href="{{ url('redirect/google') }}" style="color:#db3236;"><i class="fab fa-google-plus-square fa-4x"></i></a></p>
                            <p class="text-center"><small>We don't use your social media details at all - just the authentication mechanism.<br>And in the process, save you remembering another username and password :)</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
