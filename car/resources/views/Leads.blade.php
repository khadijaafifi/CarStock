@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold mb-6">Liste des leads</h2>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            {{-- Tableau des leads --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Résumé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($leads as $lead)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $lead->name?? '' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{  $lead->email??'' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $lead->phone??'' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $lead->summary }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $lead->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" onsubmit="return confirm('Supprimer ce lead ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-semibold">Supprimer</button>
                            </form>
                        </td>
                        
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">Aucun lead trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-6 py-4 border-t">
            {{ $leads->links() }}
        </div>
    </div>
</div>
@endsection


