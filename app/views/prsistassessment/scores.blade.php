

@extends('layout')

@section('content')

    <h3>Subscale Scores</h3>
    <table class="table table-bordered table-stiped">
      
    
    
    	<?php 
    	$cognitive = 0;
    	$behavioural = 0;
		$cognitiveAnswered = 0;
		$behaviouralAnswered = 0;
		?>
		
    	@foreach($scores as $score)
        	
        	<!-- //Calculate overall for cognitive -->
        	@if (strpos($score["item_type"], 'Cognitive Self-Regulation') !== false) 
        		<?php $cognitive = $cognitive + $score["item_score"]; ?>	
        		
        		@if ($score["item_score"] != 0) 
        			<?php $cognitiveAnswered = $cognitiveAnswered +1; ?>
        		@endif	
        	@endif
        	
        	@if (strpos($score["item_type"], 'Behavioural Self-Regulation') !== false) 
        		<?php $behavioural = $behavioural + $score["item_score"]; ?>	
        		
        		@if ($score["item_score"] != 0) 
        			<?php $behaviouralAnswered = $behaviouralAnswered +1; ?>
        		@endif		
 			@endif	
 		
        @endforeach
        
        <!--Calculate Average -->
        
        <?php
        if ($cognitiveAnswered != 0) {
        	$cognitive = $cognitive/$cognitiveAnswered;
        } else {
        	$cognitive = 0;
        }
        
        if ($behaviouralAnswered != 0) {
        	$behavioural = $behavioural/$behaviouralAnswered;
        } else {
        	$behavioural = 0;
        }
        
        
        ?>
        
                
        @if ($cognitive != 0)
        	<tr>
        	<td style="width:85%">Cognitive Self-Regulation</td>
        	<td style="width:15%">{{number_format($cognitive, 2)}}</td>
        	</tr>
        @else
            <tr>
        	<td style="width:85%">Cognitive Self-Regulation</td>
        	<td style="width:15%">.</td>
        	</tr>
        @endif

        @if ($behavioural != 0)
        	<tr>
        	<td style="width:85%">Behavioural Self-Regulation</td>
        	<td style="width:15%">{{number_format($behavioural, 2)}}</td>
        	</tr>
        @else
            <tr>
        	<td style="width:85%">Behavioural Self-Regulation</td>
        	<td style="width:15%">.</td>
        	</tr>
        @endif

        
    </table>


    <table class="table table-bordered table-stiped">
        <thead>
        <tr>
        	<!-- <th>Type</th> -->
            <th>Item</th>
            <th>Answer</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($scores as $score)
            <tr>
<!-- 
                <td>
                    {{ $score['item_type'] }}
                </td>
 -->
                <td>
                    {{ $score['item_no'] }}
                </td>
                @if ($score['item_score'] != 0)
                <td>
                    {{ $score['item_score'] }}
                </td>
                @else 
                <td>
                    .
                </td>
                @endif

            </tr>
        @endforeach
        </tbody>
    </table>

@stop