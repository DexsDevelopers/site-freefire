(function () {
  const ICONS = {
    success: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>',
    error: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>',
    warning: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>',
    info: '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>',
  };

  function ensureToastWrap() {
    let wrap = document.querySelector('.tp-toast-wrap');
    if (wrap) return wrap;
    wrap = document.createElement('div');
    wrap.className = 'tp-toast-wrap';
    document.body.appendChild(wrap);
    return wrap;
  }

  function normalizeType(type) {
    const t = String(type || '').toLowerCase();
    if (t === 'success' || t === 'error' || t === 'warning' || t === 'info') return t;
    return 'info';
  }

  function toast(type, message, opts) {
    const t = normalizeType(type);
    const o = opts && typeof opts === 'object' ? opts : {};
    const title = String(o.title || (t === 'success' ? 'Sucesso' : t === 'error' ? 'Erro' : t === 'warning' ? 'Atenção' : 'Aviso'));
    const msg = String(message || '').trim();
    if (!msg) return null;

    const duration = Number.isFinite(o.duration) ? Math.max(1200, Math.min(15000, o.duration)) : 4200;
    const wrap = ensureToastWrap();

    const el = document.createElement('div');
    el.className = `tp-toast tp-theme-${t}`;
    el.setAttribute('role', 'status');
    el.setAttribute('aria-live', 'polite');

    const actionsHtml = Array.isArray(o.actions) && o.actions.length
      ? `<div class="tp-toast__actions">${o.actions.map((a, i) => `<button class="tp-btn ${a?.primary ? 'tp-btn--primary' : ''} ${a?.danger ? 'tp-btn--danger' : ''}" data-tp-action="${i}">${String(a?.label || 'OK')}</button>`).join('')}</div>`
      : '';

    el.innerHTML = `
      <div class="tp-toast__row">
        <div class="tp-toast__icon">${ICONS[t] || ICONS.info}</div>
        <div style="min-width:0; flex:1 1 auto;">
          <div class="tp-toast__title">${escapeHtml(title)}</div>
          <div class="tp-toast__msg">${escapeHtml(msg)}</div>
          ${actionsHtml}
        </div>
      </div>
      <div class="tp-progress"><div style="animation-duration:${duration}ms"></div></div>
    `;

    wrap.appendChild(el);

    let done = false;
    function close() {
      if (done) return;
      done = true;
      el.classList.add('tp-toast-out');
      setTimeout(() => { el.remove(); }, 220);
    }

    let timer = setTimeout(close, duration);

    el.addEventListener('click', (e) => {
      const btn = e.target && e.target.closest ? e.target.closest('[data-tp-action]') : null;
      if (!btn) return;
      const idx = parseInt(btn.getAttribute('data-tp-action'), 10);
      const action = (o.actions || [])[idx];
      try { if (action && typeof action.onClick === 'function') action.onClick(); } catch (_) {}
      clearTimeout(timer);
      close();
    });

    el.addEventListener('mouseenter', () => { clearTimeout(timer); });
    el.addEventListener('mouseleave', () => {
      clearTimeout(timer);
      timer = setTimeout(close, 1200);
    });

    return { close };
  }

  function confirmDialog(opts) {
    const o = opts && typeof opts === 'object' ? opts : {};
    return modalDialog({
      type: o.type || (o.danger ? 'warning' : 'info'),
      title: o.title || 'Confirmar ação',
      message: o.message || 'Tem certeza?',
      primaryText: o.confirmText || 'Confirmar',
      secondaryText: o.cancelText || 'Cancelar',
      danger: !!o.danger,
      allowOutsideClose: true,
    });
  }

  function modalDialog(opts) {
    const o = opts && typeof opts === 'object' ? opts : {};
    const type = normalizeType(o.type);
    const title = String(o.title || 'Aviso');
    const message = String(o.message || '');
    const primaryText = String(o.primaryText || 'OK');
    const secondaryText = (o.secondaryText === null || o.secondaryText === undefined) ? '' : String(o.secondaryText || '');
    const danger = !!o.danger;
    const allowOutsideClose = (o.allowOutsideClose === undefined) ? true : !!o.allowOutsideClose;

    return new Promise((resolve) => {
      const overlay = document.createElement('div');
      overlay.className = 'tp-overlay';
      overlay.setAttribute('role', 'dialog');
      overlay.setAttribute('aria-modal', 'true');
      overlay.tabIndex = -1;

      const modal = document.createElement('div');
      modal.className = `tp-modal tp-theme-${type}`;

      const icon = ICONS[type] || ICONS.info;
      modal.innerHTML = `
        <div class="tp-modal__top">
          <div class="tp-modal__head">
            <div class="tp-modal__type">${icon}</div>
            <h3 class="tp-modal__title">${escapeHtml(title)}</h3>
          </div>
          <button class="tp-modal__close" type="button" aria-label="Fechar">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="M6 6l12 12"/></svg>
          </button>
        </div>
        <div class="tp-modal__body">${escapeHtml(message)}</div>
        <div class="tp-modal__actions">
          ${secondaryText ? `<button class="tp-btn" type="button" data-tp-cancel>${escapeHtml(secondaryText)}</button>` : ''}
          <button class="tp-btn ${danger ? 'tp-btn--danger' : 'tp-btn--primary'}" type="button" data-tp-confirm>${escapeHtml(primaryText)}</button>
        </div>
      `;

      overlay.appendChild(modal);

      const previousActive = document.activeElement;
      document.body.classList.add('tp-lock-scroll');
      document.body.appendChild(overlay);

      function cleanup(val) {
        document.body.classList.remove('tp-lock-scroll');
        overlay.remove();
        if (previousActive && typeof previousActive.focus === 'function') {
          try { previousActive.focus(); } catch (_) {}
        }
        resolve(val);
      }

      const btnClose = modal.querySelector('.tp-modal__close');
      const btnCancel = modal.querySelector('[data-tp-cancel]');
      const btnConfirm = modal.querySelector('[data-tp-confirm]');

      function onKey(e) {
        if (e.key === 'Escape') {
          e.preventDefault();
          cleanup(false);
          return;
        }
        if (e.key === 'Tab') trapFocus(e, modal);
      }

      overlay.addEventListener('click', (e) => {
        if (!allowOutsideClose) return;
        if (e.target === overlay) cleanup(false);
      });
      btnClose && btnClose.addEventListener('click', () => cleanup(false));
      btnCancel && btnCancel.addEventListener('click', () => cleanup(false));
      btnConfirm && btnConfirm.addEventListener('click', () => cleanup(true));
      document.addEventListener('keydown', onKey, true);
      setTimeout(() => { if (btnConfirm) btnConfirm.focus(); }, 0);
    });
  }

  function alertDialog(opts) {
    const o = opts && typeof opts === 'object' ? opts : {};
    return modalDialog({
      type: o.type || 'info',
      title: o.title || 'Aviso',
      message: o.message || '',
      primaryText: o.okText || 'OK',
      secondaryText: '',
      danger: !!o.danger,
      allowOutsideClose: true,
    }).then(() => undefined);
  }

  function trapFocus(e, root) {
    const focusables = root.querySelectorAll('button,[href],input,select,textarea,[tabindex]:not([tabindex="-1"])');
    const list = Array.from(focusables).filter(el => !el.hasAttribute('disabled') && el.getAttribute('aria-hidden') !== 'true');
    if (!list.length) return;
    const first = list[0];
    const last = list[list.length - 1];
    const active = document.activeElement;
    if (e.shiftKey && active === first) {
      e.preventDefault();
      last.focus();
    } else if (!e.shiftKey && active === last) {
      e.preventDefault();
      first.focus();
    }
  }

  function escapeHtml(s) {
    return String(s)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function bindConfirmForms(root) {
    const scope = root || document;
    const forms = scope.querySelectorAll('form[data-confirm]');
    forms.forEach((form) => {
      if (form.__tpBound) return;
      form.__tpBound = true;
      form.addEventListener('submit', async (e) => {
        if (form.__tpSubmitting) return;
        e.preventDefault();
        const message = form.getAttribute('data-confirm') || 'Tem certeza?';
        const title = form.getAttribute('data-confirm-title') || 'Confirmar';
        const confirmText = form.getAttribute('data-confirm-ok') || 'Remover';
        const cancelText = form.getAttribute('data-confirm-cancel') || 'Cancelar';
        const danger = (form.getAttribute('data-confirm-danger') || '1') !== '0';
        const ok = await confirmDialog({ title, message, confirmText, cancelText, danger });
        if (!ok) return;
        form.__tpSubmitting = true;
        form.submit();
      });
    });
  }

  function installAlertConfirmPolyfill() {
    const originalAlert = window.alert;
    const originalConfirm = window.confirm;
    window.alert = function (msg) {
      toast('info', String(msg || ''), { title: 'Aviso' });
      return undefined;
    };
    window.confirm = function (msg) {
      return confirmDialog({
        title: 'Confirmação',
        message: String(msg || ''),
        confirmText: 'Confirmar',
        cancelText: 'Cancelar',
        danger: true,
      });
    };
    window.__tpOriginalAlert = originalAlert;
    window.__tpOriginalConfirm = originalConfirm;
  }

  window.ThunderPopup = {
    toast,
    confirm: confirmDialog,
    modal: modalDialog,
    alert: alertDialog,
    bindConfirmForms,
    installPolyfill: installAlertConfirmPolyfill,
  };

  document.addEventListener('DOMContentLoaded', () => {
    bindConfirmForms(document);
  });
})();
