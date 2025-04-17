
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">CarStock</a>
        
        <form class="d-flex ms-auto me-3" id="searchForm">
            <input class="form-control me-2" type="search" id="queryInput" 
                   placeholder="Rechercher ici." aria-label="Search">
            <button class="btn btn-outline-primary" type="submit">
                <i class="bi bi-wechat"></i>
            </button>
        </form>

        <div class="d-flex">
            @guest
                <a class="btn btn-outline-secondary me-2" href="{{ route('login') }}">
                    Connexion
                </a>
                <a class="btn btn-primary" href="{{ route('register') }}">
                    S'inscrire
                </a>
            @else
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger">
                        Déconnexion
                    </button>
                </form>
            @endguest
        </div>
    </div>
</nav>

<script>
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const query = document.getElementById('queryInput').value;
        
        // Ouvrir le chat et envoyer la requête
        document.getElementById('chatWidget').style.display = 'flex';
        document.getElementById('chatInput').value = query;
        document.getElementById('chatForm').dispatchEvent(new Event('submit'));
    });
</script>