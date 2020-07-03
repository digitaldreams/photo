
    <table class="table table-bordered table-responsive table-striped table-hover">
        <thead>
        <tr>
            <th colspan="2">Exif Data</th>
        </tr>
        </thead>
        <tbody>
        @if($record->captured_at)
            <tr>
                <th>Captured At</th>
                <td>{{$record->captured_at->format('d M Y h:i A')}}</td>
            </tr>
        @endif
        @if($record->exif['Make'])
            <tr>
                <th>Make</th>
                <td>{{$record->exif['Make']}}</td>
            </tr>
        @endif
        @if($record->exif['Model'])
            <tr>
                <th>Make</th>
                <td>{{$record->exif['Model']}}</td>
            </tr>
        @endif
        @if($record->exif['FileSize'])
            <tr>
                <th>Size</th>
                <td>{{$record->exif['FileSize']}}</td>
            </tr>
        @endif
        </tbody>
    </table>

