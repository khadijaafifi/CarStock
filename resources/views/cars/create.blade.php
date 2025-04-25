@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-8">
        <div class="flex justify-between items-center mb-4 display-flex justify-content-space-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Ajouter un véhicule</h2>
        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:underline">← Retour</a>
        </div>
        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('cars.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Marque -->
                <div class="mb-4">
                    <label for="marque" class="block text-sm font-medium text-gray-700">Marque</label>
                    <input type="text" name="marque" id="marque" class="block w-full mt-1 border border-gray-300 rounded-lg" value="{{ old('marque') }}" required>
                    @error('marque')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Modele -->
                <div class="mb-4">
                    <label for="modele" class="block text-sm font-medium text-gray-700">Modèle</label>
                    <input type="text" name="modele" id="modele" class="block w-full mt-1 border border-gray-300 rounded-lg" value="{{ old('modele') }}" required>
                    @error('modele')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <!--Couleur-->
                <div class="mb-4">
                    <label for="couleur" class="block text-sm font-medium text-gray-700">Couleur</label>
                    <input type="text" name="couleur" class="block w-full mt-1 border border-gray-300 rounded-lg" value="{{ old('couleur') }}"required>

                    @error('couleur')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Annee -->
                <div class="mb-4">
                    <label for="annee" class="block text-sm font-medium text-gray-700">Année</label>
                    <input type="number" name="annee" id="annee"min="1900" max="2099"
                    step="1"
                    inputmode="numeric"
                    pattern="[0-9]*" class="block w-full mt-1 border border-gray-300 rounded-lg" value="{{ old('annee') }}" >
                    @error('annee')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prix -->
                <div class="mb-4">
                    <label for="prix" class="block text-sm font-medium text-gray-700">Prix</label>
                    <input type="number" name="prix" id="prix" class="block w-full mt-1 border border-gray-300 rounded-lg" value="{{ old('prix') }}" required>
                    @error('prix')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Déscription</label>
                    <textarea name="description" id="description" class="block w-full mt-1 border border-gray-300 rounded-lg" required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image -->
                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium text-gray-700">Image du véhicule</label>
                    <input type="file" name="image" id="image" class="block w-full mt-1 border border-gray-300 rounded-lg">
                    @error('image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
@endsection
