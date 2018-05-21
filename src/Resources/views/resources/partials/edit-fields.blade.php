{{ Form::bsText('title','Title','Title',$resource->title) }}
{{ Form::bsText('url','url','URL',$resource->url) }}
{{ Form::bsTextarea('description','Description','Description',$resource->description) }}
<div class='form-group '>
  <label for="tags">Tags</label>
  <select name="tags[]" class="tag-select" multiple>
  @foreach ($tags as $tag)
    @if ((count($rtags)) and (in_array($tag->name,$rtags)))
        <option selected value="{{$tag->name}}">{{$tag->name}}</option>
    @else
        <option value="{{$tag->name}}">{{$tag->name}}</option>
    @endif
  @endforeach
  </select>
</div>