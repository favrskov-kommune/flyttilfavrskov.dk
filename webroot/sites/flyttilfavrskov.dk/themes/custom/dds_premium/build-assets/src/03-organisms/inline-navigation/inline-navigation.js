import Flickity from 'flickity';

document.addEventListener('DOMContentLoaded', () => {
  const inlineNavigations = document.querySelectorAll('.js-inline-navigation-slider');

  if (inlineNavigations && inlineNavigations.length > 0) {
    for (let i = 0; i < inlineNavigations.length; i += 1) {
      const current = inlineNavigations[i];

      if (window.innerWidth < 768) {
        setTimeout(() => {
          const flkty = new Flickity(current, {
            cellAlign: 'left',
            contain: true,
            cellSelector: '.inline-navigation-item',
            prevNextButtons: false,
            pageDots: false,
          });
        });
      }
    }
  }
});
