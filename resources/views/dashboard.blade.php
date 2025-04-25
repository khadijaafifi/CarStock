@extends('layouts.app')

@section('content')
<div class="container py-5">
    <!-- En-tête -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h2 class="h4 fw-bold mb-3 mb-md-0 text-dark">📦 Gestion du stock véhicules</h2>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
            <a href="{{ route('cars.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i> Ajouter un véhicule
            </a>
        </div>
    </div>

    <!-- Tableau -->
    <div class="card shadow border-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-success text-dark">
                    <tr>
                        <th>🚗 Marque</th>
                        <th>🎨 Couleur</th>
                        <th>📅 Année</th>
                        <th>💰 Prix</th>
                        <th>🖼️ Image</th>
                        <th>📝 Description</th>
                        <th class="text-end">⚙️ Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cars as $car)
                    <tr>
                        <td class="fw-semibold">{{ $car->marque }}</td>
                        <td>
                            <span class="badge rounded-pill" style="background-color: {{ $car->couleur_hex ?? '#ccc' }};">
                                {{ $car->couleur }}
                            </span>
                        </td>
                        <td><span class="text-muted">{{ $car->annee }}</span></td>
                        <td><span class="text-primary fw-bold">{{ number_format($car->prix, 2) }} MAD</span></td>
                        <td>
                            @if($car->image)
                                <img src="{{ asset('storage/' . $car->image) }}" alt="Car Image" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <span class="text-muted small">-</span>
                            @endif
                        </td>
                        <td class="text-truncate" style="max-width: 200px;">{{ $car->description }}</td>
                        <td class="text-end">
                            <a href="{{ route('cars.edit', $car->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-pen me-1"></i> Modifier
                            </a>
                            <form action="{{ route('cars.destroy', $car->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Supprimer ce véhicule ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash me-1"></i> Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer text-end">
            {{ $cars->links() }}
        </div>
    </div>
</div>
@endsection
