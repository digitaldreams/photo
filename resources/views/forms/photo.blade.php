<form action="{{$route ?? route('photo::photos.store')}}" method="POST" enctype="multipart/form-data"
      id="photoUploadForm" form="photoUploadForm">
    {{csrf_field()}}
    <input type="hidden" name="_method" value="{{$method ?? 'POST'}}"/>
    <div class="m-form__group form-group form-row">
        <div class="col-md-9">
            <label for="file" class="col-form-label">Image</label>
            <input type="file" class="form-control {{ $errors->has('file') ? ' is-invalid' : '' }}"
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
        <div class="col-md-3">
            <img height="120px" id="file_preview" src="{{$model->getFormat()}}">
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


    <div class="form-group text-right ">
        <input type="submit" class="btn btn-primary" value="Upload"/>
    </div>
</form>
