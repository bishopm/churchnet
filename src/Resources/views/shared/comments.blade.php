<h4>Comments</h4>
@auth
	<div id="allcomments">
		@foreach ($comments as $comment)
			<div id="row{{$comment->id}}" class="row top5">
				<div class="col-xs-3 col-sm-2">
					@if ($comment->commented->avatar)
		                <img width="50px" class="img-responsive img-circle img-thumbnail" src="{!!Auth::user()->avatar!!}">
		            @else
		                <img width="50px" class="img-responsive img-circle img-thumbnail" src="{{asset('/vendor/bishopm/images/profile.png')}}">
		            @endif
		            </a>
		            <div><i>{{date("j M",strtotime($comment->created_at))}}</i></div>
				</div>
				<div class="col-xs-9 col-sm-10" style="font-size: 80%">
					<a href="{{url('/')}}/users/{{$comment->commented->id}}">{{$comment->commented->name}}</a>{!!$comment->comment!!}
					@if (isset($comment->rate))
						<div class="ratingro" data-rate-value={{$comment->rate}}></div>
					@endif
					@if ($comment->commented->id==Auth::user()->id)
						<a title="Delete my comment" onclick="deleteme({{$comment->id}});" href="#"><i class="fa fa-2x fa-trash"></i></a>
					@endif
				</div>
			</div>
		@endforeach 
	</div>
	<hr>
	<div class="row mb-5">
		<div class="col-xs-3 col-sm-2">
			<div><b>{{Auth::user()->name}}</b></div>
			<a href="{{url('/')}}/users/{{Auth::user()->id}}">
			@if (Auth::user()->avatar)
                <img width="50px" class="img-responsive img-circle img-thumbnail" src="{!!Auth::user()->avatar!!}">
            @else
                <img width="50px" class="img-responsive img-circle img-thumbnail" src="{{asset('/vendor/bishopm/images/profile.png')}}">
            @endif
            </a><br>
            @if (isset($rating))
				<div class="rating"></div>
			@endif
			<a id="publishButton" class="btn btn-primary">Send</a>
		</div>
		<div class="col-xs-9 col-sm-10">
			@if (isset($rating))
				<textarea rows="5" name="newcomment" id="newcomment" class="form-control" placeholder="Leave a comment and star rating to help others considering this resource."></textarea>
			@elseif ($comments)
				<textarea rows="5" name="newcomment" id="newcomment" class="form-control" placeholder="Join the conversation :)"></textarea>
			@else
				<textarea rows="5" name="newcomment" id="newcomment" class="form-control" placeholder="Make a comment / ask a question"></textarea>
			@endif
		</div>
	</div>
@endauth