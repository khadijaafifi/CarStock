@extends('layouts.app')

@section('title', 'Résultats de recherche')

@section('content')
    <h1>Résultats pour : "{{ $query }}"</h1>

    <div>
        <h3>Réponse de l'IA :</h3>
        <p>{{ $aiResponse }}</p>
    </div>

    <div class="row">
        @foreach($cars as $car)
            <div class="col-md-4">
                <div class="card">
                    <img src="{{ asset('storage/' . $car->image) }}" class="card-img-top" alt="{{ $car->marque }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $car->marque }} {{ $car->modele }}</h5>
                        <p class="card-text">{{ $car->description }}</p>
                        <p class="card-text">Prix: {{ $car->prix }} €</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
