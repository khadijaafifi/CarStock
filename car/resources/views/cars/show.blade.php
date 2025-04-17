@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 mt-8 max-w-7xl">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            <span class="border-b-4 border-blue-500 pb-2">Détails du véhicule</span>
        </h1>
        <a href="{{ route('home') }}" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left mr-2"></i> Retour à la liste
        </a>
    </div>

    <!-- Carte détaillée - Split 50/50 -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden flex flex-col lg:flex-row lg:h-[500px]">
        <!-- Section image (50%) -->
        <div class="lg:w-1/2 bg-gray-50 flex items-center justify-center p-4 h-[250px] lg:h-full">
            @if($car->image)
                <img src="{{ asset('storage/' . $car->image) }}" 
                     alt="{{ $car->marque }} {{ $car->modele }}"
                     class="h-full w-auto object-contain mx-auto">
            @else
                <div class="text-center text-gray-400">
                    <i class="fas fa-car fa-5x mb-4"></i>
                    <p class="text-lg">Image non disponible</p>
                </div>
            @endif
        </div>

        <!-- Section contenu (50%) -->
        <div class="lg:w-1/2 p-6 lg:p-8 flex flex-col justify-between">
            <div>
                <!-- Titre avec badge année -->
                <div class="flex justify-between items-start mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">
                        {{ $car->marque }} <span class="text-blue-600">{{ $car->modele }}</span>
                    </h2>
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $car->annee ?? 'N/A' }}
                    </span>
                </div>

                <!-- Métadonnées -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-palette text-gray-400 mr-2"></i>
                        <div>
                            <p class="text-xs text-gray-500">Couleur</p>
                            <p class="font-medium">{{ $car->couleur }}</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-tag text-gray-400 mr-2"></i>
                        <div>
                            <p class="text-xs text-gray-500">Prix</p>
                            <p class="font-bold text-blue-600">{{ number_format($car->prix, 2) }} MAD</p>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Description</h3>
                    <p class="text-gray-600 leading-relaxed">
                        {{ $car->description }}
                    </p>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex space-x-4 mt-4">
                <a href="#" class="btn btn-primary flex-1">
                    <i class="fas fa-phone-alt mr-2"></i> Contacter
                </a>
                <a href="#" class="btn btn-outline-primary flex-1">
                    <i class="fas fa-share-alt mr-2"></i> Partager
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .btn {
        @apply px-4 py-2 rounded-lg font-medium transition duration-300 flex items-center justify-center;
    }
    .btn-primary {
        @apply bg-blue-600 text-white hover:bg-blue-700;
    }
    .btn-outline-primary {
        @apply border border-blue-600 text-blue-600 hover:bg-blue-50;
    }

    /* Responsive override */
    @media (max-width: 1023px) {
        .lg\:h-\[500px\] {
            height: auto;
        }
    }
</style>
@endsection
