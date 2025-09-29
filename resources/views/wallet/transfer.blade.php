@extends('layouts.app')

@section('content')
    <div class="container">
        <h3>Transferência</h3>
        <form method="POST" action="{{ route('wallet.transfer.submit') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">E-mail do destinatário</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Valor</label>
                <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Transferir</button>
        </form>
    </div>
@endsection
