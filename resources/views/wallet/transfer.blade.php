@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4">
                    <h3 class="card-title text-center fw-bold mb-4">
                        <i class="bi bi-arrow-left-right text-primary"></i> Transferência
                    </h3>

                    <form method="POST" action="{{ route('wallet.transfer.submit') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">E-mail do destinatário</label>
                            <input type="email" name="email" id="email" class="form-control"
                                   placeholder="exemplo@dominio.com" required>
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label fw-semibold">Valor</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" step="0.01" min="0.01" name="amount" id="amount"
                                       class="form-control" required placeholder="0,00">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill">
                            <i class="bi bi-send"></i> Enviar Transferência
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
