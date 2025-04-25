@extends('layouts.app')

@section('content')
<div>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="mb-0">Liste des sites</h1>
    <div>
    <a href="{{ route('sites.create') }}" class="btn btn-success">Ajouter un site</a>
    <a href="{{ route('cars.index') }}" class="btn btn-success">Retour</a>
</div>
</div>

@if ($sites->isEmpty())
    <div class="alert alert-info">
        Aucun site enregistré pour le moment.
    </div>
@else
    <ul class="list-group">
        @foreach ($sites as $site)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>{{ $site->name }} :</strong><br>
                    <a href="{{ $site->url }}" target="_blank">{{ $site->url }}</a>
                </div>
                <div>
                    <form action="{{ route('sites.destroy', $site->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </li>
        @endforeach
    </ul>
@endif
</div>
@endsection