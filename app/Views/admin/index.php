<?php require_once 'app/Views/layout/header.php'; ?>

<div class="view-wrapper-md card shadow-sm">
    <div class="card-body p-4">
        <h2 class="h4 pb-3 mb-4 border-bottom text-body">
            Panel Administratora
        </h2>

        <div class="mb-4">
            <h3 class="h6 text-muted mb-3 text-uppercase fw-bold border-0">
                Zarządzanie systemem
            </h3>
            
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= BASE_URL ?>admin_users_list" class="btn btn-outline-secondary d-inline-flex align-items-center">
                    <i class="bi bi-people me-2"></i> Lista użytkowników systemu
                </a>
                
                <a href="<?= BASE_URL ?>register" class="btn btn-primary d-inline-flex align-items-center">
                    <i class="bi bi-person-plus me-2"></i> Dodaj nowego użytkownika
                </a>

                <a href="<?= BASE_URL ?>admin_localizations" class="btn btn-outline-secondary d-inline-flex align-items-center">
                    <i class="bi bi-geo-alt me-2"></i> Zarządzanie lokalizacjami
                </a>
            </div>
        </div>

        <div class="card bg-body-tertiary border-secondary border-opacity-25" style="border-style: dashed;">
            <div class="card-body">
                <h4 class="h5 card-title text-muted mb-2 d-flex align-items-center">
                    <i class="bi bi-gear-fill me-2"></i> Globalne ustawienia aplikacji 
                    <span class="badge bg-secondary ms-2 fs-7 fw-normal">W budowie</span>
                </h4>
                
                <p class="text-muted small mb-4 line-height-base">
                    W tym miejscu pojawią się globalne parametry konfiguracyjne dla całego systemu RMA (np. nazwa firmy, format generowania numerów zgłoszeń, polityka załączników, czy domyślne statusy urządzeń).
                </p>

                <div class="opacity-50" style="pointer-events: none;">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Nazwa instancji RMA</label>
                            <input type="text" class="form-control" value="Główny Serwis Serwisowy" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-muted">Format numeru RMA</label>
                            <select class="form-select" disabled>
                                <option>RMA/{YEAR}/{ID}</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button class="btn btn-primary" disabled>Zapisz ustawienia globalne</button>
                    </div>
                </div>

            </div>
        </div>
        
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>