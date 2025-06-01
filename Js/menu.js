const navMenu = document.getElementById('nav_menu');
const navRight = document.getElementById('nav__section--right');
const tmp = document.getElementById('tmp');

// Ẩn nếu cả 2 đang hiển thị
if (window.getComputedStyle(navMenu).display !== 'none' &&
    window.getComputedStyle(navRight).display !== 'none') {
    navMenu.style.display = 'none';
    navRight.style.display = 'none';
}

tmp.addEventListener('click', function() {
    const navMenuDisplay = window.getComputedStyle(navMenu).display;
    const navRightDisplay = window.getComputedStyle(navRight).display;

    if (navMenuDisplay === 'none' && navRightDisplay === 'none') {
        navMenu.style.display = 'flex';
        navRight.style.display = 'flex';
    } else {
        navMenu.style.display = 'none';
        navRight.style.display = 'none';
    }
});
