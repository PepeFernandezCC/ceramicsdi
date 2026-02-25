document.addEventListener('DOMContentLoaded', function () {

  var btn = document.getElementById('ccpr_submit');
  if (!btn || typeof ccpr === 'undefined') return;

  btn.addEventListener('click', function () {

    var fd = new FormData();
    fd.append('id_product', ccpr.id_product);
    fd.append('rating', document.querySelector('[name=ccpr_rating]').value);
    fd.append('comment', document.querySelector('[name=ccpr_comment]').value);
    fd.append('token', ccpr.token);

    var input = document.querySelector('[name=ccpr_photos]');
    var files = input ? input.files : null;

    if (files && files.length > ccpr.max_files) {
      showMessage('Máximo ' + ccpr.max_files + ' fotos.');
      return;
    }

    if (files) {
      for (var i = 0; i < files.length; i++) {
        fd.append('photos[]', files[i]);
      }
    }

    fetch(ccpr.submit_url, {
      method: 'POST',
      body: fd
    })
    .then(function (r) { return r.json(); })
    .then(function (json) {
      showMessage(json.ok ? json.message : json.error);
    })
    .catch(function () {
      showMessage('Error enviando reseña.');
    });

  });

  function showMessage(msg) {
    var box = document.getElementById('ccpr_msg');
    if (box) box.innerText = msg;
  }

});