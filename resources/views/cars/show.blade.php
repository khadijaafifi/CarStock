@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 font-weight-bold text-dark">
            <span class="border-bottom border-primary pb-2">Détails du véhicule</span>
        </h1>
        <a href="{{ route('home') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
        </a>
    </div>

    <!-- Carte détaillée - Split 50/50 -->
    <div class="card mb-4 shadow-sm">
        <div class="row no-gutters">
           <!-- Section image (50%) -->
<div class="col-lg-6 bg-light d-flex align-items-center justify-content-center p-4">
    @if($car->image)
        <img src="{{ asset('storage/' . $car->image) }}" 
             alt="{{ $car->marque }} {{ $car->modele }}"
             class="rounded shadow-sm"
             style="width: 100%; max-width: 90%; height: auto; object-fit: contain; transition: transform 0.3s ease;"
             onmouseover="this.style.transform='scale(1.02)'"
             onmouseout="this.style.transform='scale(1)'">
    @else
        <div class="text-center text-muted">
            <i class="fas fa-car fa-5x mb-4"></i>
            <p class="lead">Image non disponible</p>
        </div>
    @endif
</div>

            <!-- Section contenu (50%) -->
            <div class="col-lg-6 p-4 d-flex flex-column">
                <div>
                    <!-- Titre avec badge année -->
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <h2 class="h4 font-weight-bold text-dark">
                            {{ $car->marque }} <span class="text-primary">{{ $car->modele }}</span>
                        </h2>
                        <span class="badge badge-info">
                            {{ $car->annee ?? 'N/A' }}
                        </span>
                    </div>

                    <!-- Métadonnées -->
                    <div class="row mb-4">
                        <div class="col-6 d-flex align-items-center">
                            <i class="fas fa-palette text-muted mr-2"></i>
                            <div>
                                <p class="text-muted small">Couleur</p>
                                <p class="font-weight-bold">{{ $car->couleur }}</p>
                            </div>
                        </div>
                        <div class="col-6 d-flex align-items-center">
                            <i class="fas fa-tag text-muted mr-2"></i>
                            <div>
                                <p class="text-muted small">Prix</p>
                                <p class="font-weight-bold text-primary">{{ number_format($car->prix, 2) }} MAD</p>
                            </div>
                        </div>
                        <!-- Note moyenne -->
                        <div class="col-6 d-flex align-items-center">
                            <i class="fas fa-star text-muted mr-2"></i>
                            <div>
                                <p class="text-muted small">Note moyenne</p>
                                <p class="font-weight-bold">
                                    {{ $car->rating ?? 'Pas encore noté' }}/5
                                    @if(isset($car->reviews_count))
                                        <span class="text-muted small">({{ $car->reviews_count }} avis)</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h3 class="h5 font-weight-bold text-dark mb-2">Description</h3>
                        <p class="text-muted">
                            {{ $car->description }}
                        </p>
                    </div>

                    <!-- Système de notation -->
                    <div class="d-flex align-items-center mb-4">
                        <div class="d-flex align-items-center">
                            @php
                                $rating = $car->rating ?? 0;
                                $reviewCount = $car->reviews->count();
                            @endphp
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $rating ? 'text-warning' : 'text-muted' }}"></i>
                            @endfor
                            <span class="ml-2 text-muted">
                                {{ $rating }} ({{ $reviewCount }} avis)
                            </span>
                        </div>
                        @if($reviewCount > 0)
                            <div class="ml-4 text-info">
                                <i class="fas fa-comments"></i><a href="route('/chat')" class="text-info"> Donnez votre avis via le chat</a>
                            </div>
                        @endif
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="#" class="btn btn-primary w-48">
                            <i class="fas fa-phone-alt mr-2"></i> Contacter
                        </a>
                        <a href="#" class="btn btn-outline-primary w-48">
                            <i class="fas fa-share-alt mr-2"></i> Partager
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
