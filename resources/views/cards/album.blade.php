<div class="card card-default">
    <div class="card-header">
        <div class="row">
            <div class="col-sm-9">
                {{$record->id}}
            </div>
            <div class="col-sm-3">
                <div class="btn-group" style="float: left">
                    <a href="{{route('photo::albums.edit',$record->id)}}">
                        <span class="fa fa-pencil"></span>
                    </a>
                    <a href="{{route('photo::albums.show',$record->id)}}">
                        <span class="fa fa-eye"></span>
                    </a>
                    <form onsubmit="return confirm('Are you sure you want to delete?')"
                          action="{{route('@@table@@.destroy',$record->id)}}" method="post" style="display: inline">
                        {{csrf_field()}}
                        {{method_field('DELETE')}}
                        <button type="submit" class="btn btn-default cursor-pointer  btn-sm"><i
                                    class="text-danger fa fa-remove"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card-block">
        <table class="table table-bordered table-striped">
            <tbody>
            <tr>
                <th>User Id</th>
                <td>{{$record->user_id}}</td>
            </tr>
            <tr>
                <th>Name</th>
                <td>{{$record->name}}</td>
            </tr>
            <tr>
                <th>Description</th>
                <td>{{$record->description}}</td>
            </tr>

            </tbody>
        </table>
    </div>
</div>
