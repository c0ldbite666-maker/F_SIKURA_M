const API = '/foodmood/api';
/* ---------- ГЛОБАЛЬНОЕ СОСТОЯНИЕ ---------- */
let RESTAURANTS = [];
let favorites = new Set();
let CURRENT_USER = null;
let CURRENT_RESTAURANT_ID = null;
let slideIdx = 0;
let quizAnswers = { mood: null, budget: null, company: null };

/* ---------- ХЕЛПЕР ЗАПРОСОВ ---------- */
async function api(path, opts = {}) {
  const res = await fetch(API + path, {
    method: opts.method || 'GET',
    headers: { 'Content-Type': 'application/json' },
    credentials: 'same-origin',
    body: opts.body ? JSON.stringify(opts.body) : undefined,
  });
  let data = {};
  try { data = await res.json(); } catch (_) {}
  if (!res.ok) {
    const err = new Error(data.error || 'Ошибка запроса');
    err.status = res.status;
    throw err;
  }
  return data;
}


function showPage(pageId, id) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  const el = document.getElementById(pageId);
  if (!el) { document.getElementById('notfound').classList.add('active'); return; }
  el.classList.add('active');
  
  if (pageId === 'restaurant' && id) openRestaurant(id);
  
  if (pageId === 'favorites') {
    if (!CURRENT_USER) { showPage('auth'); return; }
    loadFavorites(); // Загружаем избранные рестораны
  }
  if (pageId === 'cart') {
    if (!CURRENT_USER) { showPage('auth'); return; }
    loadCart(); // Загружаем корзину
  }
  
  if (pageId === 'admin') {
    loadAdminStats();
    loadAdminRestaurants();
  }
  window.scrollTo(0, 0);
  closeMobileMenu();
}

function toggleMobileMenu() { document.getElementById('navMenu').classList.toggle('open'); }
function closeMobileMenu() { document.getElementById('navMenu').classList.remove('open'); }


function cardHTML(r) {
  const liked = favorites.has(r.id) ? 'liked' : '';
  const moods = (r.moods || []).map(m => `<span class="mood-tag">${m}</span>`).join('');
  const price = r.price || '₽₽';
  const stars = r.stars || '★★★★★';
  return `<div class="card card-wrapper" onclick="showPage('restaurant', ${r.id})">
    <button class="heart-icon ${liked}" onclick="event.stopPropagation();toggleFavorite(${r.id}, this)">♥</button>
    <img src="${r.img || 'img/placeholder.jpg'}" alt="${escapeHtml(r.name)}">
    <div class="card-content">
      <h3>${escapeHtml(r.name)}</h3>
      <div class="rating">${stars} ${r.rating ?? ''}</div>
      ${moods}
      <p class="price">${price} · ${escapeHtml(r.city || '')}</p>
    </div>
  </div>`;
}

function renderHomeTop() {
  const el = document.getElementById('homeTopGrid');
  if (!el) return;
  el.innerHTML = RESTAURANTS.slice(0, 4).map(cardHTML).join('');
}

function renderCatalog(list) {
  const grid = document.getElementById('catalogGrid');
  const empty = document.getElementById('catalogEmpty');
  if (!grid) return;
  if (!list.length) {
    grid.innerHTML = '';
    if (empty) empty.style.display = 'block';
    return;
  }
  if (empty) empty.style.display = 'none';
  grid.innerHTML = list.map(cardHTML).join('');
}

function renderFavorites() {
  const grid = document.getElementById('favRestGrid');
  const empty = document.getElementById('favEmpty');
  if (!grid) return;
  const list = RESTAURANTS.filter(r => favorites.has(r.id));
  if (!list.length) {
    grid.innerHTML = '';
    if (empty) empty.style.display = 'block';
    return;
  }
  if (empty) empty.style.display = 'none';
  grid.innerHTML = list.map(cardHTML).join('');
}


async function loadRestaurants(params = {}) {
  const qs = new URLSearchParams();
  Object.entries(params).forEach(([k, v]) => {
    if (Array.isArray(v) && v.length) qs.set(k, v.join(','));
    else if (v) qs.set(k, v);
  });
  const list = await api('/restaurants.php?' + qs.toString());
  RESTAURANTS = list;
  renderHomeTop();
  renderCatalog(RESTAURANTS);
}

async function loadFavorites() {
  if (!CURRENT_USER) {
    favorites.clear();
    renderFavorites();
    renderHomeTop();
    updateFavBtn();
    return;
  }
  try {
    const list = await api('/favorites.php');
    favorites = new Set(list.map(r => r.id));
  } catch (e) {
    favorites.clear();
  }
  renderFavorites();
  renderHomeTop();
  updateFavBtn();
}

async function loadCurrentUser() {
  try {
    const me = await api('/auth.php?action=me');
    CURRENT_USER = (me && me.id) ? me : null;
  } catch {
    CURRENT_USER = null;
  }
  updateAuthButton();
}


async function toggleFavorite(id, btn) {
  if (!CURRENT_USER) {
    alert('Войдите, чтобы добавлять в избранное');
    showPage('auth');
    return;
  }
  try {
    const res = await api('/favorites.php', { method: 'POST', body: { restaurant_id: id } });
    if (res.favorited) favorites.add(id); else favorites.delete(id);
    if (btn) btn.classList.toggle('liked', res.favorited);
    renderFavorites();
    renderHomeTop();
    updateFavBtn();
  } catch (e) {
    alert(e.message);
  }
}

function toggleFavoriteCurrent(btn) {
  if (!CURRENT_RESTAURANT_ID) return;
  toggleFavorite(CURRENT_RESTAURANT_ID, btn);
}

function updateFavBtn() {
  const btn = document.getElementById('favBtn');
  if (!btn) return;
  const liked = CURRENT_RESTAURANT_ID && favorites.has(CURRENT_RESTAURANT_ID);
  btn.textContent = liked ? 'В избранном' : 'В избранное';
  btn.style.color = liked ? '#C0392B' : 'var(--burgundy)';
  btn.style.borderColor = liked ? '#C0392B' : 'var(--burgundy)';
}


async function openRestaurant(id) {
  try {
    const r = await api('/restaurants.php?id=' + id);
    CURRENT_RESTAURANT_ID = r.id;

    // Слайдер
    const slider = document.getElementById('restSlider');
    if (slider) {
      const imgs = (r.gallery && r.gallery.length) ? r.gallery : [r.img || 'img/placeholder.jpg'];
      slider.innerHTML = imgs.map((src, i) =>
        `<img src="${src}" class="${i === 0 ? 'active' : ''}" alt="${escapeHtml(r.name)}">`
      ).join('') + `
        <button class="slider-btn prev" onclick="slide(-1)">‹</button>
        <button class="slider-btn next" onclick="slide(1)">›</button>`;
      slideIdx = 0;
    }

    // Заголовок
    document.getElementById('restName').textContent = r.name;
    document.getElementById('restRating').textContent = `${r.stars} ${r.rating} (${r.reviews_count} отзывов)`;
    document.getElementById('restDesc').textContent = r.description || '';
    document.getElementById('restMoods').innerHTML = (r.moods || []).map(m => `<span class="mood-tag">${m}</span>`).join('');

    // Меню с кнопкой "В корзину"
    const menuBox = document.getElementById('menuList');
    menuBox.innerHTML = (r.menu || []).map(item => `
      <div class="menu-item" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
          <h4>${escapeHtml(item.name)}</h4>
          <p>${escapeHtml(item.description || '')}</p>
        </div>
        <div style="display:flex; align-items:center; gap:1rem;">
          <div style="font-weight:600;color:var(--burgundy);">${item.price} ₽</div>
          <button class="btn btn-burgundy btn-small" onclick="addToCart(${item.id})">В корзину</button>
        </div>
      </div>`).join('');

    // Сайдбар
    document.getElementById('restAddress').innerHTML = `<strong>Адрес:</strong> ${escapeHtml(r.address || '—')}`;
    document.getElementById('restPhone').innerHTML = `<strong>Телефон:</strong> ${escapeHtml(r.phone || '—')}`;
    document.getElementById('restAvg').innerHTML = `<strong>Средний чек:</strong> ${r.avg_check || 0} ₽`;
    document.getElementById('restHoursWeek').textContent = r.work_hours_week || '';
    document.getElementById('restHoursWeekend').textContent = r.work_hours_weekend || '';

    // Отзывы
    const rvList = document.getElementById('reviewsList');
    rvList.innerHTML = (r.reviews || []).map(rv => {
      const stars = '★'.repeat(rv.rating) + '☆'.repeat(5 - rv.rating);
      return `<div class="review-card">
        <div class="review-header">
          <div class="review-author">
            <div style="width:44px;height:44px;background:var(--cream);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--burgundy);font-weight:600;">${escapeHtml(rv.author_name.charAt(0).toUpperCase())}</div>
            <div><h4>${escapeHtml(rv.author_name)}</h4><div class="rating">${stars}</div></div>
          </div>
          <span style="color: var(--text-light); font-size: 0.85rem;">${formatDate(rv.created_at)}</span>
        </div>
        <p style="font-size: 0.95rem;">${escapeHtml(rv.text)}</p>
      </div>`;
    }).join('') || '<p class="empty-msg">Пока нет отзывов. Будьте первым!</p>';

    updateFavBtn();
  } catch (e) {
    alert(e.message);
    showPage('catalog');
  }
}

function slide(dir) {
  const imgs = document.querySelectorAll('#restSlider img');
  if (!imgs.length) return;
  imgs[slideIdx].classList.remove('active');
  slideIdx = (slideIdx + dir + imgs.length) % imgs.length;
  imgs[slideIdx].classList.add('active');
}

function getChecked(cls) {
  return [...document.querySelectorAll('.' + cls + ':checked')].map(i => i.value);
}

async function applyFilters() {
  try {
    await loadRestaurants({
      moods: getChecked('f-mood'),
      cities: getChecked('f-city'),
      budgets: getChecked('f-budget'),
      dishes: getChecked('f-dish'),
    });
  } catch (e) {
    alert(e.message);
  }
}

function resetFilters() {
  document.querySelectorAll('.f-mood,.f-city,.f-budget,.f-dish').forEach(i => i.checked = false);
  loadRestaurants();
}

function filterByMood(mood) {
  showPage('catalog');
  document.querySelectorAll('.f-mood').forEach(i => { i.checked = (i.value === mood); });
  applyFilters();
}

let searchTimer;
function globalSearchHandler(q) {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(async () => {
    showPage('catalog');
    try {
      if (!q.trim()) { await loadRestaurants(); return; }
      const list = await api('/restaurants.php?q=' + encodeURIComponent(q));
      renderCatalog(list);
    } catch (e) { /* ignore */ }
  }, 300);
}


document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.quiz-options').forEach(group => {
    group.querySelectorAll('.quiz-option').forEach(opt => {
      opt.addEventListener('click', () => {
        group.querySelectorAll('.quiz-option').forEach(o => o.classList.remove('selected'));
        opt.classList.add('selected');
        quizAnswers[group.dataset.q] = opt.dataset.val;
      });
    });
  });
});

async function runQuiz() {
  if (!quizAnswers.mood || !quizAnswers.budget || !quizAnswers.company) {
    alert('Пожалуйста, ответьте на все три вопроса.');
    return;
  }
  try {
    const pick = await api('/quiz.php', {
      method: 'POST',
      body: { mood: quizAnswers.mood, budget: quizAnswers.budget }
    });
    const res = document.getElementById('quizResult');
    res.style.display = 'block';
    res.innerHTML = `
      <h3>Рекомендуем посетить:</h3>
      ${cardHTML(pick)}
    `;
  } catch (e) {
    alert(e.message);
  }
}
function openModal(id) {
  const m = document.getElementById(id);
  if (!m) return;
  m.classList.add('open');
  document.body.style.overflow = 'hidden';
}
function closeModal(id) {
  const m = document.getElementById(id);
  if (!m) return;
  m.classList.remove('open');
  document.body.style.overflow = '';
}
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
});


function switchAuthTab(btn, type) {
  btn.parentElement.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('register-form').style.display = type === 'register' ? 'block' : 'none';
  document.getElementById('login-form').style.display = type === 'login' ? 'block' : 'none';
}

async function submitRegister(e) {
  e.preventDefault();
  const emailErr = document.getElementById('regEmailErr');
  const passErr = document.getElementById('regPassErr');
  emailErr.classList.remove('show');
  passErr.classList.remove('show');

  try {
    const res = await api('/auth.php?action=register', {
      method: 'POST',
      body: {
        first_name: document.getElementById('regName').value,
        last_name: document.getElementById('regSurname').value,
        email: document.getElementById('regEmail').value,
        password: document.getElementById('regPass').value,
        password2: document.getElementById('regPass2').value,
        city: document.getElementById('regCity').value,
      }
    });
    CURRENT_USER = res.user;
    updateAuthButton();
    await loadFavorites();
    showPage('favorites');
  } catch (err) {
    const msg = err.message || 'Ошибка';
    if (msg.toLowerCase().includes('email')) {
      emailErr.textContent = msg;
      emailErr.classList.add('show');
    } else if (msg.toLowerCase().includes('парол')) {
      passErr.textContent = msg;
      passErr.classList.add('show');
    } else {
      alert(msg);
    }
  }
}

async function submitLogin(e) {
  e.preventDefault();
  try {
    const res = await api('/auth.php?action=login', {
      method: 'POST',
      body: {
        email: document.getElementById('loginEmail').value,
        password: document.getElementById('loginPass').value,
      }
    });
    CURRENT_USER = res.user;
    updateAuthButton();
    await loadFavorites();
    showPage('favorites');
  } catch (err) {
    alert(err.message);
  }
}

async function logout() {
  try { await api('/auth.php?action=logout', { method: 'POST' }); } catch {}
  CURRENT_USER = null;
  favorites.clear();
  updateAuthButton();
  renderFavorites();
  renderHomeTop();
  showPage('home');
}

function authButtonClick() {
  if (CURRENT_USER) logout();
  else showPage('auth');
}

function updateAuthButton() {
  const btn = document.getElementById('authBtn');
  const badge = document.getElementById('userBadge');
  if (!btn) return;
  if (CURRENT_USER) {
    btn.textContent = 'Выйти';
    if (badge) {
      badge.textContent = CURRENT_USER.first_name || CURRENT_USER.email;
      badge.classList.add('visible');
    }
  } else {
    btn.textContent = 'Войти';
    if (badge) {
      badge.textContent = '';
      badge.classList.remove('visible');
    }
  }
}

async function submitReview(e) {
  e.preventDefault();
  if (!CURRENT_RESTAURANT_ID) return;
  try {
    await api('/reviews.php', {
      method: 'POST',
      body: {
        restaurant_id: CURRENT_RESTAURANT_ID,
        author_name: document.getElementById('reviewName').value,
        rating: +document.getElementById('reviewRating').value,
        text: document.getElementById('reviewText').value,
      }
    });
    e.target.reset();
    openRestaurant(CURRENT_RESTAURANT_ID);
  } catch (err) {
    alert(err.message);
  }
}

async function submitBooking(e) {
  e.preventDefault();
  if (!CURRENT_RESTAURANT_ID) {
    alert('Сначала выберите заведение');
    return;
  }
  try {
    await api('/bookings.php', {
      method: 'POST',
      body: {
        restaurant_id: CURRENT_RESTAURANT_ID,
        name: document.getElementById('bookName').value,
        phone: document.getElementById('bookPhone').value,
        booking_at: document.getElementById('bookDate').value.replace('T', ' ') + ':00',
        guests: document.getElementById('bookGuests').value,
      }
    });
    closeModal('bookModal');
    document.getElementById('thanksMsg').textContent = 'Стол успешно забронирован.';
    showPage('thanks');
  } catch (err) {
    alert(err.message);
  }
}

async function submitCall(e) {
  e.preventDefault();
  try {
    await api('/callbacks.php', {
      method: 'POST',
      body: {
        name: document.getElementById('callName').value,
        phone: document.getElementById('callPhone').value,
      }
    });
    closeModal('callModal');
    document.getElementById('thanksMsg').textContent = 'Заявка принята. Мы перезвоним в течение 15 минут.';
    showPage('thanks');
  } catch (err) {
    alert(err.message);
  }
}

async function submitContact(e) {
  e.preventDefault();
  try {
    await api('/contacts.php', {
      method: 'POST',
      body: {
        name: document.getElementById('contactName').value,
        email: document.getElementById('contactEmail').value,
        message: document.getElementById('contactMsg').value,
      }
    });
    document.getElementById('thanksMsg').textContent = 'Сообщение отправлено. Мы ответим на указанный email.';
    showPage('thanks');
  } catch (err) {
    alert(err.message);
  }
}

async function subscribeSubmit(e) {
  e.preventDefault();
  const input = e.target.querySelector('input[name="email"]');
  try {
    await api('/subscribe.php', { method: 'POST', body: { email: input.value } });
    e.target.reset();
    alert('Вы успешно подписались на обновления.');
  } catch (err) {
    alert(err.message);
  }
}

function showAdminSection(el, section) {
  document.querySelectorAll('.admin-menu li').forEach(li => li.classList.remove('active'));
  el.classList.add('active');
  document.querySelectorAll('.admin-section').forEach(s => s.classList.remove('active'));
  const target = document.getElementById('admin-' + section);
  if (target) target.classList.add('active');

  const labels = {
    dashboard: 'Дашборд',
    restaurants: 'Заведения',
    users: 'Пользователи',
    collections: 'Подборки',
    reviews: 'Отзывы',
    analytics: 'Аналитика',
    settings: 'Настройки',
  };
  const bc = document.getElementById('adminBreadcrumb');
  if (bc) bc.textContent = labels[section] || section;

  if (section === 'dashboard' || section === 'restaurants') loadAdminRestaurants();
  if (section === 'users') loadAdminUsers();
  if (section === 'reviews') loadAdminReviews();
}

async function loadAdminStats() {
  try {
    const s = await api('/admin.php?action=stats');
    const map = { users: s.users, restaurants: s.restaurants, bookings: s.bookings, reviews: s.reviews };
    Object.entries(map).forEach(([k, v]) => {
      const el = document.querySelector(`[data-stat="${k}"]`);
      if (el) el.textContent = v;
    });
  } catch (e) {
    console.warn('admin stats:', e.message);
  }
}

async function loadAdminRestaurants() {
  try {
    const list = await api('/admin.php?action=restaurants');
    const tbody1 = document.getElementById('adminRestTable');
    const tbody2 = document.getElementById('adminRestFullTable');

    const actionBtns = (r) => `
      <button class="btn btn-outline btn-small" style="color: var(--burgundy); border-color: var(--burgundy); margin-right: 5px;" onclick="openAdminEditModal(${r.id})">Изменить</button>
      <button class="btn btn-outline btn-small" style="color: #C0392B; border-color: #C0392B;" onclick="deleteAdminRestaurant(${r.id})">Удалить</button>
    `;

    if (tbody1) {
      tbody1.innerHTML = list.slice(0, 5).map(r => `
        <tr>
          <td><img src="${r.img || 'img/placeholder.jpg'}" alt=""> ${escapeHtml(r.name)}</td>
          <td>${r.rating}</td>
          <td>${r.reviews_count}</td>
          <td>${actionBtns(r)}</td>
        </tr>`).join('') || '<tr><td colspan="4" style="text-align:center;color:var(--text-light);">Нет данных</td></tr>';
    }

    if (tbody2) {
      tbody2.innerHTML = list.map(r => `
        <tr>
          <td>${escapeHtml(r.name)}</td>
          <td>${escapeHtml(r.city)}</td>
          <td>${r.rating}</td>
          <td>${r.is_active ? 'Активно' : 'Скрыто'}</td>
          <td>${actionBtns(r)}</td>
        </tr>`).join('') || '<tr><td colspan="5" style="text-align:center;color:var(--text-light);">Нет данных</td></tr>';
    }
  } catch (e) {
    console.warn('admin restaurants:', e.message);
  }
}

async function loadAdminUsers() {
  try {
    const list = await api('/admin.php?action=users');
    const tbody = document.getElementById('adminUsersTable');
    if (!tbody) return;
    tbody.innerHTML = list.map(u => `
      <tr>
        <td>${escapeHtml(u.first_name + ' ' + u.last_name)}</td>
        <td>${escapeHtml(u.email)}</td>
        <td>${escapeHtml(u.city || '')}</td>
        <td>${formatDate(u.created_at)}</td>
      </tr>`).join('') || '<tr><td colspan="4" style="text-align:center;color:var(--text-light);">Нет данных</td></tr>';
  } catch (e) {
    console.warn('admin users:', e.message);
  }
}

async function loadAdminReviews() {
  try {
    const list = await api('/admin.php?action=reviews');
    const tbody = document.getElementById('adminReviewsTable');
    if (!tbody) return;
    tbody.innerHTML = list.map(rv => `
      <tr>
        <td>${escapeHtml(rv.author_name)}</td>
        <td>${escapeHtml(rv.restaurant)}</td>
        <td>${rv.rating}</td>
        <td>${escapeHtml(rv.text.slice(0, 80))}${rv.text.length > 80 ? '…' : ''}</td>
      </tr>`).join('') || '<tr><td colspan="4" style="text-align:center;color:var(--text-light);">Нет данных</td></tr>';
  } catch (e) {
    console.warn('admin reviews:', e.message);
  }
}

function adminSearchHandler(q) {
  if (!q.trim()) { loadAdminRestaurants(); return; }
  const ql = q.toLowerCase();
  const rows = [...document.querySelectorAll('#adminRestTable tr, #adminRestFullTable tr')];
  rows.forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(ql) ? '' : 'none';
  });
}

async function loadCart() {
  if (!CURRENT_USER) {
    document.getElementById('cartItemsList').innerHTML = '<p style="text-align:center;padding:2rem;">Войдите, чтобы увидеть корзину.</p>';
    return;
  }
  document.getElementById('cartUserName').textContent = CURRENT_USER.first_name || 'Гость';
  
  try {
    const data = await api('/cart.php');
    const list = document.getElementById('cartItemsList');
    
    if (!data.items.length) {
      list.innerHTML = '<p style="text-align: center; opacity: 0.5; padding: 2rem;">Ваша корзина пуста</p>';
    } else {
      list.innerHTML = data.items.map(item => `
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #5C2E2E; padding: 1rem 0;">
          <div style="display: flex; gap: 1rem; align-items: center;">
            <div style="width: 60px; height: 60px; background: #5C2E2E; border-radius: 10px; flex-shrink: 0;"></div>
            <div>
              <h4 style="font-size: 1rem; margin-bottom: 0.2rem;">${escapeHtml(item.name)}</h4>
              <p style="font-size: 0.8rem; opacity: 0.6;">${escapeHtml(item.description || '')}</p>
            </div>
          </div>
          <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="display: flex; align-items: center; background: #5C2E2E; border-radius: 20px; padding: 0.3rem 0.8rem;">
              <button style="background:none;border:none;color:#E8DCC8;cursor:pointer;font-size:1.2rem;" onclick="updateCartItem(${item.item_id}, ${item.quantity - 1})">-</button>
              <span style="margin: 0 0.5rem; min-width: 20px; text-align: center;">${item.quantity}</span>
              <button style="background:none;border:none;color:#E8DCC8;cursor:pointer;font-size:1.2rem;" onclick="updateCartItem(${item.item_id}, ${item.quantity + 1})">+</button>
            </div>
            <span style="font-weight: bold; width: 80px; text-align: right;">${item.price * item.quantity} ₽</span>
          </div>
        </div>
      `).join('');
    }

    const delivery = data.items.length > 0 ? 149 : 0;
    const total = data.total + delivery;
    
    document.getElementById('cartCount').textContent = data.items.reduce((acc, i) => acc + i.quantity, 0);
    document.getElementById('cartSubtotal').textContent = data.total + ' ₽';
    document.getElementById('cartTotal').textContent = total + ' ₽';
    document.getElementById('cartTotalBottom').textContent = total;
    
    document.getElementById('deliveryPrice').textContent = delivery + ' ₽';
    if (document.getElementById('deliverySummary')) {
      document.getElementById('deliverySummary').textContent = delivery + ' ₽';
    }
    
    const diff = 1000 - data.total;
    document.getElementById('freeDeliveryDiff').textContent = diff > 0 ? diff + ' ₽' : '0 ₽';

  } catch (e) {
    console.error('Ошибка корзины', e);
  }
}

async function addToCart(itemId) {
  if (!CURRENT_USER) { alert('Войдите, чтобы добавить в корзину'); showPage('auth'); return; }
  try {
    await api('/cart.php', { method: 'POST', body: { menu_item_id: itemId, action: 'add' } });
    alert('Добавлено в корзину!');
  } catch (e) { alert(e.message); }
}

async function updateCartItem(itemId, newQty) {
  if (newQty < 1) {
    if (!confirm('Удалить блюдо из корзины?')) return;
  }
  try {
    await api('/cart.php', { method: 'POST', body: { menu_item_id: itemId, quantity: newQty, action: 'update' } });
    loadCart();
  } catch (e) { alert(e.message); }
}


function openAdminEditModal(id = null) {
  document.getElementById('adminEditModal').classList.add('open');
  document.body.style.overflow = 'hidden';
  
  if (id) {
    document.getElementById('adminModalTitle').textContent = 'Редактирование заведения';
    // Запрашиваем данные текущего ресторана
    api('/restaurants.php?id=' + id).then(r => {
      document.getElementById('editRestId').value = r.id;
      document.getElementById('editRestName').value = r.name;
      document.getElementById('editRestCity').value = r.city;
      document.getElementById('editRestAddress').value = r.address || '';
      document.getElementById('editRestAvgCheck').value = r.avg_check || 0;
      document.getElementById('editRestPriceLevel').value = r.price_level || 2;
      document.getElementById('editRestDesc').value = r.description || '';
    });
  } else {
    document.getElementById('adminModalTitle').textContent = 'Добавление заведения';
    document.getElementById('editRestId').value = '';
    document.getElementById('editRestName').value = '';
    document.getElementById('editRestCity').value = '';
    document.getElementById('editRestAddress').value = '';
    document.getElementById('editRestAvgCheck').value = '';
    document.getElementById('editRestPriceLevel').value = 2;
    document.getElementById('editRestDesc').value = '';
  }
}

async function saveAdminRestaurant(e) {
  e.preventDefault();
  const id = document.getElementById('editRestId').value;
  const body = {
    id: id ? Number(id) : 0,
    name: document.getElementById('editRestName').value,
    city: document.getElementById('editRestCity').value,
    address: document.getElementById('editRestAddress').value,
    avg_check: Number(document.getElementById('editRestAvgCheck').value),
    price_level: Number(document.getElementById('editRestPriceLevel').value),
    description: document.getElementById('editRestDesc').value,
  };

  try {
    await api('/admin.php?action=save_restaurant', { method: 'POST', body });
    closeModal('adminEditModal');
    loadAdminRestaurants(); // Обновляем таблицу
    loadRestaurants(); // Обновляем каталог на главной
  } catch (err) {
    alert(err.message);
  }
}

async function deleteAdminRestaurant(id) {
  if (!confirm('Вы уверены, что хотите удалить заведение?')) return;
  try {
    await api('/admin.php?action=delete_restaurant', { method: 'DELETE', body: { id } });
    loadAdminRestaurants();
    loadRestaurants();
  } catch (err) {
    alert(err.message);
  }
}

function escapeHtml(s) {
  return String(s ?? '').replace(/[&<>"']/g, c => (
    { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
  ));
}

function formatDate(dt) {
  if (!dt) return '';
  const d = new Date(dt.replace(' ', 'T'));
  if (isNaN(d.getTime())) return dt;
  return d.toLocaleDateString('ru-RU', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

(async function init() {
  await loadCurrentUser();
  try { await loadRestaurants(); } catch (e) { console.warn('restaurants:', e.message); }
  await loadFavorites();

  const cq = document.getElementById('captchaQ');
  if (cq) cq.textContent = '2 + 3 = ?';
  const cq2 = document.getElementById('captchaQ2');
  if (cq2) cq2.textContent = '4 + 1 = ?';

  window.addEventListener('hashchange', () => {
    const id = location.hash.replace('#', '');
    if (id) showPage(id);
  });
})();