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
                <img src="{{ asset('wallet.png') }}" alt="Wallet Animated" class="img-fluid" style="max-height: 180px;">
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
                        <th>De / Para</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($transactions as $tx)
                        @php
                            $isParticipant = $tx->user_id === auth()->id()
                                             || (isset($tx->meta['original_sender']) && $tx->meta['original_sender'] === auth()->id())
                                             || (isset($tx->meta['to']) && $tx->meta['to'] === auth()->id());
                            $canReverse = $isParticipant && $tx->status === 'completed' && $tx->type !== 'reversal';
                        @endphp
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
                            <td>
                                @if($tx->status === 'completed')
                                    <span class="badge bg-success">Completo</span>
                                @elseif($tx->status === 'reverted')
                                    <span class="badge bg-warning text-dark">Revertido</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($tx->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($tx->type === 'deposit')
                                    Depósito em conta
                                @elseif($tx->type === 'transfer')
                                    Para: {{ optional(\App\Models\User::find(data_get($tx->meta, 'to')))->name ?? '-' }}
                                @elseif($tx->type === 'receive')
                                    De: {{ optional(\App\Models\User::find(data_get($tx->meta, 'original_sender')))->name ?? '-' }}
                                @elseif($tx->type === 'reversal')
                                    Para: {{ optional(\App\Models\User::find(data_get($tx->meta, 'sender')))->name ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($canReverse)
                                    <form method="POST" action="{{ route('wallet.reverse', $tx->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-arrow-counterclockwise"></i> Reverter
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-sm btn-secondary" disabled>-</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Nenhuma transação encontrada.</td>
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
