@extends('layouts.app')
@section('content')
<div>
<h1 class="mb-4">Ajouter un site</h1>

<form action="{{ route('sites.store') }}" method="POST" class="needs-validation" novalidate>
    @csrf

    <div class="mb-3">
        <label for="name" class="form-label">Nom :</label>
        <input type="text" name="name" id="name" class="form-control" required>
        <div class="invalid-feedback">
            Veuillez entrer le nom du site.
        </div>
    </div>

    <div class="mb-3">
        <label for="url" class="form-label">URL :</label>
        <input type="url" name="url" id="url" class="form-control" required>
        <div class="invalid-feedback">
            Veuillez entrer une URL valide.
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Ajouter</button>
</form>

<script>
    // Bootstrap form validation
    (function () {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
</div>
@endsection