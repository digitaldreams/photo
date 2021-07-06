<div class="card card-default" style="min-height: 320px">
    <a href="{{route('photo::photos.show',$record->id)}}">
        {!! $photoRender->renderThumbnails($record) !!}
    </a>
    <div class="row">
        <h6 class="card-title col-10">

            <a href="{{route('photo::photos.show',$record->id)}}">
                {{$record->caption}}
            </a>
        </h6>
        <div class="btn-group col-2 text-center">
            <div class="dropdown" id="dropdown-{{$record->id}}">
                <a href="#" title="Click here to See Action Buttons" class="fa fa-ellipsis-v" data-toggle="dropdown"
                   role="button" aria-expanded="false">...
                </a>
                <ul class="dropdown-menu">
                    @can('update',$record)
                        <li>
                            <a class="btn btn-light" href="{{route('photo::photos.edit',$record->id)}}">
                                <span class="fa fa-pencil"></span> Edit
                            </a>
                        </li>
                    @endcan
                    @can('delete',$record)
                        <li>
                            <form class="btn btn-light" onsubmit="return confirm('Are you sure you want to delete?')"
                                  action="{{route('photo::photos.destroy',$record->id)}}" method="post"
                                  style="display: inline">
                                {{csrf_field()}}
                                {{method_field('DELETE')}}
                                <button type="submit" class="btn btn-default cursor-pointer  btn-sm"><i
                                        class="text-danger fa fa-times"></i>Delete
                                </button>
                            </form>
                        </li>
                        <li>
                            <a class="btn btn-light" href="{{route('photo::photos.download',$record->id)}}"><i
                                    class="fa fa-download"></i> Download</a>
                        </li>
                    @endcan
                </ul>
            </div>
        </div>
    </div>


    <div class="card-footer bg-transparent">
        @foreach($record->tags as $tag)
            <a class="link-dark" href="{{route('photo::photos.index',['tag'=>$tag->name])}}">{{$tag->name}}</a>
        @endforeach

    </div>

</div>
