<form action="{{$route ?? route('photo::photos.store')}}" method="POST" enctype="multipart/form-data"
      onsubmit="return disableBtn()">
    {{csrf_field()}}
    <input type="hidden" name="_method" value="{{$method ?? 'POST'}}"/>
    <div class="m-form__group form-group form-row">
        <div class="col-md-10">
            <label for="file" class="col-form-label">Image</label>
            <input type="file" accept="image/*" class="form-control {{ $errors->has('file') ? ' is-invalid' : '' }}"
                   name="file" id="file"
                   onchange="checkSize(2097152,'file')"
                   placeholder="">
            @if($errors->has('file'))
                <div class="invalid-feedback">
                    <strong>{{ $errors->first('file') }}</strong>
                </div>
            @endif
            <p class="help-block">You should either upload a custom image or Icon</p>
        </div>
        <div class="col-md-2">
            <img height="80px" id="file_preview" src="{{$model->getFormat()}}">
        </div>

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
        <label for="title">Title</label>
        <input type="text" class="form-control {{ $errors->has('title') ? ' is-invalid' : '' }}" name="title" id="title"
               value="{{old('title',$model->title)}}"
               placeholder="e.g. Sunset" maxlength="191">
        @if($errors->has('title'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('title') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group">
        <label>Album</label>
        <select class="form-control" name="album_ids[]" id="photo_album" multiple>
            @if(isset($albums))
                @foreach($albums as $album)
                    <option value="{{$album->id}}" {{in_array($album->id,$allRelatedIds)?'selected':''}}>{{$album->name}}</option>
                @endforeach
            @endif
        </select>
    </div>

    <div class="form-group text-right ">
        <input type="reset" class="btn btn-default" value="Clear"/>
        <input type="submit" class="btn btn-primary" value="Save"/>
    </div>
</form>