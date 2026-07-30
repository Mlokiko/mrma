<?php require_once 'app/Views/layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="col-12">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4 p-lg-5">

                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom flex-wrap gap-3">
                    <h2 class="h3 mb-0 text-body d-flex align-items-center">
                        <i class="bi bi-briefcase-fill me-2 text-primary"></i> Partnerzy B2B
                    </h2>
                    <a href="<?= BASE_URL ?>partner_add"
                        class="btn btn-primary d-inline-flex align-items-center fw-semibold">
                        <i class="bi bi-plus-lg me-2"></i> Dodaj partnera
                    </a>
                </div>

                <div class="table-responsive border rounded shadow-sm bg-body">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="text-muted small text-uppercase align-middle user-select-none">
                            <tr>
                                <th class="ps-3 text-nowrap">Nazwa firmy</th>
                                <th class="text-nowrap">Reprezentant</th>
                                <th class="text-nowrap">Główny telefon</th>
                                <th class="text-nowrap">Adres E-mail</th>
                                <th class="text-nowrap">Lokalizacja / Siedziba</th>
                                <th class="text-end pe-3 text-nowrap">Akcje</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            <?php if (empty($partners)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="bi bi-briefcase fs-1 d-block mb-3 opacity-50"></i>
                                        Brak partnerów w bazie.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($partners as $p): ?>
                                    <tr>
                                        <td class="px-3">
                                            <a href="<?= BASE_URL ?>partner/<?= $p['id'] ?>"
                                                class="text-decoration-none text-primary fw-bold d-block">
                                                <?= htmlspecialchars($p['company_name']) ?>
                                            </a>
                                        </td>
                                        <td class="text-body small">
                                            <?= htmlspecialchars($p['representative_first_name'] . ' ' . $p['representative_last_name']) ?>
                                        </td>
                                        <td class="text-body fw-medium">
                                            <?= htmlspecialchars($p['primary_phone']) ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($p['email'])): ?>
                                                <a href="mailto:<?= htmlspecialchars($p['email']) ?>"
                                                    class="text-decoration-none d-inline-flex align-items-center small">
                                                    <?= htmlspecialchars($p['email']) ?> <i class="bi bi-envelope ms-1"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">---</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-muted small">
                                            <?= htmlspecialchars($p['address_location'] ?: '---') ?>
                                        </td>
                                        <td class="text-end px-3">
                                            <a href="<?= BASE_URL ?>partner/<?= $p['id'] ?>"
                                                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center">
                                                <i class="bi bi-person-lines-fill me-1"></i> Otwórz
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once 'app/Views/layout/footer.php'; ?>