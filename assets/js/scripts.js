document.addEventListener('DOMContentLoaded', function () {

    // ── Konfirmasi hapus ──
    document.querySelectorAll('.confirm-delete').forEach(function(button){
        button.addEventListener('click', function(event){
            if (!confirm('Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.')) {
                event.preventDefault();
            }
        });
    });

    // ── Fallback gambar poster ──
    document.querySelectorAll('img[data-fallback]').forEach(function(img){
        img.onerror = function(){
            this.style.display = 'none';
            var fallback = document.getElementById(this.dataset.fallback);
            if (fallback) fallback.style.display = 'flex';
        };
    });

    // ── Auto-dismiss flash alert setelah 4 detik ──
    var flashContainer = document.querySelector('.flash-container');
    if (flashContainer) {
        setTimeout(function(){
            flashContainer.style.transition = 'opacity 0.5s ease';
            flashContainer.style.opacity = '0';
            setTimeout(function(){ flashContainer.remove(); }, 500);
        }, 4000);
    }

    // ── Highlight active sidebar link ──
    var currentPath = window.location.pathname;
    document.querySelectorAll('.admin-sidebar .nav-link').forEach(function(link){
        if (link.getAttribute('href') && currentPath.endsWith(link.getAttribute('href').split('/').pop())) {
            link.classList.add('active');
        }
    });

    // ── Preview gambar sebelum upload ──
    document.querySelectorAll('input[type="file"][data-preview]').forEach(function(input){
        input.addEventListener('change', function(){
            var previewId = this.dataset.preview;
            var preview = document.getElementById(previewId);
            if (preview && this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e){ preview.src = e.target.result; preview.style.display = 'block'; };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
});

// ── Toggle show/hide password ──
function togglePassword(inputId, btn) {
    var input = document.getElementById(inputId);
    if (!input) return;
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '<i class="bi bi-eye-slash"></i>';
    } else {
        input.type = 'password';
        btn.innerHTML = '<i class="bi bi-eye"></i>';
    }
}
