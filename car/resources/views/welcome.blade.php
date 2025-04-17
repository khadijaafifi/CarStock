@extends('layouts.app')
@section('title', 'Accueil')
@section('content')
<div class="container display-flex justify-content-center">
    <h1>Nos voitures disponibles</h1>
    <div class="row">
        @foreach ($cars as $car)
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $car->marque }} {{ $car->modele }}</h5>
                        {{-- <p class="card-text">Année : {{ $car->annee }}</p> --}}
                        <p class="card-text">Prix : {{ $car->prix }} €</p>
                        <p class="card-text">{{ $car->description }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
