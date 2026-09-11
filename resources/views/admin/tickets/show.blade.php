@extends('adminlte::page')
@section('title', 'تیکت #'.$ticket->id)
@section('content_header')<h1>تیکت #{{ $ticket->id }}</h1>@stop
@section('content')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <div class="card"><div class="card-header"><strong>{{ $ticket->subject }}</strong><span class="float-left badge badge-secondary">{{ $ticket->status }}</span></div><div class="card-body"><p>{{ $ticket->message }}</p><small>{{ $ticket->user?->name ?? 'کاربر حذف‌شده' }} — {{ $ticket->created_at }}</small></div></div>
    @foreach($ticket->replies as $reply)
        <div class="card border-right border-primary"><div class="card-body"><p>{{ $reply->message }}</p><small>{{ $reply->user?->name ?? 'کاربر حذف‌شده' }} — {{ $reply->created_at }}</small></div></div>
    @endforeach
    @if($ticket->status !== 'closed')
        <div class="card"><form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}">@csrf<div class="card-body"><label for="message">پاسخ</label><textarea id="message" name="message" class="form-control" maxlength="5000" required>{{ old('message') }}</textarea></div><div class="card-footer"><button class="btn btn-primary" type="submit">ارسال پاسخ</button></div></form></div>
        <form method="POST" action="{{ route('admin.tickets.close', $ticket) }}">@csrf<button class="btn btn-danger" type="submit">بستن تیکت</button></form>
    @else
        <div class="alert alert-secondary">این تیکت بسته شده و پاسخ جدید نمی‌پذیرد.</div>
    @endif
@stop
