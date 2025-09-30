@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4">
                    <h3 class="card-title text-center fw-bold mb-4">
                        <i class="bi bi-arrow-down-circle text-success"></i> Depósito
                    </h3>

                    <form method="POST" action="{{ route('wallet.deposit.submit') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="amount" class="form-label fw-semibold">Valor</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" name="amount" id="amount"
                                       class="form-control" required placeholder="0,00">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100 rounded-pill">
                            <i class="bi bi-wallet2"></i> Confirmar Depósito
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
