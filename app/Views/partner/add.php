<?php require_once 'app/Views/layout/header.php'; ?>

<div class="row justify-content-center">
    <div class="view-wrapper-md">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4 p-lg-5">
                
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom flex-wrap gap-3">
                    <h2 class="h3 mb-0 text-body d-flex align-items-center">
                        <i class="bi bi-building-add me-2 text-primary"></i> Rejestracja nowego partnera B2B
                    </h2>
                    <a href="<?= BASE_URL ?>partner_list" class="btn btn-outline-secondary d-inline-flex align-items-center fw-semibold">
                        <i class="bi bi-list-ul me-2"></i> Wróć do listy
                    </a>
                </div>

                <form method="POST" action="<?= BASE_URL ?>partner_add">
                    
                    <h3 class="h5 mt-4 mb-3 text-muted fw-bold text-uppercase fs-6"><i class="bi bi-building me-2"></i> Dane firmy</h3>
                    <div class="row g-3 mb-4 bg-body-tertiary p-3 rounded border border-secondary-subtle">
                        <div class="col-12">
                            <label for="company_name" class="form-label small fw-semibold text-muted">Pełna nazwa firmy (np. Dostawca Części XYZ Sp. z o.o.)</label>
                            <input type="text" id="company_name" name="company_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="address_location" class="form-label small fw-semibold text-muted">Adres fizyczny / Siedziba</label>
                            <input type="text" id="address_location" name="address_location" class="form-control" placeholder="Ulica, Kod pocztowy, Miasto">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label small fw-semibold text-muted">Ogólny e-mail firmowy</label>
                            <input type="email" id="email" name="email" class="form-control">
                        </div>
                    </div>

                    <h3 class="h5 mt-5 mb-3 text-muted fw-bold text-uppercase fs-6"><i class="bi bi-person-badge me-2"></i> Osoba kontaktowa (Reprezentant)</h3>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="representative_first_name" class="form-label small fw-semibold text-muted">Imię reprezentanta</label>
                            <input type="text" id="representative_first_name" name="representative_first_name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="representative_last_name" class="form-label small fw-semibold text-muted">Nazwisko reprezentanta</label>
                            <input type="text" id="representative_last_name" name="representative_last_name" class="form-control">
                        </div>
                        <div class="col-12" id="phoneContainer">
                            <label for="primary_phone" class="form-label small fw-semibold text-muted">Główny telefon kontaktowy</label>
                            <input type="text" id="primary_phone" name="primary_phone" class="form-control" placeholder="+48..." required>
                            
                            <div id="additionalPhonesList" class="mt-2 d-flex flex-column gap-2"></div>
                            
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2 d-inline-flex align-items-center" id="btnAddPhone">
                                <i class="bi bi-plus-lg me-1"></i> Dodaj kolejny numer
                            </button>
                        </div>
                    </div>

                    <h3 class="h5 mt-5 mb-3 text-muted fw-bold text-uppercase fs-6"><i class="bi bi-info-circle me-2"></i> Dodatkowe informacje</h3>
                    <div class="mb-4">
                        <label for="internal_note" class="form-label small fw-semibold text-warning-emphasis"><i class="bi bi-lock-fill me-1"></i> Notatka wewnętrzna (Tylko dla serwisu)</label>
                        <textarea id="internal_note" name="internal_note" class="form-control border-warning-subtle bg-warning-subtle bg-opacity-10" rows="3" placeholder="np. Szybka wysyłka, ale trudny kontakt mailowy..."></textarea>
                    </div>

                    <div class="mt-5 text-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold shadow-sm w-100 w-sm-auto">
                            <i class="bi bi-save me-2"></i> Zarejestruj partnera
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('btnAddPhone').addEventListener('click', () => {
            const wrapper = document.createElement('div');
            wrapper.className = 'input-group input-group-sm';
            wrapper.innerHTML = `
                <input type="text" name="add_phone_number[]" class="form-control" placeholder="Dodatkowy numer"> 
                <input type="text" name="add_phone_desc[]" class="form-control" placeholder="Opis (np. biuro)"> 
                <button type="button" class="btn btn-outline-danger" onclick="this.parentElement.remove()" title="Usuń numer">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            document.getElementById('additionalPhonesList').appendChild(wrapper);
        });
    });
</script>

<?php require_once 'app/Views/layout/footer.php'; ?>