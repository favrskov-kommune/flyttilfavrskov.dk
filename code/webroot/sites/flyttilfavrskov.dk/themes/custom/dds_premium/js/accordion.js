document.addEventListener('DOMContentLoaded', function() {
  const accordions = document.querySelectorAll('.js-accordion');
  if (accordions.length === 0) {
    return;
  }

  for (let i = 0; i < accordions.length; i += 1) {
    const accordion = accordions[i];
    const items = accordion.querySelectorAll('.js-accordion-item');
    for (let x = 0; x < items.length; x += 1) {
      const element = items[x];
      element.addEventListener('click', () => {
        if (element.classList.contains('active')) {
          element.setAttribute('aria-expanded', 'false');
          element.classList.remove('active');
          element.nextElementSibling.classList.remove('active');
          element.nextElementSibling.setAttribute('aria-hidden', 'true');
        } else {
          element.setAttribute('aria-expanded', 'true');
          element.classList.add('active');
          element.nextElementSibling.classList.add('active');
          element.nextElementSibling.setAttribute('aria-hidden', 'false');
        }
      });
    }

    const button = accordion.querySelector('.js-display-all-items');
    if (button) {
      button.addEventListener('click', () => {
        button.style.display = 'none';
        const hiddenItems = accordion.querySelectorAll('.js-accordion-item.hidden');
        for (let x = 0; x < hiddenItems.length; x += 1) {
          hiddenItems[x].classList.remove('hidden');
        }
      });
    }
  }
});
