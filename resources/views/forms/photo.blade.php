<form action="{{$route or route('photo::photos.store')}}" method="POST">
    {{csrf_field()}}
    <input type="hidden" name="_method" value="{{$method or 'POST'}}"/>
    <input type="hidden" name="place_id" value="{{$model->getLocationPlaceId()}}" id="api_place_id">
    <div class="row">
        <div class="col-sm-2">
            <div class="card">
                <div class="card-img-top">
                    <img src="{{$model->getSrc()}}" class="img img-fluid">
                </div>
            </div>
        </div>
        <div class="col-sm-10">
            <div class="form-group {{ $errors->has('src') ? ' has-danger' : '' }}">
                <label for="exampleFormControlFile1">Upload Your Image</label>
                <input type="file" name="file" class="form-control-file"
                       id="exampleFormControlFile1" required>

                @if($errors->has('file'))
                    <div class="invalid-feedback">
                        <strong>{{ $errors->first('file') }}</strong>
                    </div>
                @endif
            </div>
        </div>

    </div>


    <div class="form-group {{ $errors->has('caption') ? ' has-danger' : '' }}">
        <label for="caption">Caption</label>
        <input type="text" class="form-control" name="caption" id="caption" value="{{old('caption',$model->caption)}}"
               placeholder="" maxlength="191" required>
        @if($errors->has('caption'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('caption') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group {{ $errors->has('title') ? ' has-danger' : '' }}">
        <label for="title">Title</label>
        <input type="text" class="form-control" name="title" id="title" value="{{old('title',$model->title)}}"
               placeholder="" maxlength="191">
        @if($errors->has('title'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('title') }}</strong>
            </div>
        @endif
    </div>


    <div class="form-group {{ $errors->has('address') ? ' has-danger' : '' }}">
        <label for="location">Address</label>
        <input type="text" class="form-control" name="address" id="address"
               value="<?php echo old('address', $model->getLocationName()) ?>"
               placeholder="e.g. 11th street,Dhaka,Bangladesh"
               maxlength="200">

        <ul class="list-group" id="locationDropdown">

        </ul>
        @if($errors->has('location'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('location') }}</strong>
            </div>
        @endif
    </div>


    <div class="form-group text-right ">
        <input type="reset" class="btn btn-default" value="Clear"/>
        <input type="submit" class="btn btn-primary" value="Save"/>

    </div>
</form>