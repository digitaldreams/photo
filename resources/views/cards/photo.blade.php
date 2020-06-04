<div class="card card-default">
    <a href="{{route('photo::photos.show',$record->id)}}">
        <img class="card-img-top" src="{{isset($fullSize)?$record->getUrl():$record->getFormat()}}"
             alt="{{$record->caption}}" style="max-height: 300px" title="{{$record->title}}">
    </a>
    <div class="card-body">
        <a href="{{route('photo::photos.show',$record->id)}}">
            <h5 class="card-title"> {{$record->caption}}</h5>
        </a>

    </div>
    <div class="card-footer bg-transparent">
        @foreach($record->albums as $album)
            <a class="badge badge-light" href="{{route('photo::albums.show',$album->id)}}">{{$album->name}}</a>
        @endforeach
        <div class="btn-group btn-group-sm">
            @can('update',$record)
                <a class="btn btn-light" href="{{route('photo::photos.edit',$record->id)}}">
                    <span class="fa fa-pencil"></span> Edit
                </a>
            @endcan
            @can('delete',$record)
                <form class="btn btn-light" onsubmit="return confirm('Are you sure you want to delete?')"
                      action="{{route('photo::photos.destroy',$record->id)}}" method="post" style="display: inline">
                    {{csrf_field()}}
                    {{method_field('DELETE')}}
                    <button type="submit" class="btn btn-default cursor-pointer  btn-sm"><i
                            class="text-danger fa fa-times"></i>Delete
                    </button>
                </form>
            @endcan
        </div>

    </div>

</div>
