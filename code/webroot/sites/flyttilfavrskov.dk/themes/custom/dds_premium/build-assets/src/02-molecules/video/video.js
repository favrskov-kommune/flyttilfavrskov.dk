document.addEventListener('DOMContentLoaded', () => {
  const videos = document.querySelectorAll('.js-video');

  if (videos && videos.length > 0) {
    for (let i = 0; i < videos.length; i += 1) {
      const currentVideo = videos[i];
      const playIcon = currentVideo.querySelector('.js-video-play-icon');
      const iframeWrapper = currentVideo.querySelector('.js-video-iframe-wrapper');
      const videoData = currentVideo.dataset.video;
      const isAutoplay = currentVideo.dataset.autoplay;
      let isPlaying = false;

      if (isAutoplay === 'true') {
        document.addEventListener('scroll', () => {
          const currentVideoRect = currentVideo.getBoundingClientRect();
          const bodyRect = document.body.getBoundingClientRect();
          const videoTop = currentVideoRect.top;
          const vLeft = currentVideoRect.left;
          const vBottom = currentVideoRect.bottom;
          const videoRight = currentVideoRect.right;
          const bodyWidth = bodyRect.clientWidth;
          const bodyHeight = bodyRect.clientHeight;
          const winHeight = window.innerHeight;
          const winWidth = window.innerWidth;
          const visibleX = videoTop >= 0 && vLeft >= 0 && vBottom <= (winHeight || bodyHeight);
          const visibleY = videoRight <= (winWidth || bodyWidth);
          const isVisible = visibleX && visibleY;

          if (isVisible && !isPlaying) {
            isPlaying = true;
            iframeWrapper.innerHTML = JSON.parse(videoData).oEmbed.html;
          }
        });
      } else {
        playIcon.addEventListener('click', (e) => {
          const oembed = JSON.parse(videoData).oEmbed;
          if (oembed !== 'null' && oembed !== null) {
            if (oembed.html.indexOf('iframe') > -1) {
              oembed.html = oembed.html.replace('mute=1', 'mute=0');
              oembed.html = oembed.html.replace('controls=0', 'controls=1');
              oembed.html = oembed.html.replace('showinfo=0', 'showinfo=1');
              const regex = /<iframe.*?src="(.*?)"/;
              const oldSrc = regex.exec(oembed.html)[1];
              const newSrc = `${oldSrc}&autoplay=1&showinfo=0&autohide=1&mute=1`; // We need to set this ourselves, otherwise we are not sure it is gonna play.
              oembed.html = oembed.html.replace(oldSrc, newSrc);
            } else if (oembed.html.indexOf('video') > -1) {
              oembed.html = oembed.html.replace('<video', '<video autoplay muted');
            }
            e.currentTarget.parentNode.classList.add('video--hide-content');
            iframeWrapper.innerHTML = oembed.html;
          }
        });
      }
    }
  }
});
