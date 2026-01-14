document.addEventListener('DOMContentLoaded', () => {
const root = document.querySelector('#landing-template');
  if (!root) return;

  const SEL = 'video.landing-media-reverse, video.landing-hero-video, video.landing-media';

  function microSeek(v) {
    try {
      if (!isFinite(v.duration) || v.duration <= 0) return;
      const t = v.currentTime || 0;
      const next = Math.min(t + 0.01, Math.max(v.duration - 0.05, 0));
      v.currentTime = next;
    } catch (_) {}
  }

  async function revive(v) {
    try {
      // Fijar atributos cada vez (por si el DOM se recrea)
      v.muted = true;
      v.loop = true;
      v.autoplay = true;
      v.playsInline = true;
      v.preload = 'auto';

      v.setAttribute('muted', '');
      v.setAttribute('loop', '');
      v.setAttribute('autoplay', '');
      v.setAttribute('playsinline', '');
      v.setAttribute('preload', 'auto');

      // Reenganchar pipeline si está “suspendido”
      if (v.readyState < 2) v.load();

      // Intento normal
      let p = v.play();
      if (p && p.catch) await p.catch(() => {});

      // Si sigue sin moverse, forzamos el “click en la barra” programático
      if (v.paused || v.readyState < 2) {
        microSeek(v);
        p = v.play();
        if (p && p.catch) await p.catch(() => {});
      }
    } catch (_) {}
  }

  function bind(v) {
    if (v.__pslBound) return;
    v.__pslBound = true;

    // Cuando entra en estados típicos de “se me quedó colgado”
    ['pause', 'stalled', 'suspend', 'waiting'].forEach(evt => {
      v.addEventListener(evt, () => {
        if (document.hidden) return;
        requestAnimationFrame(() => revive(v));
        setTimeout(() => revive(v), 200);
        setTimeout(() => revive(v), 800);
      });
    });

    revive(v);
  }

  function bindAll() {
    root.querySelectorAll(SEL).forEach(bind);
  }

  // Si el theme re-renderiza / cambia nodos
  new MutationObserver(bindAll).observe(root, { childList: true, subtree: true });

  // Watchdog: si alguien lo para, lo revivimos
  setInterval(() => {
    if (document.hidden) return;
    root.querySelectorAll(SEL).forEach(v => {
      if (v.paused || v.readyState < 2) revive(v);
    });
  }, 1000);

  // Reintento al volver a foco
  window.addEventListener('focus', bindAll);
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') bindAll();
  });

  bindAll();
});


