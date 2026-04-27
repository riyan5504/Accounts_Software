@extends('backend.master')

@section('content')
    <table border="1" width="100%">
    <tr>
        <th>Item</th>
        <th>Total In</th>
        <th>Total Out</th>
        <th>Stock</th>
    </tr>

    @foreach($stocks as $row)
    <tr>
        <td>{{ $row->item->item_name ?? '' }}</td>
        <td>{{ $row->total_in }}</td>
        <td>{{ $row->total_out }}</td>
        <td>{{ $row->stock }}</td>
    </tr>
    @endforeach
</table>
@endsection