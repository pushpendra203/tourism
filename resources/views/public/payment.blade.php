@include('public.layout.header')

@component('public.layout.breadcrumb', ['breadcrumb' => ['Home' => '/', 'Plans' => '/plan']])
    @slot('active') Checkout @endslot
@endcomponent

@component('public.partials.page-header')
    @slot('title') Book: {{ $plan->title }} @endslot
@endcomponent

<div id="site-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @elseif(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card mb-4">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Booking Summary</h4>
                        <p><strong>Plan:</strong> {{ $plan->title }}</p>
                        <p><strong>Location:</strong> {{ $plan->locationName->location ?? '-' }}</p>
                        <p><strong>Start:</strong> {{ date('d M, Y', strtotime($plan->start_time)) }}</p>
                        <p><strong>End:</strong> {{ date('d M, Y', strtotime($plan->end_time)) }}</p>
                        <p><strong>Price:</strong> ₹{{ number_format($plan->price, 2) }}</p>
                        <p><strong>Duration:</strong> {{ $plan->duration }}</p>
                    </div>
                </div>

                <form action="{{ route('razorpay.payment') }}" method="POST" id="payment-form">
                    @csrf
                    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
                    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
                </form>

                <div class="text-center">
                    <button id="pay-button" class="btn btn-primary px-5">Pay ₹{{ number_format($plan->price, 2) }}</button>
                </div>

            </div>
        </div>
    </div>
</div>

@include('public.layout.footer')

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.getElementById('pay-button').onclick = function (e) {
        e.preventDefault();

        var options = {
            "key": "{{ env('RAZORPAY_KEY') }}",
            "amount": "{{ $plan->price * 100 }}",
            "currency": "INR",
            "name": "Trip Genie",
            "description": "Booking for {{ $plan->title }}",
            "handler": function (response) {
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                document.getElementById('razorpay_signature').value = response.razorpay_signature;
                document.getElementById('payment-form').submit();
            },
            "prefill": {
                "name": "{{ auth()->user()->name ?? 'Guest' }}",
                "email": "{{ auth()->user()->email ?? 'guest@example.com' }}",
                "contact": "{{ auth()->user()->phone ?? '9999999999' }}"
            },
            "theme": {
                "color": "#0d6efd"
            }
        };

        var rzp = new Razorpay(options);
        rzp.open();
    }
</script>
