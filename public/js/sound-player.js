(function () {
  var audio = document.getElementById('global-player');
  if (!audio) return;

  var activeCard = null;
  var playRecorded = {};

  function formatTime(s) {
    s = Math.floor(s || 0);
    var m = Math.floor(s / 60);
    var sec = s % 60;
    return m + ':' + (sec < 10 ? '0' : '') + sec;
  }

  function setIcon(card, playing) {
    var icon = card.querySelector('.play-icon');
    if (!icon) return;
    icon.classList.toggle('fa-play', !playing);
    icon.classList.toggle('fa-pause', playing);
  }

  function resetCard(card) {
    if (!card) return;
    setIcon(card, false);
    card.classList.remove('playing');
    var bar = card.querySelector('.progress-fill');
    if (bar) bar.style.width = '0%';
    var drool = card.querySelector('.drool-fill');
    if (drool) drool.style.height = '0%';
    var ring = card.querySelector('.progress-ring');
    if (ring) ring.style.strokeDashoffset = '264';
    var timeEl = card.querySelector('.time-current');
    if (timeEl) timeEl.textContent = '0:00';
  }

  document.querySelectorAll('.sound-card, .sound-button-wrapper').forEach(function (card) {
    var btn = card.querySelector('.play-btn');
    var progressWrap = card.querySelector('.progress-wrap');

    if (btn) {
      btn.addEventListener('click', function () {
        var url = card.dataset.playUrl;

        if (activeCard === card && !audio.paused) {
          audio.pause();
          setIcon(card, false);
          return;
        }

        if (activeCard && activeCard !== card) {
          resetCard(activeCard);
        }

        if (activeCard !== card) {
          audio.src = url;
          activeCard = card;
        }

        audio.play().catch(function () {});
        setIcon(card, true);
        card.classList.add('playing');
      });
    }

    if (progressWrap) {
      progressWrap.addEventListener('click', function (e) {
        if (activeCard !== card || !audio.duration) return;
        var rect = progressWrap.getBoundingClientRect();
        var pct = Math.min(1, Math.max(0, (e.clientX - rect.left) / rect.width));
        audio.currentTime = pct * audio.duration;
      });
    }
  });

  audio.addEventListener('timeupdate', function () {
    if (!activeCard || !audio.duration) return;

    var pct = (audio.currentTime / audio.duration);
    
    var bar = activeCard.querySelector('.progress-fill');
    if (bar) bar.style.width = (pct * 100) + '%';

    var drool = activeCard.querySelector('.drool-fill');
    if (drool) drool.style.height = (pct * 100) + '%';

    var ring = activeCard.querySelector('.progress-ring');
    if (ring) ring.style.strokeDashoffset = 264 - (pct * 264);

    var timeEl = activeCard.querySelector('.time-current');
    if (timeEl) timeEl.textContent = formatTime(audio.currentTime);

    // Chỉ tăng lượt nghe 1 lần/sound sau khi nghe đủ 5 giây hoặc 15% thời lượng (tuỳ cái nào
    // nhỏ hơn) — không tính khi vừa bấm play, không tính lặp lại khi tua qua tua lại.
    var slug = activeCard.dataset.slug;
    var threshold = Math.min(5, audio.duration * 0.15);
    if (slug && !playRecorded[slug] && audio.currentTime >= threshold) {
      playRecorded[slug] = true;
      fetch('/sounds/' + slug + '/play', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': window.SOUND_CSRF_TOKEN || '' },
      }).catch(function () {});
    }
  });

  audio.addEventListener('ended', function () {
    resetCard(activeCard);
  });

  window.copySoundLink = function (url, btn) {
    var fullUrl = url.indexOf('http') === 0 ? url : window.location.origin + url;
    navigator.clipboard.writeText(fullUrl).then(function () {
      var old = btn.innerHTML;
      btn.innerHTML = '<i class="fa-solid fa-check"></i>';
      setTimeout(function () {
        btn.innerHTML = old;
      }, 1500);
    });
  };

  window.likeSound = function (slug, btn) {
    fetch('/sounds/' + slug + '/like', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': window.SOUND_CSRF_TOKEN || '' },
    }).then(r => r.json()).then(data => {
      if(data.success) {
        var countEl = btn.querySelector('.like-count');
        if(countEl) countEl.textContent = new Intl.NumberFormat().format(data.like_count);
        btn.classList.add('text-red-500', 'bg-red-50', 'dark:bg-red-500/10');
        var icon = btn.querySelector('.fa-heart');
        if(icon) {
            icon.classList.remove('fa-regular');
            icon.classList.add('fa-solid');
        }
      }
    }).catch(console.error);
  };

  window.shareSound = function (slug) {
    fetch('/sounds/' + slug + '/share', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': window.SOUND_CSRF_TOKEN || '' },
    }).then(r => r.json()).then(data => {
      if(data.success) {
        var countEls = document.querySelectorAll('.share-count');
        countEls.forEach(el => el.textContent = new Intl.NumberFormat().format(data.share_count));
      }
    }).catch(console.error);
  };
})();
