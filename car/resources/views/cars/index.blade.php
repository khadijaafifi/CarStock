@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 font-weight-bold">Liste des véhicules</h2>
        @if(Auth::check() && str_ends_with(Auth::user()->email, '@admin.com'))
        <div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-tasks mr-1"></i> Gérer le stock
        </a>
        <a href="{{ route('leads.index') }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-address-book mr-2"></i> Voir les leads
        </a>
        </div>
        @endif
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="row">
        @foreach($cars as $car)
        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
            <div class="card h-100 border-0 shadow-sm">
                <!-- Image avec ratio fixe 16:9 -->
                <div class="position-relative" style="padding-top: 56.25%; overflow: hidden;">
                    @if($car->image)
                    <img src="{{ asset('storage/' . $car->image) }}" 
                         alt="{{ $car->marque }} {{ $car->modele }}"
                         class="position-absolute top-0 left-0 w-100 h-100 object-fit-cover">
                    @else
                    <div class="position-absolute top-0 left-0 w-100 h-100 bg-light d-flex align-items-center justify-content-center">
                        <i class="fas fa-car text-muted fa-3x"></i>
                    </div>
                    @endif
                </div>

                <div class="card-body pb-2">
                    <!-- Marque et modèle -->
                    <h5 class="card-title font-weight-bold mb-1 text-truncate">
                        {{ $car->marque }} - {{ $car->modele }}
                    </h5>
                    
                    <!-- Couleur et année -->
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge badge-light border mr-2">{{ $car->annee }}</span>
                        <span class="text-muted small">{{ $car->couleur }}</span>
                    </div>
                    
                    <!-- Prix -->
                    <div class="h5 text-primary font-weight-bold mb-3">
                        {{ number_format($car->prix, 2) }} MAD
                    </div>
                    
                    <!-- Description (limitée à 2 lignes) -->
                    <p class="card-text text-secondary small line-clamp-2 mb-3">
                        {{ $car->description }}
                    </p>
                </div>

                <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                    <a href="{{ route('cars.show', $car->id) }}" 
                       class="btn btn-primary btn-sm btn-block">
                        <i class="fas fa-eye mr-1"></i> Voir détails
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    /* Styles complémentaires */
    .object-fit-cover {
        object-fit: cover;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
</style>
@endsection