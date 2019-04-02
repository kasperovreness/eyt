

@extends('layout')

@section('content')

<table class="table table-bordered table-stiped">
    <thead>
    <tr>
        <th>Level</th>
        <th>Part</th>
        <th>Value</th>
        <th>Response Time</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($scores as $score)
    <tr>
        <td>{{ $score->level }}</td>
        <td>{{ $score->part }}</td>
        <td>{{ $score->value }}</td>
        <td>{{ $score->responseTime }}</td>
    </tr>
    @endforeach
    </tbody>
</table>

@stop