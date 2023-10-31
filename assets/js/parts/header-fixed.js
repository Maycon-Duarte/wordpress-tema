// function padding_for_header_fixed() {
//     var header = jQuery('header');
//     var body = jQuery('body');
//     var wpadminbar = jQuery('#wpadminbar');
//     var menuPrincipal = jQuery('.menu-principal');

//     if (header.length) {
//         var headerHeight = header.outerHeight();
//         body.css('padding-top', headerHeight);

//         if (window.innerWidth < 1025) {
//             menuPrincipal.css('top', headerHeight);
//             var altura = window.innerHeight - headerHeight;
//             //var paddings = parseInt(menuPrincipal.css('padding-top')) + parseInt(menuPrincipal.css('padding-bottom'));
//             //altura = altura - paddings;
//             menuPrincipal.css('height', altura);
//         } else {
//             menuPrincipal.css('top', 'auto');
//             root.style.overflow = 'auto';
//             menuPrincipal.css('height', 'auto');
//         }
//     }

//     if (wpadminbar.length) {
//         var wpadminbarHeight = wpadminbar.outerHeight();
//         header.css('top', wpadminbarHeight);
//     }
// }

// jQuery(function () {
//     padding_for_header_fixed();
//     jQuery(window).on('resize', function () {
//         padding_for_header_fixed();
//         padding_for_header_fixed();
//     });

//     jQuery(window).on('scroll', function () {
//         padding_for_header_fixed();
//         padding_for_header_fixed();
//     });
// });

// jQuery(function ($) {
//     // Add space for Elementor Menu Anchor link
//     if (window.elementorFrontend) {
//         elementorFrontend.hooks.addFilter('frontend/handlers/menu_anchor/scroll_top_distance', function (scrollTop) {
//             return scrollTop - 185;
//         });
//     }
// });