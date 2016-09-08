@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="col-md-10">
			<h3>Orders</h3>
			@foreach ($orders as $order)
				<ul class="list-group">
					<li class="list-group-item">
						{{ $order->email}}
						-
						{{ $order->product }}
					</li>
				</ul>
			@endforeach
		</div>
	</div>
@endsection