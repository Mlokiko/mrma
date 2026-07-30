</div> </div> <footer class="bg-body-tertiary text-muted text-center py-3 border-top mt-auto w-100">
        <div class="container">
            <small>&copy; <?= date('Y') ?> mRMA System.</small>
        </div>
    </footer>

    <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="settingsModalLabel"><i class="bi bi-sliders me-2"></i>Personalizacja</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Zamknij"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Motyw systemu</label>
                        <select class="form-select" id="themeSelect">
                            <option value="light">☀️ Jasny</option>
                            <option value="dark">🌙 Ciemny</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Szerokość strony</label>
                        <select class="form-select" id="pageWidthSelect">
                            <option value="standard">Standardowa (1320px)</option>
                            <option value="wide">Szeroka (1600px) - Zalecana</option>
                            <option value="ultrawide">Bardzo szeroka (1920px)</option>
                            <option value="fluid">Pełna szerokość (100%)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Układ menu (PC)</label>
                        <select class="form-select" id="navOrientationSelect">
                            <option value="horizontal">Poziome (Na górze)</option>
                            <option value="vertical">Pionowe (Z lewej)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeSelect = document.getElementById('themeSelect');
            const navOrientationSelect = document.getElementById('navOrientationSelect');
            const pageWidthSelect = document.getElementById('pageWidthSelect');

            // 1. Ładowanie aktualnych wartości do selectów
            if (themeSelect) themeSelect.value = document.documentElement.getAttribute('data-bs-theme') || 'light';
            if (navOrientationSelect) navOrientationSelect.value = document.documentElement.getAttribute('data-nav-orientation') || 'horizontal';
            if (pageWidthSelect) pageWidthSelect.value = document.documentElement.getAttribute('data-page-width') || 'wide';

            // 2. Obsługa zmiany motywu
            if (themeSelect) {
                themeSelect.addEventListener('change', function () {
                    const val = this.value;
                    document.documentElement.setAttribute('data-bs-theme', val);
                    localStorage.setItem('theme', val);
                });
            }

            // 3. Obsługa zmiany układu paska
            if (navOrientationSelect) {
                navOrientationSelect.addEventListener('change', function () {
                    const val = this.value;
                    document.documentElement.setAttribute('data-nav-orientation', val);
                    localStorage.setItem('navOrientation', val);
                });
            }

            // 4. Obsługa zmiany szerokości strony
            if (pageWidthSelect) {
                pageWidthSelect.addEventListener('change', function () {
                    const val = this.value;
                    document.documentElement.setAttribute('data-page-width', val);
                    localStorage.setItem('pageWidth', val);
                });
            }
        });
    </script>
</body>
</html>