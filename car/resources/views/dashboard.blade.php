@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <h2 class="text-2xl font-bold text-gray-800">
            Gestion de stock
        </h2>
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <a href="{{ route('home') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left mr-2"></i> Retour
            </a>
            <a href="{{ route('cars.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i> Ajouter un véhicule
            </a>
        </div>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marque</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Couleur</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Année</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($cars as $car)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $car->marque }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="inline-flex items-center">
                                <span class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $car->couleur_hex ?? '#cccccc' }}"></span>
                                {{ $car->couleur }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $car->annee }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">{{ number_format($car->prix, 2) }} MAD</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($car->image)
                            <img src="{{ asset('storage/' . $car->image) }}" alt="Car Image" class="w-12 h-12 object-cover rounded-md shadow-sm">
                            @else
                            <span class="text-gray-400 text-sm">Aucune image</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $car->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                            <a href="{{ route('cars.edit', $car->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit mr-1"></i> Modifier
                            </a>
                            <form action="{{ route('cars.destroy', $car->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?')">
                                    <i class="fas fa-trash mr-1"></i> Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            {{ $cars->links() }}
        </div>
    </div>
</div>

<style>
    .btn {
        @apply inline-flex items-center px-4 py-2 rounded-md font-medium transition-colors;
    }
    .btn-sm {
        @apply px-3 py-1 text-sm;
    }
    .btn-primary {
        @apply bg-blue-600 text-white hover:bg-blue-700;
    }
    .btn-outline {
        @apply border border-gray-300 text-gray-700 hover:bg-gray-50;
    }
    .btn-warning {
        @apply bg-yellow-500 text-white hover:bg-yellow-600;
    }
    .btn-danger {
        @apply bg-red-500 text-white hover:bg-red-600;
    }
</style>
@endsection