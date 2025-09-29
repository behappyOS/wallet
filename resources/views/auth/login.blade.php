@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2>Login</h2>
        <form method="POST" action="{{ route('login.submit') }}">
            @csrf

            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required autofocus>
            </div>

            <div class="mb-3">
                <label>Senha</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Entrar</button>
            <a href="{{ route('register') }}" class="btn btn-link">Criar conta</a>
        </form>
    </div>
@endsection
