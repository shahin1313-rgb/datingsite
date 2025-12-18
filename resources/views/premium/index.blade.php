@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <h1 class="display-4 mb-4">Upgrade to Premium</h1>
            <p class="lead">Get noticed faster and chat without limits.</p>
            
            <div class="card-deck mt-5">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h3 class="my-0">VIP Gold</h3>
                    </div>
                    <div class="card-body">
                        <h2 class="card-title">$9.99 <small class="text-muted">/ mo</small></h2>
                        <ul class="list-unstyled mt-3 mb-4">
                            <li>✅ Unlimited Messages</li>
                            <li>✅ See who liked you</li>
                            <li>✅ Undo accidental skips</li>
                            <li>✅ Profile boost once a week</li>
                        </ul>
                        <button type="button" class="btn btn-lg btn-block btn-primary">Choose Plan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection