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
          let newIframe = JSON.parse(videoData).oEmbed.html;
          newIframe = newIframe.replace('mute=1', 'mute=0');
          newIframe = newIframe.replace('controls=0', 'controls=1');
          newIframe = newIframe.replace('showinfo=0', 'showinfo=1');
          const regex = /<iframe.*?src="(.*?)"/;

          const oldSrc = regex.exec(newIframe)[1];

          const newSrc = `${oldSrc}&autoplay=1&showinfo=0&autohide=1`; // We need to set this ourselves, otherwise we are not sure it is gonna play.

          newIframe = newIframe.replace(oldSrc, newSrc);

          e.currentTarget.parentNode.classList.add('video--hide-content');

          iframeWrapper.innerHTML = newIframe;
        });
      }
    }
  }
});
