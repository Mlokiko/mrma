<?php require_once 'app/Views/layout/header.php'; ?>

<div class="row justify-content-center py-4">
    <div class="view-wrapper-sm">
        
        <div class="card shadow-sm border-light-subtle">
            <div class="card-body p-4">
                
                <h2 class="h4 text-center mb-4 text-body fw-bold">Logowanie</h2>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center small py-2 mb-3" role="alert">
                        <i class="bi bi-exclamation-circle-fill me-2"></i>
                        <div><?= htmlspecialchars($error) ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>login">
                    <div class="mb-3">
                        <label for="username" class="form-label small fw-semibold text-muted">Nazwa użytkownika</label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary text-muted"><i class="bi bi-person"></i></span>
                            <input type="text" id="username" class="form-control" name="username" placeholder="Wpisz swój login" required autocomplete="username">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label small fw-semibold text-muted">Hasło</label>
                        <div class="input-group">
                            <span class="input-group-text bg-body-tertiary text-muted"><i class="bi bi-lock"></i></span>
                            <input type="password" id="password" class="form-control" name="password" placeholder="Wpisz swoje hasło" required autocomplete="current-password">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Zaloguj się
                    </button>
                </form>
                
            </div>
        </div>

    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>