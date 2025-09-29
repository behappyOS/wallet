@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Depósito</h3>
        <form method="POST" action="{{ route('wallet.deposit.submit') }}">
            @csrf
            <div class="mb-3">
                <label for="amount" class="form-label">Valor</label>
                <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success">Depositar</button>
        </form>
    </div>
@endsection
