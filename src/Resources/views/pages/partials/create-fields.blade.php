{{ Form::bsText('title','Title','Title') }}
{{ Form::bsTextarea('body','Body','Body') }}
<div class='form-group '>
  <label for="tags">Tags</label>
  <select name="tags[]" class="tag-select" multiple>
  @foreach ($tags as $tag)
    <option value="{{$tag->name}}">{{$tag->name}}</option>
  @endforeach
  </select>
</div>