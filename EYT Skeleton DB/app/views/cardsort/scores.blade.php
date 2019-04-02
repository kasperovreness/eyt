

@extends('layout')

@section('content')

<table class="table table-bordered table-stiped">

	<thead>
	<tr>
		<th>Start</th>
		<th>Level 1 duration (sec)</th>
		<th>Level 2 duration (sec)</th>
		<th>Level 3 duration (sec)</th>
	</tr>
	</thead>
	<tbody>
	@foreach ($games as $game)
		<tr>
		
			<td> {{$game->ts_start}} </td>
			
			<?php
			$total_1 = (strtotime($game->ts_lvl1_end) - strtotime($game->ts_lvl1_start));
			$total_2 = (strtotime($game->ts_lvl2_end) - strtotime($game->ts_lvl2_start));
			$total_3 = (strtotime($game->ts_lvl3_end) - strtotime($game->ts_lvl3_start));
			?>
			<td>{{ $total_1 }}</td>
			<td>{{ $total_2 }}</td>
			<td>{{ $total_3 }}</td>
					
		</tr>
		@endforeach
	</tbody>
</table>

<table class="table table-bordered table-stiped">
    <thead>
    <tr>
        <th>Level</th>
        <th>Card</th>
        <th>Value</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($scores as $score)
    <tr>
        <td>{{ $score->level }}</td>
        <td>{{ $score->card }}</td>
        <td>{{ $score->value }}</td>
    </tr>
    @endforeach
    </tbody>
</table>

@stop