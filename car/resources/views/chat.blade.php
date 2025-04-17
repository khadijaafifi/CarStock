{{-- @extends('layouts.app')

@section('title', 'Chat Assistant')

@section('content')
    <div id="chatWidget">
        <div id="chatHeader">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Assistant AI</h5>
                <div>
                    <button id="showHistoryBtn" class="btn btn-sm btn-outline-light me-2" title="Afficher l'historique">
                        <i class="fas fa-history"></i>
                    </button>
                    <button id="resetChat" class="btn btn-sm btn-outline-light" title="Réinitialiser la conversation">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        </div>
        <div id="chatMessages"></div>
        <form id="chatForm">
            <div class="input-group">
                <input type="text" id="chatInput" class="form-control" placeholder="Posez votre question..." autocomplete="off">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </form>
</div>

    <!-- Panneau d'historique -->
    <div id="historyPanel">
        <button id="closeHistory" title="Fermer l'historique">
            <i class="fas fa-times"></i>
        </button>
        <div id="historyList"></div>
    </div>
@endsection 
