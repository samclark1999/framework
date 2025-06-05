document.addEventListener('DOMContentLoaded', (e) => {
// after swiper is initialized remove display:none from banner
    addEventListener('swiper-initialized', () => {
        const banner = document.querySelector('.banner');
        if (banner) {
            banner.style.display = 'block';
        }
    });
});