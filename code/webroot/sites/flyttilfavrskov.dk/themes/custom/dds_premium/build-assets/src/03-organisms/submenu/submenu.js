import animateScrollTo from 'animated-scroll-to';
import debounce from 'lodash/debounce';

document.addEventListener('DOMContentLoaded', () => {
  const submenus = document.querySelectorAll('.js-submenu');
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

  function reachedEndOfScroll(element) {
    const { scrollLeft } = element;
    const width = element.clientWidth;
    const { scrollWidth } = element;
    return scrollWidth - scrollLeft === width;
  }

  function handleShadow(submenus, scroll, resize) {
    for (let i = 0; i < submenus.length; i += 1) {
      const submenu = submenus[i];
      if (scroll) {
        toggleShadow(submenu);
        submenu.addEventListener('scroll', () => {
          toggleShadow(submenu);
        }, false);
      } else if (resize) {
        toggleShadow(submenu);
      }
    }
  }

  function toggleShadow(submenu) {
    const shadowRight = submenu.parentNode.querySelector('.js-submenu-shadow-right');
    const shadowLeft = submenu.parentNode.querySelector('.js-submenu-shadow-left');
    shadowRight.style.transform = `translateX(${submenu.scrollLeft}px)`;
    shadowLeft.style.transform = `translateX(${submenu.scrollLeft}px)`;
    if (reachedEndOfScroll(submenu)) {
      shadowRight.classList.remove('active');
    } else {
      shadowRight.classList.add('active');
    }
    if (submenu.scrollLeft > 0) {
      shadowLeft.classList.add('active');
    } else {
      shadowLeft.classList.remove('active');
    }
  }

  handleShadow(submenus, true, false);
  window.addEventListener('resize', debounce(() => {
    handleShadow(submenus, false, true);
  }, 100), {passive: true});

  for (let i = 0; i < links.length; i += 1) {
    const link = links[i];
    link.addEventListener('click', (e) => {
      const hash = link.href.split('#')[1];
      const element = document.getElementById(hash);
      if (hash.length > 0 && element) {
        e.preventDefault();
        const topPosition = (element.getBoundingClientRect().top + window.pageYOffset) - getHeaderHeight();
        animateScrollTo(topPosition, {
          speed: 200,
        });
      }
    });
  }
});
