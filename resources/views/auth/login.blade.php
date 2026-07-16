@extends('layouts.app')

@section('title', 'Acceso administrativo | LabCustom')

@section('content')
    <section class="admin-auth-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7 col-lg-5">
                    <div class="auth-card">
                        <div class="eyebrow">Panel administrativo</div>
                        <h1 class="h3 fw-bold mt-2 mb-4">Entrar a LabCustom</h1>

                        <form method="POST" action="{{ route('login.store') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="email">Correo</label>
                                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="password">Password</label>
                                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" id="remember" name="remember" type="checkbox" value="1">
                                <label class="form-check-label" for="remember">Mantener sesion</label>
                            </div>
                            <button class="btn btn-dark w-100" type="submit">
                                <i class="bi bi-lock-fill me-2"></i>Entrar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
