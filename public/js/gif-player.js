(function () {
  window.copyGifLink = function (url, btn) {
    var fullUrl = url.indexOf('http') === 0 ? url : window.location.origin + url;
    navigator.clipboard.writeText(fullUrl).then(function () {
      var old = btn.innerHTML;
      btn.innerHTML = '<i class="fa-solid fa-check"></i>';
      setTimeout(function () {
        btn.innerHTML = old;
      }, 1500);
    });
  };

  window.likeGif = function (slug, btn) {
    fetch('/Gifs/' + slug + '/like', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': window.Gif_CSRF_TOKEN || '' },
    }).then(r => r.json()).then(data => {
      if (data.success) {
        var countEl = btn.querySelector('.like-count');
        if (countEl) countEl.textContent = new Intl.NumberFormat().format(data.like_count);
        btn.classList.add('text-red-500', 'bg-red-50', 'dark:bg-red-500/10');
        var icon = btn.querySelector('.fa-heart');
        if (icon) {
          icon.classList.remove('fa-regular');
          icon.classList.add('fa-solid');
        }
      }
    }).catch(console.error);
  };

  window.shareGif = function (slug) {
    fetch('/Gifs/' + slug + '/share', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': window.Gif_CSRF_TOKEN || '' },
    }).then(r => r.json()).then(data => {
      if (data.success) {
        var countEls = document.querySelectorAll('.share-count');
        countEls.forEach(el => el.textContent = new Intl.NumberFormat().format(data.share_count));
      }
    }).catch(console.error);
  };

  // GIF không có khái niệm "nghe đủ X giây" như âm thanh (hiện ngay khi tải trang) nên tính
  // 1 lượt xem mỗi lần vào trang chi tiết là hợp lý — chỉ gắn ở trang show (#gif-view-root),
  // không tính khi GIF chỉ xuất hiện trong lưới danh sách.
  var viewRoot = document.getElementById('gif-view-root');
  if (viewRoot && viewRoot.dataset.slug) {
    fetch('/Gifs/' + viewRoot.dataset.slug + '/play', {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': window.Gif_CSRF_TOKEN || '' },
    }).catch(function () {});
  }
})();
