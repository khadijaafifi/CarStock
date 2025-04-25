@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-8">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Car</h2>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('cars.update', $car->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Marque -->
                <div class="mb-4">
                    <label for="marque" class="block text-sm font-medium text-gray-700">Brand</label>
                    <input type="text" name="marque" id="marque" class="block w-full mt-1 border border-gray-300 rounded-lg" value="{{ old('marque', $car->marque) }}" required>
                    @error('marque')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Modele -->
                <div class="mb-4">
                    <label for="modele" class="block text-sm font-medium text-gray-700">Model</label>
                    <input type="text" name="modele" id="modele" class="block w-full mt-1 border border-gray-300 rounded-lg" value="{{ old('modele', $car->modele) }}" required>
                    @error('modele')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Annee -->
                <div class="mb-4">
                    <label for="annee" class="block text-sm font-medium text-gray-700">Year</label>
                    <input type="number" name="annee" id="annee" class="block w-full mt-1 border border-gray-300 rounded-lg" value="{{ old('annee', $car->annee) }}" required>
                    @error('annee')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prix -->
                <div class="mb-4">
                    <label for="prix" class="block text-sm font-medium text-gray-700">Price</label>
                    <input type="number" name="prix" id="prix" class="block w-full mt-1 border border-gray-300 rounded-lg" value="{{ old('prix', $car->prix) }}" required>
                    @error('prix')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" class="block w-full mt-1 border border-gray-300 rounded-lg" required>{{ old('description', $car->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image -->
                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium text-gray-700">Car Image</label>
                    <input type="file" name="image" id="image" class="block w-full mt-1 border border-gray-300 rounded-lg">
                    @error('image')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    @if($car->image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $car->image) }}" alt="Car Image" class="w-32 h-32 object-cover">
                        </div>
                    @endif
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">Update Car</button>
                </div>
            </form>
        </div>
    </div>
@endsection
