<?php require_once 'app/Views/layout/header.php'; ?>

<div class="view-wrapper-md row justify-content-center">
    <div class="col-11 col-md-8 col-lg-6">
        
        <div class="card shadow-sm">
            <div class="card-body p-4">
                
                <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom flex-wrap gap-2">
                    <h2 class="h4 mb-0 text-body">Szczegóły użytkownika</h2>
                    <a href="<?= BASE_URL ?>admin_users_list" class="text-muted small text-decoration-none d-inline-flex align-items-center">
                        <i class="bi bi-arrow-left me-1"></i> Powrót do listy
                    </a>
                </div>

                <div class="d-flex flex-column gap-3">
                    
                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary border-opacity-10" style="border-style: dashed !important;">
                        <span class="text-muted small fw-semibold text-uppercase">Identyfikator systemowy:</span>
                        <strong class="text-body">#<?= htmlspecialchars($user['id']) ?></strong>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary border-opacity-10" style="border-style: dashed !important;">
                        <span class="text-muted small fw-semibold text-uppercase">Login (Nazwa):</span>
                        <strong class="text-body"><?= htmlspecialchars($user['username']) ?></strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary border-opacity-10" style="border-style: dashed !important;">
                        <span class="text-muted small fw-semibold text-uppercase">Imię i Nazwisko:</span>
                        <span class="text-body fw-medium"><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary border-opacity-10" style="border-style: dashed !important;">
                        <span class="text-muted small fw-semibold text-uppercase">Adres E-mail:</span>
                        <span class="text-body"><?= htmlspecialchars($user['email'] ?: 'Nie zdefiniowano') ?></span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary border-opacity-10" style="border-style: dashed !important;">
                        <span class="text-muted small fw-semibold text-uppercase">Numer telefonu:</span>
                        <span class="text-body"><?= htmlspecialchars($user['phone_number'] ?: 'Nie podano') ?></span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom border-secondary border-opacity-10" style="border-style: dashed !important;">
                        <span class="text-muted small fw-semibold text-uppercase">Typ konta (Uprawnienia):</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1.5 text-uppercase">
                            <?= htmlspecialchars($user['account_type']) ?>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pb-1">
                        <span class="text-muted small fw-semibold text-uppercase">Ostatnie logowanie:</span>
                        <span class="text-body small"><?= $user['last_login'] ? date('d.m.Y H:i:s', strtotime($user['last_login'])) : 'Nigdy' ?></span>
                    </div>
                    
                </div>

                <div class="mt-4 pt-3 border-top text-end">
                    <a href="<?= BASE_URL ?>admin_panel" class="btn btn-outline-secondary">
                        Zamknij podgląd
                    </a>
                </div>

            </div>
        </div>

    </div>
</div>

<?php require_once 'app/Views/layout/header.php'; ?>