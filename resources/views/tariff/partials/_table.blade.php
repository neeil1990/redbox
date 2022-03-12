<div class="table-responsive">
    <table class="table" id="{{ $id }}">
        <tbody>
        @foreach($total as $t)
        <tr>
            <th style="width:50%">{{ $t['title'] }}:</th>
            <td>{{ $t['value'] }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
