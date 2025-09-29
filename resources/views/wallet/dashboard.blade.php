@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mb-4 align-items-center">
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm border-0 rounded-4 bg-gradient" style="background: linear-gradient(135deg, #4facfe, #4455ab) !important; color: #fff;">
                    <div class="card-body">
                        <h5 class="card-title fw-bold"><i class="bi bi-wallet2"></i> Saldo Atual</h5>
                        <p class="card-text display-5 fw-semibold">
                            R$ {{ number_format($user->balance, 2, ',', '.') }}
                        </p>
                        <a href="{{ route('wallet.deposit') }}" class="btn btn-light btn-sm btn-custom me-2">
                            <i class="bi bi-arrow-down-circle"></i> Depositar
                        </a>
                        <a href="{{ route('wallet.transfer') }}" class="btn btn-light btn-sm btn-custom">
                            <i class="bi bi-arrow-right-circle"></i> Transferir
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 d-flex align-items-center justify-content-center mb-3">
                <img src="{{ asset('wallet.png') }}"
                     alt="Wallet Animated" class="img-fluid" style="max-height: 180px;">
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-dark text-white fw-bold">
                Histórico de Transações
            </div>
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
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
                    @forelse($transactions as $tx)
                        <tr>
                            <td>{{ $tx->id }}</td>
                            <td>
                                @switch($tx->type)
                                    @case('deposit')
                                        <span class="badge bg-success">Depósito</span>
                                        @break
                                    @case('transfer')
                                        <span class="badge bg-warning text-dark">Envio</span>
                                        @break
                                    @case('receive')
                                        <span class="badge bg-info text-dark">Recebido</span>
                                        @break
                                    @case('reversal')
                                        <span class="badge bg-danger">Reversão</span>
                                        @break
                                @endswitch
                            </td>
                            <td>R$ {{ number_format($tx->amount, 2, ',', '.') }}</td>
                            <td>{{ ucfirst($tx->status) }}</td>
                            <td>{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($tx->status === 'completed' && $tx->type !== 'reversal')
                                    <form method="POST" action="{{ route('wallet.reverse', $tx->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-arrow-counterclockwise"></i> Reverter
                                        </button>
                                    </form>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Nenhuma transação encontrada.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $transactions->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
@endsection
