<form action="{{$route or route('photo::photos.store')}}" method="POST" enctype="multipart/form-data">
    {{csrf_field()}}
    <input type="hidden" name="_method" value="{{$method or 'POST'}}"/>
    <input type="hidden" name="place_api_data" value="" id="place_api_data">
    <div class="row">
        <div class="col-sm-2">
            <div class="card">
                <div class="card-img-top">
                    <img src="{{$model->getFormat()}}" class="img img-fluid" id="imagePreview">
                </div>
            </div>
        </div>
        <div class="col-sm-10">
            <div class="form-group ">
                <label for="exampleFormControlFile1">Upload Your Image</label>
                <input type="file" onchange="readURL(this,'imagePreview')" name="file" class="form-control-file {{ $errors->has('file') ? ' is-invalid' : '' }}"
                       id="exampleFormControlFile1" accept="image/x-png,image/gif,image/jpeg">

                @if($errors->has('file'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('file') }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="form-group">
        <label for="caption">Caption</label>
        <input type="text" class="form-control {{ $errors->has('caption') ? ' is-invalid' : '' }}" name="caption"
               id="caption" value="{{old('caption',$model->caption)}}"
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

    <div class="form-group ">
        <label for="address">Address</label>
        <div class="input-group">
            <div class="input-group-addon">
                <i class="fa fa-map-pin"></i>
            </div>
            <input type="text" class="form-control {{ $errors->has('address') ? ' is-invalid' : '' }}" name="address"
                   id="address"
                   value="<?php echo old('address', $model->getLocationName()) ?>"
                   placeholder="e.g. 11th street,Dhaka,Bangladesh"
                   maxlength="200">
            <div class="input-group-addon">
                <img src="https://developers.google.com/places/documentation/images/powered-by-google-on-white.png">
            </div>
        </div>

        <ul class="list-group" id="locationDropdown">

        </ul>
        @if($errors->has('address'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('address') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group text-right ">
        <input type="reset" class="btn btn-default" value="Clear"/>
        <input type="submit" class="btn btn-primary" value="Save"/>
    </div>
</form>