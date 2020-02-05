<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>Name</th>
        <th>Description</th>
        <th>&nbsp;</th>
    </tr>
    </thead>
    <tbody>
    @foreach($records as $record)
        <tr>
            <td>
                <a href="{{route('photo::albums.show',$record->id)}}"> {{$record->name }}
                </a>
            </td>
            <td> {{$record->description }} </td>
            <td>
                <a href="{{route('photo::albums.edit',$record->id)}}">
                    <span class="fa fa-pencil-alt"></span>
                </a>
                <form onsubmit="return confirm('Are you sure you want to delete?')"
                      action="{{route('photo::albums.destroy',$record->id)}}" method="post" style="display: inline">
                    {{csrf_field()}}
                    {{method_field('DELETE')}}
                    <button type="submit" class="btn btn-default cursor-pointer  btn-sm"><i
                                class="text-danger fa fa-trash"></i></button>
                </form>
            </td>
        </tr>

    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="3">
            {{{$records->render()}}}
        </td>
    </tr>
    </tfoot>
</table>