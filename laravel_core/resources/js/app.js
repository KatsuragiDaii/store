// ============================================================================
// Scroll-reveal: elemen dengan atribut [data-animate] otomatis "muncul"
// (fade/slide, tergantung nilai atributnya -- lihat app.css) begitu masuk
// area pandang saat halaman di-scroll. Jalan otomatis di SEMUA halaman
// karena file ini di-load lewat @vite di tiap page, gak perlu setup ulang
// per halaman.
// ============================================================================
document.addEventListener('DOMContentLoaded', () => {
    const animatedEls = document.querySelectorAll('[data-animate]');
    if (!animatedEls.length) return;

    // Browser lama yang gak dukung IntersectionObserver: langsung tampilkan
    // semua tanpa animasi, jangan sampai konten kesembunyi selamanya.
    if (!('IntersectionObserver' in window)) {
        animatedEls.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            const delay = Number(entry.target.dataset.animateDelay || 0);
            setTimeout(() => entry.target.classList.add('is-visible'), delay);
            observer.unobserve(entry.target);
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -40px 0px',
    });

    animatedEls.forEach((el) => observer.observe(el));
});
