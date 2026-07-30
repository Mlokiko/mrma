<?php require_once 'app/Views/layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="view-wrapper-lg">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4 p-lg-5">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom pb-3 mb-4 gap-3">
                    <div>
                        <h2 class="h4 mb-1 text-body fw-bold d-flex align-items-center">
                            <i class="bi bi-journal-text me-2 text-primary"></i> Notatki do RMA #<?= htmlspecialchars($rma['id']) ?>
                        </h2>
                        <p class="text-muted small m-0">
                            Urządzenie: <strong class="text-body"><?= htmlspecialchars(($rma['manufacturer_name'] ?? '') . ' ' . ($rma['model_name'] ?? '')) ?></strong> 
                            <span class="mx-1 opacity-50">|</span> 
                            Klient: <strong class="text-body"><?= htmlspecialchars($rma['client_first_name'] . ' ' . $rma['client_last_name']) ?></strong>
                        </p>
                    </div>
                    <a href="<?= defined('BASE_URL') ? BASE_URL . 'rma/' . $rma['id'] : BASE_URL . 'rma_details&id=' . $rma['id'] ?>" class="btn btn-outline-secondary d-inline-flex align-items-center fw-semibold text-nowrap">
                        <i class="bi bi-arrow-left me-2"></i> Powrót do zlecenia
                    </a>
                </div>

                <div class="row g-4">

                    <div class="col-lg-4">
                        <div class="card bg-body-tertiary border-light-subtle shadow-sm sticky-lg-top" style="top: 1.5rem; z-index: 1;">
                            <div class="card-body p-4">
                                <h3 class="h6 text-muted fw-bold text-uppercase border-bottom pb-2 mb-3" id="formTitle">
                                    <i class="bi bi-pencil-square me-2 text-primary" id="formIcon"></i> Dodaj notatkę
                                </h3>

                                <form method="POST" action="<?= defined('BASE_URL') ? BASE_URL . 'rma_notes/' . $rma['id'] : BASE_URL . 'rma_notes&id=' . $rma['id'] ?>" id="note_form">
                                    <input type="hidden" name="note_id" id="note_id" value="">

                                    <div class="mb-3">
                                        <textarea id="note_text" name="note_text" rows="5" class="form-control" placeholder="Treść notatki..." required></textarea>
                                    </div>

                                    <div class="mb-4 bg-body p-3 rounded border border-secondary-subtle">
                                        <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                            <input class="form-check-input mt-0 fs-5" type="checkbox" role="switch" id="is_internal" name="is_internal" value="1" checked>
                                            <label class="form-check-label small fw-semibold text-body" for="is_internal" title="Odznaczenie sprawi, że notatka będzie publiczna i klient zobaczy ją w przyszłości w swoim panelu sprawdzania statusu." style="cursor: help;">
                                                <i class="bi bi-lock-fill text-warning me-1"></i> Widoczna tylko dla serwisu
                                            </label>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" id="submit_note_btn" class="btn btn-primary w-100 fw-semibold d-inline-flex align-items-center justify-content-center">
                                            <i class="bi bi-save me-2" id="submit_icon"></i> <span>Zapisz</span>
                                        </button>
                                        <button type="button" id="cancel_edit_btn" class="btn btn-secondary w-100 fw-semibold d-inline-flex align-items-center justify-content-center" style="display: none;">
                                            <i class="bi bi-x-lg me-2"></i> Anuluj
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="d-flex flex-column gap-3">
                            
                            <?php if (empty($notes)): ?>
                                <div class="text-center text-muted py-5 border border-secondary border-opacity-25 rounded bg-body-tertiary" style="border-style: dashed !important;">
                                    <i class="bi bi-journal-x fs-1 d-block mb-3 opacity-50"></i>
                                    <span class="fw-medium">Brak jakichkolwiek notatek dla tego zgłoszenia.</span>
                                </div>
                            <?php else: ?>
                                <?php foreach ($notes as $note): ?>
                                    <div class="card shadow-sm <?= $note['is_internal'] ? 'border-warning-subtle' : 'border-light-subtle' ?>">
                                        
                                        <div class="card-header bg-transparent <?= $note['is_internal'] ? 'border-warning-subtle' : 'border-light-subtle' ?> d-flex justify-content-between align-items-center py-2 flex-wrap gap-2">
                                            <div class="small text-muted">
                                                <i class="bi bi-person-circle me-1"></i> <strong class="text-body"><?= htmlspecialchars($note['author_name']) ?></strong> 
                                                <span class="mx-1 opacity-50">&bull;</span> 
                                                <i class="bi bi-calendar3 mx-1"></i> <?= date('d.m.Y H:i', strtotime($note['created_at'])) ?>
                                                
                                                <?php if (!empty($note['updated_at'])): ?>
                                                    <span class="ms-1 fst-italic small" title="<?= date('d.m.Y H:i', strtotime($note['updated_at'])) ?>">(Edytowano)</span>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="d-flex gap-2 align-items-center">
                                                <?php if ($note['user_id'] == $_SESSION['user_id']): ?>
                                                    <button type="button" class="btn btn-sm btn-link text-decoration-none py-0 px-1 text-muted edit-btn d-inline-flex align-items-center" data-id="<?= $note['id'] ?>" data-internal="<?= $note['is_internal'] ?>">
                                                        <i class="bi bi-pencil-square me-1"></i> Edytuj
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <?php if ($note['is_internal']): ?>
                                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">Wewnętrzna</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success-subtle text-success-emphasis border border-success-subtle px-2 py-1">Dla klienta</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="card-body <?= $note['is_internal'] ? 'bg-warning-subtle bg-opacity-10' : '' ?> p-3">
                                            <p class="card-text note-text m-0 text-body" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;"><?= htmlspecialchars($note['note_text']) ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formTitle = document.getElementById('formTitle');
        const formIcon = document.getElementById('formIcon');
        const submitBtn = document.getElementById('submit_note_btn');
        const submitBtnText = submitBtn.querySelector('span');
        const submitBtnIcon = document.getElementById('submit_icon');
        const cancelBtn = document.getElementById('cancel_edit_btn');

        // Obsługa przycisków Edycji
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                // Pobieramy dane z klikniętej notatki
                const noteId = this.getAttribute('data-id');
                const isInternal = this.getAttribute('data-internal') === '1';

                // outerText / innerText wyciąga sam czysty tekst z HTML (zachowując entery)
                const noteText = this.closest('.card').querySelector('.note-text').innerText;

                // Wypełniamy formularz
                document.getElementById('note_id').value = noteId;
                document.getElementById('note_text').value = noteText;
                document.getElementById('is_internal').checked = isInternal;

                // Zmiana UI na tryb edycji
                formTitle.innerHTML = '<i class="bi bi-pencil-fill me-2 text-warning"></i> Edytuj notatkę';
                submitBtnText.textContent = 'Zapisz zmiany';
                submitBtnIcon.className = 'bi bi-check2-circle me-2';
                submitBtn.classList.remove('btn-primary');
                submitBtn.classList.add('btn-warning');

                // Wyświetlamy przycisk Anuluj
                cancelBtn.style.display = 'inline-flex';

                // Skrolowanie do formularza i focus
                window.scrollTo({ top: 0, behavior: 'smooth' });
                document.getElementById('note_text').focus();
            });
        });

        // Obsługa przycisku Anuluj edycję
        cancelBtn.addEventListener('click', function () {
            // Czyszczenie formularza
            document.getElementById('note_id').value = '';
            document.getElementById('note_text').value = '';
            document.getElementById('is_internal').checked = true;

            // Powrót UI do trybu dodawania
            formTitle.innerHTML = '<i class="bi bi-pencil-square me-2 text-primary"></i> Dodaj notatkę';
            submitBtnText.textContent = 'Zapisz';
            submitBtnIcon.className = 'bi bi-save me-2';
            submitBtn.classList.remove('btn-warning');
            submitBtn.classList.add('btn-primary');

            this.style.display = 'none';
        });
    });
</script>

<?php require_once 'app/Views/layout/footer.php'; ?>