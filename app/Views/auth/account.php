<?php require_once 'app/Views/layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="view-wrapper-md">
        
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                
                <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom flex-wrap gap-3">
                    <div>
                        <h2 class="h3 mb-1 text-body">
                            <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                        </h2>
                        <div class="d-flex gap-2 align-items-center text-muted small flex-wrap">
                            <span>Rola: <strong class="text-body text-uppercase"><?= htmlspecialchars($user['account_type']) ?></strong></span>
                            <span class="text-secondary opacity-25">|</span>
                            <span>Ostatnie logowanie: <strong class="text-body"><?= $user['last_login'] ? date('d.m.Y H:i', strtotime($user['last_login'])) : 'Pierwsze' ?></strong></span>
                        </div>
                    </div>
                    
                    <a href="<?= BASE_URL ?>logout" class="btn btn-danger d-inline-flex align-items-center">
                        <i class="bi bi-box-arrow-right me-2"></i> Wyloguj się
                    </a>
                </div>

                <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success d-flex align-items-center shadow-sm mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                        <div><?= htmlspecialchars($successMessage) ?></div>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-danger d-flex align-items-center shadow-sm mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <div><?= htmlspecialchars($errorMessage) ?></div>
                    </div>
                <?php endif; ?>

                <div class="card bg-body-tertiary border-light-subtle mb-4 shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="h5 card-title mb-3 pb-2 border-bottom text-body d-flex align-items-center">
                            <i class="bi bi-person-lines-fill me-2 text-primary"></i> Dane osobowe
                        </h3>
                        
                        <form method="POST" action="<?= BASE_URL ?>account">
                            <input type="hidden" name="update_profile" value="1">
                            
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label small fw-semibold text-muted">Imię</label>
                                    <input type="text" id="first_name" class="form-control" name="first_name" value="<?= htmlspecialchars($user['first_name'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label small fw-semibold text-muted">Nazwisko</label>
                                    <input type="text" id="last_name" class="form-control" name="last_name" value="<?= htmlspecialchars($user['last_name'] ?? '') ?>" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label small fw-semibold text-muted">Adres E-mail</label>
                                    <input type="email" id="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="phone_number" class="form-label small fw-semibold text-muted">Numer telefonu</label>
                                    <input type="text" id="phone_number" class="form-control" name="phone_number" placeholder="+48..." value="<?= htmlspecialchars($user['phone_number'] ?? '') ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-muted">Login systemowy i ID konta</label>
                                <input type="text" class="form-control bg-body border-secondary border-opacity-10 text-muted" value="<?= htmlspecialchars($user['username']) ?> (#<?= htmlspecialchars($user['id']) ?>)" disabled style="cursor: not-allowed;">
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary px-4">Zapisz profil</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card bg-body-tertiary border-light-subtle mb-4 shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="h5 card-title mb-3 pb-2 border-bottom text-body d-flex align-items-center">
                            <i class="bi bi-shield-lock-fill me-2 text-warning"></i> Bezpieczeństwo konta
                        </h3>
                        
                        <form method="POST" action="<?= BASE_URL ?>account">
                            <input type="hidden" name="change_password" value="1">
                            
                            <div class="mb-3">
                                <label for="old_password" class="form-label small fw-semibold text-muted">Aktualne hasło</label>
                                <input type="password" id="old_password" class="form-control" name="old_password" placeholder="••••••••" required autocomplete="current-password">
                            </div>
                            
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="new_password" class="form-label small fw-semibold text-muted">Nowe hasło</label>
                                    <input type="password" id="new_password" class="form-control" name="new_password" placeholder="Min. 6 znaków" required autocomplete="new-password">
                                </div>
                                <div class="col-md-6">
                                    <label for="confirm_password" class="form-label small fw-semibold text-muted">Powtórz nowe hasło</label>
                                    <input type="password" id="confirm_password" class="form-control" name="confirm_password" placeholder="Wpisz nowe hasło ponownie" required autocomplete="new-password">
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-secondary px-4">Aktualizuj hasło</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card bg-body-tertiary border-secondary border-opacity-25 shadow-sm" style="border-style: dashed;">
                    <div class="card-body p-4">
                        <h3 class="h5 card-title mb-2 text-muted d-flex align-items-center">
                            <i class="bi bi-palette-fill me-2"></i> Preferencje interfejsu 
                            <span class="badge bg-secondary ms-2 fs-7 fw-normal">W budowie</span>
                        </h3>
                        
                        <div class="row g-3 opacity-50" style="pointer-events: none;">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Motyw wizualny</label>
                                <select class="form-select" disabled>
                                    <option>Jasny (Domyślny)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted">Język systemu</label>
                                <select class="form-select" disabled>
                                    <option>Polski</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>