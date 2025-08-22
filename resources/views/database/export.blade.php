@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Export Database') }}</div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <p>Klik tombol di bawah ini untuk mengekspor database ke file SQL.</p>
                    
                    <form action="{{ route('database.export') }}" method="POST">
                        @csrf
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Export Database') }}
                            </button>
                        </div>
                    </form>

                    <div class="mt-4">
                        <h5>Informasi Database</h5>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <strong>Connection:</strong> {{ config('database.default') }}
                            </li>
                            <li class="list-group-item">
                                <strong>Database:</strong> {{ config('database.connections.' . config('database.default') . '.database') }}
                            </li>
                            <li class="list-group-item">
                                <strong>Host:</strong> {{ config('database.connections.' . config('database.default') . '.host') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
