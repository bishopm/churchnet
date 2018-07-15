@extends('churchnet::templates.frontend')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-offset-3 col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h3>Login to ChurchNet</h3>
                    <p>Log in using any one of the three options below</p>
                    <div class="row">
                        <div class="col-md-12">
                        <ul class="nav nav-pills nav-fill">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#social">Social Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#username">Username</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#phone">Cellphone</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="social" role="tabpanel" aria-labelledby="home-tab">
                                <a href="{{ url('redirect/facebook') }}" style="color:#3b5998;"><i class="fab fa-facebook fa-4x"></i></a>&nbsp;
                                <a href="{{ url('redirect/google') }}" style="color:#db3236;"><i class="fab fa-google-plus-square fa-4x"></i></a></p>
                            </div>
                            <div class="tab-pane fade" id="username" role="tabpanel" aria-labelledby="profile-tab">
                                <div class="mt-3 form-group">
                                    <input class="form-control" placeholder="Username" name="name">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="password" type="password">
                                </div>
                            </div>
                            <div class="tab-pane fade" id="phone" role="tabpanel" aria-labelledby="contact-tab">
                                <div class="mt-3 form-group">
                                    <input class="form-control" placeholder="Cellphone" name="phone">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="password" type="password">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
