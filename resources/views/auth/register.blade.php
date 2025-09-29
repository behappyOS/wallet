@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2>Cadastro</h2>
        <form method="POST" action="{{ route('register.submit') }}">
            @csrf

            <div class="mb-3">
                <label>Nome</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Senha</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Confirmar Senha</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Cadastrar</button>
            <a href="{{ route('login') }}" class="btn btn-link">Já tenho conta</a>
        </form>
    </div>
@endsection
