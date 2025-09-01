@extends('layouts.app')

@section('content')
    <div style="padding: 2rem;">
        <h1>Welcome to your Dashboard, {{ Auth::user()->name }}!</h1>
        <p>This is your protected dashboard area.</p>
        <p>You can manage your files or start a chat using the links in the navigation bar above.</p>
    </div>
@endsection
