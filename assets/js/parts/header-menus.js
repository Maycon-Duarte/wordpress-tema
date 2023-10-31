// let ButtonMobile = document.getElementById('menu-mobile-button');
// let MenuPrincipal = document.getElementsByClassName('menu-principal')[0];
// let root = document.documentElement;

// if (ButtonMobile || MenuPrincipal) {
//     const lootieAnimationBurguer = lottie.loadAnimation({
//         container: ButtonMobile,
//         renderer: 'svg',
//         loop: false,
//         autoplay: false,
//         path: THEME_URL + '/assets/lootie-files/menu-mobile.json'
//     });

//     function toggleMobileMenu() {
//         if (ButtonMobile.classList.contains('open')) {
//             lootieAnimationBurguer.playSegments([50, 100], true);
//             ButtonMobile.classList.remove('open'); 
//             ButtonMobile.setAttribute('aria-expanded', 'false');
//             ButtonMobile.setAttribute('aria-label', 'Abrir menu');
//             root.style.overflow = 'auto';
//             MenuPrincipal.classList.remove('open');
//         } else {
//             lootieAnimationBurguer.playSegments([0, 50], true);
//             ButtonMobile.classList.add('open');
//             ButtonMobile.setAttribute('aria-expanded', 'true');
//             ButtonMobile.setAttribute('aria-label', 'Fechar menu');
//             root.style.overflow = 'hidden';
//             MenuPrincipal.classList.add('open');
//         }
//     }

//     ButtonMobile.addEventListener('click', toggleMobileMenu);
// }

// let menuItems = document.querySelectorAll('.menu-principal .menu-item-has-children');

// menuItems.forEach(item => {
//     let after = document.createElement('i');
//     after.classList.add('coc-icons', 'coc-arrow-right');
//     after.addEventListener('click', function (e) {
//         e.preventDefault();
//         let subItem = item.querySelector('.sub-menu');
//         if (item.classList.contains('open')) {
//             item.classList.remove('open');
//             subItem.style.maxHeight = null;
//         } else {
//             item.classList.add('open');
//             subItem.style.maxHeight = subItem.scrollHeight + 'px';
//         }
//     });

//     let link = item.querySelector('a');
//     link.appendChild(after);
// });



