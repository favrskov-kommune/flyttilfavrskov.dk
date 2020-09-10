import animateScrollTo from 'animated-scroll-to';

document.addEventListener('DOMContentLoaded', () => {
  const links = document.querySelectorAll('.js-submenu a[href*="#"]');
  if (links.length === 0) {
    return;
  }

  const getHeaderHeight = () => {
    const header = document.querySelector('.js-header');
    if (!header || document.body.clientWidth < 1200) {
      return 0;
    }
    return header.getBoundingClientRect().height;
  };

  for (let i = 0; i < links.length; i += 1) {
    const link = links[i];
    link.addEventListener('click', (e) => {
      const hash = link.href.split('#')[1];
      const element = document.getElementById(hash);
      if (hash.length > 0 && element) {
        e.preventDefault();
        const topPosition = element.getBoundingClientRect().top - getHeaderHeight();
        animateScrollTo(topPosition, {
          speed: 200,
        });
      }
    });
  }
});
