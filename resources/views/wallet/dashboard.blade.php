@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Bem-vindo, {{ $user->name }}</h2>
        <p><strong>Saldo:</strong> R$ {{ number_format($user->balance, 2, ',', '.') }}</p>

        <a href="{{ route('wallet.deposit') }}" class="btn btn-success">Depositar</a>
        <a href="{{ route('wallet.transfer') }}" class="btn btn-primary">Transferir</a>

        <hr>
        <h4>Transações</h4>
        <table class="table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Tipo</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            @foreach($transactions as $tx)
                <tr>
                    <td>{{ $tx->id }}</td>
                    <td>{{ ucfirst($tx->type) }}</td>
                    <td>R$ {{ number_format($tx->amount, 2, ',', '.') }}</td>
                    <td>{{ $tx->status }}</td>
                    <td>{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($tx->status === 'completed')
                            <form method="POST" action="{{ route('wallet.reverse', $tx->id) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-danger">Reverter</button>
                            </form>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        {{ $transactions->links() }}
    </div>
@endsection
