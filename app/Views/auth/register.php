<?php require_once 'app/Views/layout/header.php'; ?>

<div class="row justify-content-center py-4">
    <div class="col-11 col-sm-10 col-md-7 col-lg-6 col-xl-5">
        
        <div class="card shadow-sm border-light-subtle">
            <div class="card-body p-4">
                
                <h2 class="h4 text-center mb-4 text-body fw-bold">Rejestracja pracownika</h2>

                <form method="POST" action="<?= BASE_URL ?>register">

                    <div class="mb-3">
                        <label for="username" class="form-label small fw-semibold text-muted">Login systemowy</label>
                        <input type="text" id="username" class="form-control" name="username" placeholder="Unikalna nazwa użytkownika" required autocomplete="username">
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-sm-6">
                            <label for="first_name" class="form-label small fw-semibold text-muted">Imię</label>
                            <input type="text" id="first_name" class="form-control" name="first_name" placeholder="Jan" required>
                        </div>

                        <div class="col-sm-6">
                            <label for="last_name" class="form-label small fw-semibold text-muted">Nazwisko</label>
                            <input type="text" id="last_name" class="form-control" name="last_name" placeholder="Kowalski" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label small fw-semibold text-muted">Adres E-mail</label>
                        <input type="email" id="email" class="form-control" name="email" placeholder="jan.kowalski@serwis.pl" required>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label small fw-semibold text-muted">Hasło do konta</label>
                        <input type="password" id="password" class="form-control" name="password" placeholder="Skonfiguruj silne hasło" required autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        <i class="bi bi-person-check me-2"></i> Utwórz konto
                    </button>
                </form>
                
            </div>
        </div>

    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>