<form action="{{$route ?? route('photo::albums.store')}}" method="POST">
    {{csrf_field()}}
    <input type="hidden" name="_method" value="{{$method ?? 'POST'}}"/>
    <div class="form-group {{ $errors->has('user_id') ? ' has-danger' : '' }}">
        <label for="user_id">User Id</label>
        <input type="text" class="form-control" name="user_id" id="user_id" value="{{old('user_id',$model->user_id)}}"
               placeholder="">
        @if($errors->has('user_id'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('user_id') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group {{ $errors->has('name') ? ' has-danger' : '' }}">
        <label for="name">Name</label>
        <input type="text" class="form-control" name="name" id="name" value="{{old('name',$model->name)}}"
               placeholder="" maxlength="150" required="required">
        @if($errors->has('name'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('name') }}</strong>
            </div>
        @endif
    </div>

    <div class="form-group {{ $errors->has('description') ? ' has-danger' : '' }}">
        <label for="description">Description</label>
        <input type="text" class="form-control" name="description" id="description"
               value="{{old('description',$model->description)}}" placeholder="" maxlength="191">
        @if($errors->has('description'))
            <div class="invalid-feedback">
                <strong>{{ $errors->first('description') }}</strong>
            </div>
        @endif
    </div>


    <div class="form-group text-right ">
        <input type="reset" class="btn btn-default" value="Clear"/>
        <input type="submit" class="btn btn-primary" value="Save"/>

    </div>
</form>