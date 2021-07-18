<form action="{{$route ?? route('photo::photos.store')}}" method="POST" enctype="multipart/form-data"
      id="photoUploadForm" form="photoUploadForm">
    {{csrf_field()}}
    <input type="hidden" name="_method" value="{{$method ?? 'POST'}}"/>
    <input type="hidden" name="returnUrl" value="{{request('returnUrl')}}">
    <div class="m-form__group form-group form-row">
        <div class="col-md-9">
            <label for="file" class="col-form-label">Image</label>
            <input type="file" class="form-control {{ $errors->has('file') ? ' is-invalid' : '' }}"
                   name="file" id="file"
                   onchange="checkSize(2097152,'file')"
                   placeholder="" accept="image/*" {{empty($model->id)?'required':''}}>
            @if($errors->has('file'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('file') }}</strong>
                </div>
            @endif
            <p class="help-block text-muted">Image size must be less than 2MB</p>
        </div>
        <div class="col-md-3">
            @if(!empty($model->id) && !empty($thumbs=$photoRender->getThumbnailUrls($model->thumbnails)))
                <img height="120px" id="file_preview" src="{{$thumbs[0]['url']}}">
            @else
                <img height="120px" id="file_preview" src="{{config('photo.default')}}">
            @endif
        </div>

    </div>
    <div class="form-group">
        <label>
            <input type="checkbox" name="crop" value="yes">
            Yes crop my image to {{config('photo.maxHeight')}}px Height and {{config('photo.maxWidth')}}px Width
        </label>
    </div>
    <div class="form-group">
        <label for="caption">Caption</label>
        <input type="text" class="form-control {{ $errors->has('caption') ? ' is-invalid' : '' }}" name="caption"
               id="photo_caption" value="{{old('caption',$model->caption)}}"
               placeholder="e.g. Sunset of sea beach" maxlength="191" required>
        @if($errors->has('caption'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('caption') }}</strong>
            </div>
        @endif
    </div>
    <div class="form-group">
        <label for="caption">Tags</label>
        <select class="form-control" id="photo_tags" name="tags[]" multiple>
            @foreach($model->tags as $tag)
                <option selected>{{$tag->name}}</option>
            @endforeach
        </select>
        @if($errors->has('tags.*'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('tags') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group text-right ">
        <input type="submit" class="btn btn-primary" value="Upload"/>
    </div>
</form>
