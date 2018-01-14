<div class="card card-default">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-9">
                {{$record->caption}}
            </div>
            <div class="col-sm-3">
                <div class="btn-group" style="float: left">

                </div>
            </div>
        </div>
    </div>
    <div class="card-block">

    </div>
</div>
<div class="card card-default">
    <img class="card-img-top" src="{{$record->getSrc()}}" alt="{{$record->title}}">
    <div class="card-body">
        <h5 class="card-title"> {{$record->caption}}</h5>
        <a class="card-link" href="{{route('photo::photos.edit',$record->id)}}">
            <span class="fa fa-pencil"></span>
        </a>
        <a class="card-link" href="{{route('photo::photos.show',$record->id)}}">
            <span class="fa fa-eye"></span>
        </a>
        <form class="card-link" onsubmit="return confirm('Are you sure you want to delete?')"
              action="{{route('photo::photos.destroy',$record->id)}}" method="post" style="display: inline">
            {{csrf_field()}}
            {{method_field('DELETE')}}
            <button type="submit" class="btn btn-default cursor-pointer  btn-sm"><i
                        class="text-danger fa fa-remove"></i></button>
        </form>
    </div>
    <div class="card-footer bg-transparent">
        <i class="fa fa-map-marker"></i> {{$record->getLocationName()}}
    </div>

</div>
