@auth
  <?php
  if (Auth::user()->avatar) {
      $imgsrc=Auth::user()->avatar;
  } else {
      $imgsrc=asset('/vendor/bishopm/images/profile.png');
  }?>
  <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.10/summernote.js"></script>
  <script type="text/javascript">
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="token"]').attr('value')
          }
      });
      $('#newcomment').summernote({ 
        height: 100,
        toolbar: [
          ['style', ['bold', 'italic', 'underline']],
          ['link', ['linkDialogShow', 'unlink']]
        ],
        popover: {
          image: [],
          link: [],
          air: []
        }
      });
      $('#publishButton').on('click',function(){
      	user={{Auth::user()->id}};
      	if (user){
          newcom='<div class="row mt-3"><div class="col-xs-2 col-sm-1"><img class="img-responsive img-circle img-thumbnail" width="50px" src="{{$imgsrc}}"></div><div class="col-xs-10 col-sm-11" style="font-size: 80%">{{Auth::user()->name}}: ' + $('textarea#newcomment').val() + '<div><i>{{date("j M")}}</i></div></div></div>';
      	}
        $.ajax({
            type : 'POST',
            url : '{{$url}}',
            data : {'newcomment':$('textarea#newcomment').val(),'user':user},
            success: function(){
            	$(newcom).appendTo('#allcomments');
            }
        });
      });
      function deleteme(id){
        $.ajax({
            type : 'POST',
            url : '{{route('deletecomment')}}',
            data : {'id':id},
            success: function(e){
              rowid='#row' + e;
              $(rowid).hide();
            }
        });
      }
</script>
@endauth