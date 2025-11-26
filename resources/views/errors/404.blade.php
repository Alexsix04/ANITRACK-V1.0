@extends('errors::minimal')

@section('title', __('Página no encontrada'))
@section('code', '')
@section('message')
<div style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background-color: #fff;
    text-align: center;
    overflow: hidden;
">
    <!-- Imagen centrada y responsiva -->
    <img src="{{ asset('images/errors/404.png') }}" alt="404"
         style="
            max-width: 90%;
            max-height: 80%;
            width: auto;
            height: auto;
        ">

    <!-- Botón centrado debajo de la imagen -->
    <a href="{{ url('/') }}" style="
        margin-top: 20px;
        padding: 12px 25px;
        background-color: rgba(0,0,0,0.6);
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s;
    "
    onmouseover="this.style.backgroundColor='rgba(0,0,0,0.8)';"
    onmouseout="this.style.backgroundColor='rgba(0,0,0,0.6)';"
    >Volver al inicio</a>
</div>

<style>
    html, body {
        margin: 0;
        padding: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }

    @media (max-width: 600px) {
        img {
            max-width: 80%;
            max-height: 60%;
        }
        a {
            padding: 10px 20px;
            font-size: 14px;
        }
    }
</style>
@endsection