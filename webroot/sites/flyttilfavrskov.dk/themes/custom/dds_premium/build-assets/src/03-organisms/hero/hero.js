document.addEventListener('DOMContentLoaded', () => {
  const heros = document.querySelectorAll('.js-hero');

  if (heros && heros.length > 0) {
    for (let i = 0; i < heros.length; i += 1) {
      const currentHero = heros[i];
      const iframeWrapper = currentHero.querySelector('.js-hero-iframe-wrapper');
      const videoData = currentHero.dataset.video;

      if (videoData) {
        const oembed = JSON.parse(videoData).oEmbed;

        if (oembed !== 'null' && oembed !== null) {
          if (oembed.html.indexOf('iframe') > -1) {
            const regex = /<iframe.*?src="(.*?)"/;
            const oldSrc = regex.exec(oembed.html)[1];
            const newSrc = `${oldSrc}&autoplay=1&mute=1&controls=0&showinfo=0&autohide=1&background=1`; // We need to set this ourselves, otherwise we are not sure it is gonna play.
            oembed.html = oembed.html.replace(oldSrc, newSrc);
          } else if (oembed.html.indexOf('video') > -1) {
            oembed.html = oembed.html.replace('<video', '<video autoplay loop muted');
          }
          iframeWrapper.innerHTML = oembed.html;
        }
      }
    }
  }
});
