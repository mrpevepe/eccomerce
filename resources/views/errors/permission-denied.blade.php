@extends('layouts.main')
@section('title', 'Cadastro users Admin')
@section('content')
    <h1>Você não tem permissão para acessar essa página</h1>
    <p>Será redirecionado em <span id="countdown">5</span> segundos...</p>

    <script>
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');
        const interval = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            if (countdown <= 0) {
                clearInterval(interval);
                window.location.href = '/'; // redirect homepage
            }
        }, 1000);
    </script>

@endsection