<div class="well" id="frmUpdateGame">
    <h4>Edit Game Data</h4>
    <br/>
    <table class="table table-bordered table-striped">
        <tr>
            <td>
                <label>Study Name</label><br/>
                <input type="text" name="study" value="{{$game->test_name}}" />
            </td>
            <td>
                <label>Child ID</label><br/>
                <input type="text" name="child_id" value="{{$game->subject_id}}" />
            </td>
            <td>
                <label>Session ID</label><br/>
                <input type="text" name="session_id" value="{{$game->session_id}}" />
            </td>
        </tr>
        <tr>
            <td>
                <label>Grade</label><br/>
                <input type="text" name="grade" value="{{$game->grade}}" />
            </td>
            <td>
                <label>D.O.B.</label><br/>
                <input type="text" name="dob" placeholder="dd-mm-yyyy" value="{{$game->dob}}" />
            </td>
            <td>
                <label>Age</label><br/>
                <input type="text" name="age" value="{{$game->age}}" />
            </td>
        </tr>
        <tr>
            <td>
                <label>Gender</label><br/>
                <input type="text" name="gender" value="{{$game->sex}}" />
            </td>
            <td colspan="2">
                <br/>
                <button id="btnUpdateGame" class="btn btn-success pull-right" data-game_id="{{$game->id}}" data-game_type="{{$type}}">Save Changes</button>
            </td>
        </tr>
    </table>
    <br/>
    <h4>Past Changes</h4>
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>User</th>
            <th>Timestamp</th>
            <th>Data</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($changes as $change)
    {{--*/ $old = json_decode($change->old, true);
            $new = json_decode($change->new, true);
    /*--}}
<tr>
<td>{{$change->user->username}}</td>
<td>{{date('d F, Y', strtotime($change->created_at))}}</td>
<td>
    <table class="table table-bordered">
        <tr>
            <th>Item</th>
            <th>Old Value</th>
            <th>New Value</th>
        </tr>
        @if (!empty($old))
        @foreach (array_keys($old) as $key)
            @if ($old[$key] != $new[$key] && $key != "updated_at")
                <tr>
                    <td>{{$key}}</td>
                    <td>{{$old[$key]}}</td>
                    <td>{{$new[$key]}}</td>
                </tr>
            @endif
        @endforeach
        @endif
    </table>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>