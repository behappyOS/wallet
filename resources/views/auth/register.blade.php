@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4">
                    <h3 class="card-title text-center fw-bold mb-4">
                        <i class="bi bi-person-plus text-success"></i> Cadastro
                    </h3>

                    <form method="POST" action="{{ route('register.submit') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nome</label>
                            <input type="text" id="name" name="name" class="form-control"
                                   placeholder="Seu nome" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email" id="email" name="email" class="form-control"
                                   placeholder="seu@email.com" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Senha</label>
                            <input type="password" id="password" name="password" class="form-control"
                                   placeholder="********" required>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirmar Senha</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   class="form-control" placeholder="********" required>
                        </div>

                        <button type="submit" class="btn btn-success w-100 rounded-pill mb-3">
                            <i class="bi bi-check-circle"></i> Cadastrar
                        </button>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-decoration-none">
                                <i class="bi bi-box-arrow-in-right"></i> Já tenho conta
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
