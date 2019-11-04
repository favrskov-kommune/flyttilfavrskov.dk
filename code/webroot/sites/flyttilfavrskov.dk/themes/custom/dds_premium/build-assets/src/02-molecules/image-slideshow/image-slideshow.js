import Flickity from 'flickity';
import FlickityFade from 'flickity-fade';
import FlickityAsNavFor from 'flickity-as-nav-for';

document.addEventListener('DOMContentLoaded', () => {
  const imageSlideShowsWrappers = document.querySelectorAll('.js-image-slideshow-wrapper');

  if (imageSlideShowsWrappers && imageSlideShowsWrappers.length > 0) {
    for (let i = 0; i < imageSlideShowsWrappers.length; i += 1) {
      const current = imageSlideShowsWrappers[i];
      const slideshow = current.querySelector('.js-image-slideshow');
      const navigation = current.querySelector('.js-image-slideshow-navigation');

      setTimeout(() => {
        const flktySlideshow = new Flickity(slideshow, {
          // options
          cellAlign: 'left',
          contain: true,
          pageDots: false,
          prevNextButtons: false,
          fade: true,
          wrapAround: true,
          autoPlay: current.dataset.autoplay === 'true' ? 3000 : '',
          cellSelector: '.image-slideshow-slide',
        });

        const flktyNavigation = new Flickity(navigation, {
          // options
          contain: true,
          pageDots: false,
          prevNextButtons: false,
          asNavFor: slideshow,
        });
      });
    }
  }
});
