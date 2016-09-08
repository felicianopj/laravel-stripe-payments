@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="col-md-10">
			@if(session('msg'))
				<div class="alert alert-success" role="alert">
					{{ session('msg') }}
				</div>
			@endif

			@if(session('error'))
				<div class="alert alert-danger" role="alert">
					{{ session('error') }}
				</div>
			@endif
			<div class="row">
				@foreach ($products as $product)
				<form action="{{ route('pay', $product->id) }}" method="POST">
					{{ csrf_field() }}
					<div class="col-sm-5 col-md-5">
				    	<div class="thumbnail">
				     		<div class="caption">
						        <h3>{{ $product->name }}</h3>
						        <p>{{ $product->description }}</p>
						        <p>Buy for ${{ substr_replace($product->price, '.', 2, 0) }}</p>
						        <p>
						        	<script
									    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
									    data-key="{{ env('STRIPE_KEY') }}"
									    data-amount="{{ $product->price }}"
									    data-name="My Store"
										data-locale="auto"
										data-currency="brl">
									</script>
						        </p>
				      		</div>
				    	</div>
				  	</div>
				</form>
				@endforeach
			</div>
		</div>
	</div>
@endsection