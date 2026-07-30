<?php require_once 'app/Views/layout/header.php'; ?>

<div class="view-wrapper-md card shadow-sm">
    <div class="card-body p-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <a href="<?= BASE_URL ?>admin_panel" class="text-muted small text-decoration-none d-inline-flex align-items-center mb-1">
                    <i class="bi bi-arrow-left me-1"></i> Powrót do Panelu
                </a>
                <h2 class="h4 mb-0 text-body">Lista użytkowników</h2>
            </div>
            <a href="<?= BASE_URL ?>register" class="btn btn-primary d-inline-flex align-items-center">
                <i class="bi bi-person-plus me-2"></i> Dodaj użytkownika
            </a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
<thead class="text-muted small text-uppercase align-middle user-select-none">
    <tr>
        <th scope="col" class="ps-3 text-nowrap" style="width: 10%;">ID</th>
        <th scope="col" class="text-nowrap" style="width: 35%;">Nazwa użytkownika</th>
        <th scope="col" class="text-nowrap" style="width: 35%;">Imię i Nazwisko</th>
        <th scope="col" class="text-end pe-3 text-nowrap" style="width: 20%;">Akcje</th>
    </tr>
</thead>
                <tbody class="table-group-divider">
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="text-muted fw-bold">#<?= htmlspecialchars($u['id']) ?></td>
                                <td>
                                    <span class="text-body fw-semibold"><?= htmlspecialchars($u['username']) ?></span>
                                </td>
                                <td class="text-body"><?= htmlspecialchars($u['first_name'] . ' ' . $u['last_name']) ?></td>
                                <td class="text-end text-nowrap">
                                    <a href="<?= BASE_URL ?>admin_user_details?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-secondary me-1 d-inline-flex align-items-center">
                                        <i class="bi bi-eye me-1"></i> Podgląd
                                    </a>
                                    
                                    <?php if ((int)$u['id'] !== (int)$_SESSION['user_id']): ?>
                                        <a href="<?= BASE_URL ?>admin_user_delete?id=<?= $u['id'] ?>" 
                                           class="btn btn-sm btn-danger d-inline-flex align-items-center" 
                                           onclick="return confirm('Czy na pewno chcesz permanentnie usunąć tego użytkownika z systemu?');">
                                            <i class="bi bi-trash me-1"></i> Usuń
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1.5 small fst-italic">
                                            Twoje konto
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="bi bi-info-circle fs-4 d-block mb-2"></i> Brak zarejestrowanych użytkowników.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>