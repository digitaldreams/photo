<div class="card card-default">
    <a href="{{route('photo::photos.show',$record->id)}}">
        <img class="card-img-top" src="{{isset($fullSize)?$record->getUrl():$record->getFormat()}}"
             alt="{{$record->caption}}" title="{{$record->title}}">
    </a>
    <div class="card-body">
        <a href="{{route('photo::photos.show',$record->id)}}">
            <h5 class="card-title"> {{$record->caption}}</h5>
            @if(empty($record->title))
                <p class="card-title">{{$record->title}}</p>
            @endif
        </a>
    </div>
    <div class="card-footer bg-transparent">
        <div class="row">
            <div class="col-sm-8 p-0">
                @foreach($record->albums as $album)
                    <a href="{{route('photo::albums.show',$album->id)}}">{{$album->name}}</a>
                @endforeach
            </div>
            <div class="col-sm-4 text-right p-0">
                @can('update',$record)
                    <a class="card-link" href="{{route('photo::photos.edit',$record->id)}}">
                        <span class="fa fa-pencil-alt"></span>
                    </a>
                @endcan
                @can('delete',$record)
                    <form class="card-link" onsubmit="return confirm('Are you sure you want to delete?')"
                          action="{{route('photo::photos.destroy',$record->id)}}" method="post" style="display: inline">
                        {{csrf_field()}}
                        {{method_field('DELETE')}}
                        <button type="submit" class="btn btn-default cursor-pointer  btn-sm"><i
                                    class="text-danger fa fa-times"></i></button>
                    </form>
                @endcan
            </div>
        </div>
    </div>

</div>
