<?php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="theme-color" content="#5C2E2E">
<title>F_Сикура_M — Подбор корейских заведений</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  :root {
    --burgundy: #5C2E2E;
    --burgundy-dark: #3D1F1F;
    --cream: #E8DCC8;
    --cream-light: #F5EDE0;
    --rose: #D4A5A5;
    --rose-light: #E8C4C4;
    --text-dark: #2C1810;
    --text-light: #6B5D52;
    --white: #FFFFFF;
    --shadow: 0 4px 20px rgba(92, 46, 46, 0.1);
  }
  body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: var(--cream-light);
    color: var(--text-dark);
    line-height: 1.6;
    overflow-x: hidden; /* Защита от горизонтального скролла */
  }
  .page { display: none; }
  .page.active { display: block; }

  /* NAVBAR */
  .navbar {
    background: var(--burgundy);
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 1000;
    gap: 1rem;
    flex-wrap: wrap;
  }
  .logo {
    font-size: 1.4rem;
    font-weight: 600;
    color: var(--white);
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    letter-spacing: 0.5px;
  }
  .nav-menu {
    display: flex;
    gap: 1.5rem;
    list-style: none;
    align-items: center;
  }
  .nav-menu a {
    color: var(--white);
    text-decoration: none;
    cursor: pointer;
    transition: opacity 0.3s;
    font-size: 0.95rem;
  }
  .nav-menu a:hover { opacity: 0.8; }
  .nav-search {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    border: none;
    font-size: 0.85rem;
    min-width: 180px;
    background: rgba(255,255,255,0.9);
    font-family: inherit;
  }
  .nav-buttons { display: flex; gap: 0.6rem; flex-wrap: wrap; align-items: center; }
  .user-badge {
    color: var(--white);
    font-size: 0.9rem;
    padding: 0.4rem 0.8rem;
    background: rgba(255,255,255,0.12);
    border-radius: 20px;
    display: none;
  }
  .user-badge.visible { display: inline-block; }
  .btn {
    padding: 0.6rem 1.5rem;
    border-radius: 25px;
    border: none;
    cursor: pointer;
    font-size: 0.9rem;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
    font-family: inherit;
  }
  .btn-outline {
    background: transparent;
    color: var(--white);
    border: 2px solid var(--white);
  }
  .btn-outline:hover { background: var(--white); color: var(--burgundy); }
  .btn-primary { background: var(--rose); color: var(--white); }
  .btn-primary:hover { background: var(--rose-light); transform: translateY(-2px); }
  .btn-burgundy { background: var(--burgundy); color: var(--white); }
  .btn-burgundy:hover { background: var(--burgundy-dark); }
  .btn-small { padding: 0.5rem 1rem; font-size: 0.85rem; }
  .burger {
    display: none;
    background: transparent;
    border: none;
    color: var(--white);
    font-size: 1.6rem;
    cursor: pointer;
  }

  /* HERO */
  .hero {
    position: relative;
    color: var(--white);
    padding: 4rem 2rem;
    text-align: center;
    min-height: 520px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    overflow: hidden;
  }
  .hero-img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 0;
  }
  .hero-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(rgba(92,46,46,0.6), rgba(61,31,31,0.8));
    z-index: 1;
  }
  .hero-inner {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    max-width: 800px;
  }
  .hero h1 { font-size: 2.5rem; margin-bottom: 1rem; font-weight: 600; line-height: 1.2; word-wrap: break-word; }
  .hero p { font-size: 1.1rem; margin-bottom: 2rem; opacity: 0.9; font-style: italic; }
  .mood-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
    margin: 1.5rem 0;
  }
  .mood-btn {
    padding: 0.8rem 1.5rem;
    background: var(--cream);
    color: var(--text-dark);
    border: none;
    border-radius: 30px;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s;
    font-family: inherit;
  }
  .mood-btn:hover { background: var(--rose); color: var(--white); }
  .randomizer {
    background: var(--rose);
    padding: 1.2rem 2.5rem;
    border-radius: 20px;
    display: inline-block;
    margin-top: 2rem;
    cursor: pointer;
    transition: all 0.3s;
  }
  .randomizer:hover { background: var(--rose-light); }
  .randomizer h3 { color: var(--white); margin-bottom: 0.3rem; font-size: 1.1rem; font-weight: 600; }
  .randomizer p { color: var(--white); font-size: 0.9rem; }

  /* SECTIONS */
  .section { padding: 3rem 2rem; max-width: 1200px; margin: 0 auto; }
  .section-title {
    font-size: 1.8rem;
    color: var(--burgundy);
    margin-bottom: 2rem;
    text-align: center;
    font-weight: 600;
  }
  .grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
  }
  .card {
    background: var(--white);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow);
    transition: all 0.3s;
    cursor: pointer;
  }
  .card:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(92,46,46,0.15); }
  .card img { width: 100%; height: 200px; object-fit: cover; display: block; }
  .card-content { padding: 1.5rem; }
  .card h3 { color: var(--burgundy); margin-bottom: 0.5rem; font-size: 1.2rem; }
  .rating { color: #FFB800; margin: 0.5rem 0; font-size: 0.95rem; }
  .mood-tag {
    display: inline-block;
    background: var(--cream);
    color: var(--burgundy);
    padding: 0.3rem 0.8rem;
    border-radius: 12px;
    font-size: 0.8rem;
    margin-top: 0.5rem;
    margin-right: 0.3rem;
  }
  .price { color: var(--text-light); margin-top: 0.5rem; font-weight: 600; font-size: 0.9rem; }
  .empty-msg {
    text-align: center;
    color: var(--text-light);
    padding: 2rem;
    font-style: italic;
  }

  /* CATALOG */
  .catalog-container {
    display: flex;
    max-width: 1400px;
    margin: 2rem auto;
    padding: 0 2rem;
    gap: 2rem;
  }
  .filters-sidebar {
    width: 280px;
    background: var(--white);
    padding: 2rem;
    border-radius: 16px;
    box-shadow: var(--shadow);
    height: fit-content;
    flex-shrink: 0;
  }
  .filter-group { margin-bottom: 1.5rem; }
  .filter-group h3 { color: var(--burgundy); margin-bottom: 0.8rem; font-size: 1rem; font-weight: 600; }
  .filter-option {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    cursor: pointer;
    font-size: 0.95rem;
  }
  .catalog-content { flex: 1; min-width: 0; }

  /* RESTAURANT DETAIL */
  .restaurant-detail { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
  .restaurant-header {
    background: var(--white);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow);
    margin-bottom: 2rem;
  }
  .slider {
    position: relative;
    height: 350px;
    overflow: hidden;
    background: var(--cream);
  }
  .slider img {
    width: 100%; height: 100%; object-fit: cover;
    position: absolute; top: 0; left: 0; opacity: 0;
    transition: opacity 0.5s;
  }
  .slider img.active { opacity: 1; }
  .slider-btn {
    position: absolute; top: 50%; transform: translateY(-50%);
    background: rgba(92,46,46,0.6); color: var(--white);
    border: none; width: 40px; height: 40px; border-radius: 50%;
    cursor: pointer; font-size: 1.2rem; z-index: 5;
  }
  .slider-btn.prev { left: 1rem; }
  .slider-btn.next { right: 1rem; }
  .restaurant-info { padding: 2rem; }
  .restaurant-info h1 { color: var(--burgundy); font-size: 2rem; margin-bottom: 0.5rem; }
  .details-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
  }
  .details-main, .details-sidebar {
    background: var(--white);
    padding: 2rem;
    border-radius: 16px;
    box-shadow: var(--shadow);
  }
  .menu-item {
    display: flex;
    justify-content: space-between;
    padding: 1rem 0;
    border-bottom: 1px solid var(--cream);
    gap: 1rem;
  }
  .menu-item h4 { color: var(--burgundy); font-size: 1.05rem; margin-bottom: 0.2rem; }
  .menu-item p { color: var(--text-light); font-size: 0.9rem; }
  .map-box {
    margin-top: 1rem;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid var(--cream);
  }
  .map-box iframe { width: 100%; height: 220px; border: 0; display: block; }

  /* REVIEWS */
  .review-card {
    background: var(--white);
    padding: 1.5rem;
    border-radius: 16px;
    box-shadow: var(--shadow);
    margin-bottom: 1rem;
  }
  .review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    gap: 1rem;
  }
  .review-author {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  .review-author img {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    object-fit: cover;
  }
  .review-form {
    background: var(--cream-light);
    padding: 1.5rem;
    border-radius: 16px;
    margin-top: 1.5rem;
  }
  .review-form h4 { color: var(--burgundy); margin-bottom: 1rem; font-size: 1.1rem; }

  /* QUIZ */
  .quiz-container {
    max-width: 800px;
    margin: 4rem auto;
    padding: 3rem;
    background: var(--white);
    border-radius: 20px;
    box-shadow: var(--shadow);
    text-align: center;
  }
  .quiz-container h2 { color: var(--burgundy); font-size: 1.8rem; margin-bottom: 1rem; font-weight: 600; }
  .quiz-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin: 1rem 0 2rem;
  }
  .quiz-option {
    padding: 1.2rem;
    background: var(--cream-light);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s;
    border: 2px solid transparent;
    font-family: inherit;
    font-size: 0.95rem;
  }
  .quiz-option:hover { border-color: var(--rose); }
  .quiz-option.selected { background: var(--burgundy); color: var(--white); }
  .quiz-result {
    display: none;
    margin-top: 2rem;
    padding: 2rem;
    background: var(--cream-light);
    border-radius: 16px;
    text-align: left;
  }
  .quiz-result h3 { color: var(--burgundy); margin-bottom: 1rem; font-size: 1.2rem; }

  /* AUTH */
  .auth-container {
    display: flex;
    min-height: calc(100vh - 70px);
  }
  .auth-left {
    flex: 1;
    background: var(--burgundy);
    color: var(--white);
    padding: 4rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  .auth-left h2 { font-size: 2.2rem; margin-bottom: 1rem; font-weight: 600; }
  .auth-left .highlight { color: var(--rose); font-style: italic; }
  .auth-left-stats {
    display: flex;
    gap: 3rem;
    margin-top: 3rem;
    flex-wrap: wrap;
  }
  .auth-left-stats div h3 { font-size: 1.8rem; color: var(--rose); font-weight: 600; }
  .auth-left-stats div p { font-size: 0.9rem; opacity: 0.9; }
  .mood-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 0.8rem;
    margin-top: 2rem;
  }
  .mood-pill {
    padding: 0.6rem 1.2rem;
    border-radius: 20px;
    border: 1px solid var(--rose);
    background: transparent;
    color: var(--white);
    cursor: pointer;
    font-family: inherit;
    font-size: 0.9rem;
  }
  .mood-pill.active { background: var(--rose); border-color: var(--rose); color: var(--burgundy); font-weight: 600; }
  .auth-right {
    flex: 1;
    background: var(--cream-light);
    padding: 4rem;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .auth-form { width: 100%; max-width: 500px; }
  .auth-form h2 { color: var(--burgundy); font-size: 1.8rem; margin-bottom: 0.5rem; font-weight: 600; }
  .auth-form .subtitle { color: var(--text-light); margin-bottom: 2rem; }
  .auth-tabs {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 2rem;
    background: var(--cream);
    padding: 0.3rem;
    border-radius: 25px;
    width: fit-content;
  }
  .auth-tab {
    padding: 0.6rem 1.5rem;
    border-radius: 20px;
    cursor: pointer;
    border: none;
    font-size: 0.95rem;
    font-family: inherit;
    background: transparent;
    color: var(--text-light);
  }
  .auth-tab.active { background: var(--white); color: var(--burgundy); font-weight: 600; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
  .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
  .form-group { margin-bottom: 1.2rem; }
  .form-group label {
    display: block;
    margin-bottom: 0.4rem;
    color: var(--text-dark);
    font-weight: 500;
    font-size: 0.9rem;
  }
  .form-group input, .form-group textarea, .form-group select {
    width: 100%;
    padding: 0.8rem 1rem;
    border: 1px solid var(--cream);
    border-radius: 10px;
    font-size: 0.95rem;
    background: var(--white);
    font-family: inherit;
  }
  .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
    outline: none; border-color: var(--burgundy);
  }
  .field-error { color: #C0392B; font-size: 0.8rem; margin-top: 0.3rem; display: none; }
  .field-error.show { display: block; }
  .checkbox-group {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    margin: 1.5rem 0;
    font-size: 0.85rem;
    color: var(--text-light);
  }
  .checkbox-group input { width: auto; margin-top: 0.25rem; }
  .checkbox-group a { color: var(--burgundy); }
  .captcha-box {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: var(--white);
    padding: 0.8rem 1rem;
    border-radius: 10px;
    border: 1px solid var(--cream);
    margin-bottom: 1.2rem;
  }
  .captcha-box input { width: 80px; padding: 0.4rem; text-align: center; border: 1px solid var(--cream); border-radius: 6px; }
  .captcha-question { font-weight: 600; color: var(--burgundy); font-size: 0.95rem; }
  .social-login {
    text-align: center;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--cream);
  }
  .social-login p { color: var(--text-light); margin-bottom: 1rem; font-size: 0.9rem; }
  .social-buttons {
    display: flex;
    justify-content: center;
    gap: 0.8rem;
    flex-wrap: wrap;
  }
  .social-btn {
    padding: 0.6rem 1.2rem;
    background: var(--white);
    color: var(--text-dark);
    border-radius: 20px;
    border: 1px solid var(--cream);
    cursor: pointer;
    font-size: 0.9rem;
    font-family: inherit;
    transition: all 0.2s;
  }
  .social-btn:hover { border-color: var(--burgundy); color: var(--burgundy); }

  /* ADMIN */
  .admin-container { display: flex; min-height: calc(100vh - 70px); }
  .admin-sidebar {
    width: 260px;
    background: var(--burgundy);
    color: var(--white);
    padding: 2rem 0;
    flex-shrink: 0;
  }
  .admin-sidebar h2 {
    padding: 0 2rem 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    margin-bottom: 1rem;
    font-size: 1.4rem;
  }
  .admin-menu { list-style: none; }
  .admin-menu li {
    padding: 0.8rem 2rem;
    cursor: pointer;
    transition: background 0.3s;
    font-size: 0.95rem;
  }
  .admin-menu li:hover, .admin-menu li.active { background: var(--burgundy-dark); font-weight: 600; }
  .admin-content {
    flex: 1;
    padding: 2rem;
    background: var(--cream-light);
    min-width: 0;
  }
  .admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    gap: 1rem;
    flex-wrap: wrap;
  }
  .admin-header div { font-size: 0.95rem; }
  .admin-search {
    flex: 1;
    max-width: 300px;
    padding: 0.6rem 1.2rem;
    border: 1px solid var(--cream);
    border-radius: 20px;
    font-size: 0.9rem;
    font-family: inherit;
  }
  .admin-user {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    font-size: 0.95rem;
  }
  .admin-user img {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
  }
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
  }
  .stat-card {
    background: var(--white);
    padding: 1.5rem;
    border-radius: 16px;
    box-shadow: var(--shadow);
  }
  .stat-card h3 { color: var(--text-light); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: normal; }
  .stat-card .number { font-size: 1.8rem; color: var(--burgundy); font-weight: 600; }
  .stat-card .change { color: #4CAF50; font-size: 0.85rem; margin-top: 0.3rem; }
  .admin-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
  }
  .admin-table {
    background: var(--white);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: var(--shadow);
    overflow-x: auto;
  }
  .admin-table h3 { color: var(--burgundy); margin-bottom: 1.5rem; font-size: 1.1rem; }
  .admin-table table { width: 100%; border-collapse: collapse; min-width: 500px; }
  .admin-table th, .admin-table td {
    padding: 0.8rem;
    text-align: left;
    border-bottom: 1px solid var(--cream-light);
    font-size: 0.9rem;
  }
  .admin-table th { color: var(--text-light); font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.5px; }
  .admin-table img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    vertical-align: middle;
    margin-right: 0.5rem;
  }
  .chart-card {
    background: var(--white);
    padding: 1.5rem;
    border-radius: 16px;
    box-shadow: var(--shadow);
  }
  .chart-card h3 { color: var(--burgundy); margin-bottom: 1.5rem; font-size: 1.1rem; }
  .donut {
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: conic-gradient(
      var(--burgundy) 0% 45%,
      var(--rose) 45% 70%,
      var(--rose-light) 70% 90%,
      var(--cream) 90% 100%
    );
    margin: 0 auto;
    position: relative;
  }
  .donut::after {
    content: '842\A Заведений';
    white-space: pre;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: var(--white);
    width: 110px;
    height: 110px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-weight: 600;
    color: var(--burgundy);
    font-size: 0.9rem;
  }
  .chart-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 0.8rem;
    margin-top: 1.5rem;
    justify-content: center;
  }
  .legend-item { display: flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; }
  .legend-dot { width: 10px; height: 10px; border-radius: 50%; }
  .admin-section { display: none; }
  .admin-section.active { display: block; }

  /* FOOTER */
  .footer {
    background: var(--burgundy);
    color: var(--white);
    padding: 3rem 2rem 1rem;
    margin-top: 4rem;
  }
  .footer-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto 2rem;
  }
  .footer h4 { margin-bottom: 1rem; color: var(--rose); font-weight: 600; font-size: 1.1rem; }
  .footer p { font-size: 0.9rem; opacity: 0.8; }
  .footer ul { list-style: none; }
  .footer li { margin-bottom: 0.5rem; font-size: 0.9rem; }
  .footer a { color: var(--white); text-decoration: none; cursor: pointer; opacity: 0.8; }
  .footer a:hover { opacity: 1; }
  .footer-bottom {
    text-align: center;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255,255,255,0.1);
    font-size: 0.85rem;
    opacity: 0.7;
  }
  .subscribe-form {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
    flex-wrap: wrap;
  }
  .subscribe-form input {
    flex: 1;
    min-width: 150px;
    padding: 0.6rem 1rem;
    border-radius: 20px;
    border: none;
    font-family: inherit;
    font-size: 0.9rem;
  }

  /* MOOD SECTION ON HOME */
  .mood-section {
    background: var(--white);
    padding: 3rem 2rem;
    border-radius: 20px;
    margin: 2rem auto;
    max-width: 1200px;
    box-shadow: var(--shadow);
  }
  .mood-section h2 { color: var(--burgundy); text-align: center; margin-bottom: 2rem; font-size: 1.6rem; font-weight: 600; }
  .mood-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
  }
  .mood-card {
    background: var(--cream-light);
    padding: 1.5rem;
    border-radius: 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    border: 1px solid transparent;
  }
  .mood-card:hover { border-color: var(--rose); background: var(--white); transform: translateY(-3px); box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
  .mood-card h3 { color: var(--text-dark); font-size: 1rem; font-weight: 500; }

  /* MODALS */
  .modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(44,24,16,0.6);
    z-index: 2000;
    align-items: center;
    justify-content: center;
    padding: 1rem;
  }
  .modal-overlay.open { display: flex; }
  .modal {
    background: var(--white);
    border-radius: 20px;
    padding: 2rem;
    max-width: 440px;
    width: 100%;
    position: relative;
    max-height: 90vh;
    overflow-y: auto;
  }
  .modal h3 { color: var(--burgundy); margin-bottom: 1.5rem; font-size: 1.3rem; font-weight: 600; }
  .modal-close {
    position: absolute;
    top: 1rem; right: 1rem;
    background: var(--cream-light);
    border: none;
    width: 32px; height: 32px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1rem;
    color: var(--burgundy);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .modal .btn { width: 100%; padding: 0.8rem; margin-top: 0.5rem; }

  /* THANK YOU / 404 */
  .center-page {
    min-height: 60vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 3rem 2rem;
  }
  .center-page .big { font-size: 5rem; color: var(--rose); line-height: 1; font-weight: 600; }

  /* АДАПТИВНОСТЬ */
  @media (max-width: 900px) {
    .navbar { 
      flex-wrap: wrap; 
      padding: 1rem; 
      gap: 0.5rem;
    }
    
    .burger { display: block; order: 2; font-size: 1.8rem; } 
    
    .nav-menu {
      display: none;
      width: 100%;
      order: 5;
      flex-direction: column;
      align-items: flex-start;
      gap: 0.8rem;
      padding: 1rem;
      background: var(--burgundy-dark);
      border-radius: 10px;
      margin-top: 0.5rem;
    }
    .nav-menu.open { display: flex; }
    .nav-menu a { width: 100%; display: block; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.1); }
    
    .nav-search { width: 100%; order: 4; margin-top: 0.5rem; }
    .nav-buttons { width: 100%; order: 3; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem; margin-top: 0.5rem;}
    
    .catalog-container, .admin-container, .auth-container { flex-direction: column; }
    .filters-sidebar, .admin-sidebar { width: 100%; }
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .admin-grid, .details-grid { grid-template-columns: 1fr; }
    .footer-grid { grid-template-columns: 1fr; gap: 1.5rem; }
    .form-row { grid-template-columns: 1fr; }
    .quiz-options { grid-template-columns: 1fr; }
    
    .hero h1 { font-size: 1.8rem; }
    .hero p { font-size: 1rem; }
    
    .auth-left, .auth-right { padding: 2rem; }
    
    .cart-wrapper > div {
      grid-template-columns: 1fr !important;
    }
  }
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="logo" onclick="showPage('home')">F_Сикура_M</div>
  
  <button class="burger" onclick="toggleMobileMenu()">☰</button>

  <ul class="nav-menu" id="navMenu">
    <li><a onclick="showPage('catalog')">Заведения</a></li>
    <li><a onclick="showPage('collections')">Подборки</a></li>
    <li><a onclick="showPage('favorites')">Избранное</a></li>
    <li><a onclick="showPage('cart')">Корзина</a></li>
    <li><a onclick="showPage('blog')">Блог</a></li>
    <li><a onclick="showPage('about')">О нас</a></li>
    <li><a onclick="showPage('contacts')">Контакты</a></li>
  </ul>
  
  <input type="text" class="nav-search" id="globalSearch" placeholder="Поиск по сайту..." oninput="globalSearchHandler(this.value)">
  
  <div class="nav-buttons">
    <span class="user-badge" id="userBadge"></span>
    <button class="btn btn-outline" id="authBtn" onclick="authButtonClick()">Войти</button>
    <button class="btn btn-burgundy" onclick="showPage('admin')" style="background: var(--rose);">Админ</button>
    <button class="btn btn-primary" onclick="showPage('quiz')">Выбор за меня</button>
  </div>
</nav>

<!-- ГЛАВНАЯ -->
<div id="home" class="page active">
  <div class="hero">
    <img class="hero-img" src="img/hero.jpg" alt="Корейская кухня">
    <div class="hero-overlay"></div>
    <div class="hero-inner">
      <h1>Куда пойти и что съесть под ваше настроение?</h1>
      <p>Забудьте о муках выбора. Найдите идеальное корейское место за минуту.</p>
      <div class="mood-buttons">
        <button class="mood-btn" onclick="filterByMood('Уютный вечер')">Уютный вечер</button>
        <button class="mood-btn" onclick="filterByMood('Быстрый перекус')">Быстрый перекус</button>
        <button class="mood-btn" onclick="filterByMood('Деловой обед')">Деловой обед</button>
        <button class="mood-btn" onclick="filterByMood('Произвести впечатление')">Произвести впечатление</button>
      </div>
      <div class="randomizer" onclick="showPage('quiz')">
        <h3>Не можете решить? Доверьтесь нам</h3>
        <p>Запустить подбор по параметрам</p>
      </div>
    </div>
  </div>

  <div class="section">
    <h2 class="section-title">Что вам ближе?</h2>
    <div class="mood-grid">
      <div class="mood-card" onclick="filterByMood('Уютный вечер')">
        <h3>Хочу уютно поужинать</h3>
      </div>
      <div class="mood-card" onclick="filterByMood('Быстрый перекус')">
        <h3>Нужно быстро перекусить</h3>
      </div>
      <div class="mood-card" onclick="filterByMood('Произвести впечатление')">
        <h3>Хочу удивить гостей</h3>
      </div>
      <div class="mood-card" onclick="filterByMood('Деловой обед')">
        <h3>Тихо поработать</h3>
      </div>
    </div>
  </div>

  <div class="section">
    <h2 class="section-title">Популярные заведения</h2>
    <div class="grid" id="homeTopGrid"></div>
  </div>

  <div class="section">
    <h2 class="section-title">Гид по блюдам</h2>
    <div class="grid">
      <div class="card" onclick="showPage('catalog')">
        <img src="img/dish1.jpg" alt="Пибимпап">
        <div class="card-content">
          <h3>Пибимпап</h3>
          <p>Рис с овощами и мясом</p>
        </div>
      </div>
      <div class="card" onclick="showPage('catalog')">
        <img src="img/dish2.jpg" alt="Кимчи-чиге">
        <div class="card-content">
          <h3>Кимчи-чиге</h3>
          <p>Острый суп с тофу</p>
        </div>
      </div>
      <div class="card" onclick="showPage('catalog')">
        <img src="img/dish3.jpg" alt="Хе из осетра">
        <div class="card-content">
          <h3>Хе из осетра</h3>
          <p>Традиционная закуска</p>
        </div>
      </div>
    </div>
  </div>

  <footer class="footer">
    <div class="footer-grid">
      <div>
        <h4>F_Сикура_M</h4>
        <p>© 2026 F_Сикура_M. Все права защищены.</p>
        <p style="margin-top: 1rem; font-size: 0.9rem;">Подпишитесь на новые подборки:</p>
        <form class="subscribe-form" onsubmit="subscribeSubmit(event)">
          <input type="email" name="email" placeholder="Ваш email" required>
          <button class="btn btn-primary" type="submit">Подписаться</button>
        </form>
      </div>
      <div>
        <h4>Социальные сети</h4>
        <ul>
          <li>VK</li>
          <li>Telegram</li>
          <li>Instagram</li>
        </ul>
      </div>
      <div>
        <h4>Помощь</h4>
        <ul>
          <li><a onclick="showPage('about')">Как это работает</a></li>
          <li><a onclick="showPage('contacts')">FAQ</a></li>
          <li><a onclick="showPage('contacts')">Обратная связь</a></li>
          <li><a onclick="showPage('contacts')">Пользовательское соглашение</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>Сервис подбора корейской кухни в России</p>
    </div>
  </footer>
</div>

<!-- КАТАЛОГ -->
<div id="catalog" class="page">
  <div class="catalog-container">
    <div class="filters-sidebar">
      <h3 style="color: var(--burgundy); margin-bottom: 1.5rem; font-weight: 600;">Фильтры</h3>
      <div class="filter-group">
        <h3>Настроение</h3>
        <label class="filter-option"><input type="checkbox" class="f-mood" value="Уютный вечер"> Уютный вечер</label>
        <label class="filter-option"><input type="checkbox" class="f-mood" value="Быстрый перекус"> Быстрый перекус</label>
        <label class="filter-option"><input type="checkbox" class="f-mood" value="Деловой обед"> Деловой обед</label>
        <label class="filter-option"><input type="checkbox" class="f-mood" value="Произвести впечатление"> Произвести впечатление</label>
        <label class="filter-option"><input type="checkbox" class="f-mood" value="Бюджетный вариант"> Бюджетный вариант</label>
      </div>
      <div class="filter-group">
        <h3>Город</h3>
        <label class="filter-option"><input type="checkbox" class="f-city" value="Москва"> Москва</label>
        <label class="filter-option"><input type="checkbox" class="f-city" value="СПб"> Санкт-Петербург</label>
        <label class="filter-option"><input type="checkbox" class="f-city" value="Казань"> Казань</label>
        <label class="filter-option"><input type="checkbox" class="f-city" value="Екатеринбург"> Екатеринбург</label>
        <label class="filter-option"><input type="checkbox" class="f-city" value="Владивосток"> Владивосток</label>
      </div>
      <div class="filter-group">
        <h3>Бюджет</h3>
        <label class="filter-option"><input type="checkbox" class="f-budget" value="1"> До 1000 руб.</label>
        <label class="filter-option"><input type="checkbox" class="f-budget" value="2"> 1000-2000 руб.</label>
        <label class="filter-option"><input type="checkbox" class="f-budget" value="3"> 2000-3000 руб.</label>
        <label class="filter-option"><input type="checkbox" class="f-budget" value="4"> Более 3000 руб.</label>
      </div>
      <div class="filter-group">
        <h3>Тип блюда</h3>
        <label class="filter-option"><input type="checkbox" class="f-dish" value="BBQ"> BBQ (кальби, пулькоги)</label>
        <label class="filter-option"><input type="checkbox" class="f-dish" value="Супы"> Супы (чиге, рамен)</label>
        <label class="filter-option"><input type="checkbox" class="f-dish" value="Рис"> Рисовые блюда</label>
        <label class="filter-option"><input type="checkbox" class="f-dish" value="Закуски"> Закуски (кимчи, хе)</label>
        <label class="filter-option"><input type="checkbox" class="f-dish" value="Десерты"> Десерты</label>
      </div>
      <button class="btn btn-burgundy" style="width: 100%;" onclick="applyFilters()">Применить</button>
      <button class="btn btn-outline" style="width: 100%; margin-top: 0.5rem; color: var(--burgundy); border-color: var(--burgundy);" onclick="resetFilters()">Сбросить</button>
    </div>

    <div class="catalog-content">
      <h2 style="color: var(--burgundy); margin-bottom: 2rem; font-weight: 600;">Корейские заведения России</h2>
      <div class="grid" id="catalogGrid"></div>
      <p class="empty-msg" id="catalogEmpty" style="display:none;">По вашему запросу ничего не найдено. Попробуйте изменить фильтры.</p>
    </div>
  </div>
</div>

<!-- КАРТОЧКА ЗАВЕДЕНИЯ -->
<div id="restaurant" class="page">
  <div class="restaurant-detail">
    <div class="restaurant-header">
      <div class="slider" id="restSlider">
        <img src="img/rest1-1.jpg" class="active" alt="Заведение 1">
        <img src="img/rest1-2.jpg" alt="Заведение 2">
        <img src="img/rest1-3.jpg" alt="Заведение 3">
        <button class="slider-btn prev" onclick="slide(-1)">‹</button>
        <button class="slider-btn next" onclick="slide(1)">›</button>
      </div>
      <div class="restaurant-info">
        <h1 id="restName">Заведение</h1>
        <div class="rating" id="restRating" style="font-size: 1.1rem;">★★★★★ 4.8</div>
        <p id="restDesc" style="margin-top: 1rem; font-size: 1rem; color: var(--text-light);"></p>
        <div style="margin-top: 1rem;" id="restMoods"></div>
      </div>
    </div>

    <div class="details-grid">
      <div class="details-main">
        <h2 style="color: var(--burgundy); margin-bottom: 1.5rem; font-weight: 600; font-size: 1.4rem;">Популярные блюда</h2>
        <div id="menuList"></div>

        <h2 style="color: var(--burgundy); margin: 2rem 0 1.5rem; font-weight: 600; font-size: 1.4rem;">Отзывы</h2>
        <div id="reviewsList"></div>

        <div class="review-form">
          <h4>Оставить отзыв</h4>
          <form onsubmit="submitReview(event)">
            <div class="form-group">
              <label>Ваше имя</label>
              <input type="text" id="reviewName" placeholder="Имя" required>
            </div>
            <div class="form-group">
              <label>Оценка</label>
              <select id="reviewRating" required>
                <option value="5">5 — Отлично</option>
                <option value="4">4 — Хорошо</option>
                <option value="3">3 — Нормально</option>
                <option value="2">2 — Плохо</option>
                <option value="1">1 — Ужасно</option>
              </select>
            </div>
            <div class="form-group">
              <label>Отзыв</label>
              <textarea id="reviewText" rows="3" placeholder="Поделитесь впечатлениями..." required></textarea>
            </div>
            <button class="btn btn-burgundy" type="submit">Отправить отзыв</button>
          </form>
        </div>
      </div>

      <div class="details-sidebar">
        <h3 style="color: var(--burgundy); margin-bottom: 1rem; font-weight: 600;">Информация</h3>
        <p style="font-size: 0.95rem;" id="restAddress"><strong>Адрес:</strong> —</p>
        <div class="map-box">
          <iframe id="restMap" src="https://www.openstreetmap.org/export/embed.html?bbox=37.60%2C55.75%2C37.62%2C55.77&layer=mapnik" loading="lazy"></iframe>
        </div>
        <p style="margin-top: 1rem; font-size: 0.95rem;"><strong>Часы работы:</strong></p>
        <p style="font-size: 0.95rem;" id="restHoursWeek">Пн-Чт: 12:00 - 23:00</p>
        <p style="font-size: 0.95rem;" id="restHoursWeekend">Пт-Вс: 12:00 - 01:00</p>
        <p style="margin-top: 1rem; font-size: 0.95rem;" id="restPhone"><strong>Телефон:</strong> —</p>
        <p style="margin-top: 1rem; font-size: 0.95rem;" id="restAvg"><strong>Средний чек:</strong> —</p>
        <p style="margin-top: 1rem; font-size: 0.95rem;"><strong>Кухня:</strong> Корейская</p>

        <button class="btn btn-burgundy" style="width: 100%; margin-top: 2rem; padding: 1rem;" onclick="openModal('bookModal')">Забронировать стол</button>
        <button class="btn btn-outline" style="width: 100%; margin-top: 0.8rem; color: var(--burgundy); border-color: var(--burgundy);" onclick="openModal('payModal')">Оплатить онлайн</button>
        <button class="btn btn-outline" style="width: 100%; margin-top: 0.8rem; color: var(--burgundy); border-color: var(--burgundy);" onclick="openModal('callModal')">Заказать звонок</button>
        <button class="btn btn-outline" style="width: 100%; margin-top: 0.8rem; color: var(--burgundy); border-color: var(--burgundy);" id="favBtn" onclick="toggleFavoriteCurrent(this)">В избранное</button>
      </div>
    </div>
  </div>
</div>

<!-- ИЗБРАННОЕ (РЕСТОРАНЫ) -->
<div id="favorites" class="page">
  <div class="section">
    <h2 class="section-title">Ваши избранные заведения</h2>
    <div class="grid" id="favRestGrid"></div>
    <p class="empty-msg" id="favEmpty" style="display:none;">Список пуст. Добавьте заведения в избранное (нажмите на сердечко).</p>
  </div>
</div>

<!-- КОРЗИНА -->
<div id="cart" class="page">
  <div class="cart-wrapper" style="background: #3D1F1F; min-height: 100vh; padding: 2rem; color: #E8DCC8; display: flex; justify-content: center;">
    <div style="max-width: 1000px; width: 100%; display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
      
      <!-- Левая часть (Список) -->
      <div>
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
          <h1 style="font-size: 2rem; cursor: pointer;" onclick="showPage('home')">← Корзина</h1>
          <span style="font-size: 1.2rem; margin-left: auto;" id="cartUserName">Привет!</span>
        </div>

        <div style="border: 1px solid #5C2E2E; border-radius: 20px; padding: 1rem;">
          <div id="cartItemsList">
             <p style="text-align: center; opacity: 0.5; padding: 2rem;">Ваша корзина пуста</p>
          </div>
          
          <div style="border-top: 1px solid #5C2E2E; padding-top: 1rem; margin-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
            <span style="font-size: 0.85rem; opacity: 0.6;">Доставка: <span id="deliveryPrice">0 ₽</span></span>
            <span style="font-size: 0.85rem;">До бесплатной доставки: <span id="freeDeliveryDiff">1000 ₽</span></span>
          </div>
          <button class="btn" style="width: 100%; margin-top: 1rem; background: #E8DCC8; color: #3D1F1F; border-radius: 20px; font-weight: bold;" onclick="alert('Заказ оформлен (Демо)')">Далее <span id="cartTotalBottom">0</span> ₽</button>
        </div>
      </div>

      <!-- Правая часть (Сводка) -->
      <div style="border: 1px solid #5C2E2E; border-radius: 20px; padding: 1.5rem; height: fit-content;">
        <h3 style="margin-bottom: 1.5rem; font-size: 1rem; text-transform: uppercase;">Сводка заказа</h3>
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.8rem;">
          <span>Товары (<span id="cartCount">0</span>)</span>
          <span id="cartSubtotal">0 ₽</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 0.8rem;">
          <span>Доставка</span>
          <span style="color: #C0392B;" id="deliverySummary">0 ₽</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 1.5rem; color: #4CAF50;">
          <span>Скидка</span>
          <span>-0 ₽</span>
        </div>
        <div style="border-top: 1px solid #5C2E2E; padding-top: 1rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; font-weight: bold; font-size: 1.2rem;">
          <span>Итого</span>
          <span id="cartTotal">0 ₽</span>
        </div>
        <button class="btn" style="width: 100%; background: #E8DCC8; color: #3D1F1F; border-radius: 20px; font-weight: bold;" onclick="alert('Оплата (Демо)')">Перейти к оплате</button>
      </div>

    </div>
  </div>
</div>

<!-- ПОДБОРКИ -->
<div id="collections" class="page">
  <div class="section">
    <h2 class="section-title">Все подборки корейской кухни</h2>
    <div class="grid">
      <div class="card">
        <img src="img/coll1.jpg" alt="BBQ">
        <div class="card-content">
          <h3>Лучший корейский BBQ в России</h3>
          <p>10 заведений</p>
        </div>
      </div>
      <div class="card">
        <img src="img/coll2.jpg" alt="Стрит-фуд">
        <div class="card-content">
          <h3>Корейский стрит-фуд до 500 руб.</h3>
          <p>15 заведений</p>
        </div>
      </div>
      <div class="card">
        <img src="img/coll3.jpg" alt="Супы">
        <div class="card-content">
          <h3>Согревающие корейские супы</h3>
          <p>8 заведений</p>
        </div>
      </div>
      <div class="card">
        <img src="img/coll4.jpg" alt="Уютные">
        <div class="card-content">
          <h3>Уютные рестораны для свидания</h3>
          <p>7 заведений</p>
        </div>
      </div>
      <div class="card">
        <img src="img/coll5.jpg" alt="Бюджет">
        <div class="card-content">
          <h3>Корейская кухня до 1000 руб.</h3>
          <p>12 заведений</p>
        </div>
      </div>
      <div class="card">
        <img src="img/coll6.jpg" alt="Премиум">
        <div class="card-content">
          <h3>Премиум корейские рестораны</h3>
          <p>5 заведений</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ВЫБЕРИ ЗА МЕНЯ -->
<div id="quiz" class="page">
  <div class="quiz-container">
    <h2>Подбор заведения</h2>
    <p style="margin-bottom: 2rem; color: var(--text-light);">Ответьте на три вопроса, и мы подберем подходящий вариант</p>

    <h3 style="margin-bottom: 1rem; color: var(--burgundy); font-size: 1.1rem;">Какое у вас настроение?</h3>
    <div class="quiz-options" data-q="mood">
      <button class="quiz-option" data-val="Уютный вечер">Уютно посидеть</button>
      <button class="quiz-option" data-val="Быстрый перекус">Быстро перекусить</button>
      <button class="quiz-option" data-val="Произвести впечатление">Произвести впечатление</button>
      <button class="quiz-option" data-val="Деловой обед">Деловой обед</button>
    </div>

    <h3 style="margin: 1rem 0; color: var(--burgundy); font-size: 1.1rem;">Ваш бюджет?</h3>
    <div class="quiz-options" data-q="budget">
      <button class="quiz-option" data-val="1">До 1000 руб.</button>
      <button class="quiz-option" data-val="2">1000-2000 руб.</button>
      <button class="quiz-option" data-val="3">2000-3000 руб.</button>
      <button class="quiz-option" data-val="4">Более 3000 руб.</button>
    </div>

    <h3 style="margin: 1rem 0; color: var(--burgundy); font-size: 1.1rem;">Компания?</h3>
    <div class="quiz-options" data-q="company">
      <button class="quiz-option" data-val="Один">Один</button>
      <button class="quiz-option" data-val="Вдвоём">Вдвоём</button>
      <button class="quiz-option" data-val="С семьёй">С семьёй</button>
      <button class="quiz-option" data-val="Большая компания">Большая компания</button>
    </div>

    <button class="btn btn-burgundy" style="margin-top: 1.5rem; padding: 1rem 2.5rem; font-size: 1rem;" onclick="runQuiz()">
      Подобрать заведение
    </button>

    <div class="quiz-result" id="quizResult"></div>
  </div>
</div>

<!-- ВХОД/РЕГИСТРАЦИЯ -->
<div id="auth" class="page">
  <div class="auth-container">
    <div class="auth-left">
      <h2>F_Сикура_M</h2>
      <p style="font-size: 1.2rem; margin-bottom: 2rem; line-height: 1.4;">Ужин под ваше <span class="highlight">усмотрение</span> за пару кликов</p>
      <p style="opacity: 0.85; line-height: 1.6; font-size: 0.95rem;">Создайте профиль, и мы подберем корейское заведение по атмосфере, компании и бюджету. Никакого бесконечного скролла отзывов.</p>

      <div class="mood-pills">
        <button class="mood-pill active">Уютный вечер</button>
        <button class="mood-pill">Быстро перекусить</button>
        <button class="mood-pill">Произвести впечатление</button>
        <button class="mood-pill">Бюджетный вариант</button>
      </div>

      <div class="auth-left-stats">
        <div>
          <h3>1200+</h3>
          <p>заведений в каталоге</p>
        </div>
        <div>
          <h3>4.8</h3>
          <p>средняя оценка сервиса</p>
        </div>
        <div>
          <h3>2 мин</h3>
          <p>от выбора до брони</p>
        </div>
      </div>
    </div>

    <div class="auth-right">
      <div class="auth-form">
        <div class="auth-tabs">
          <button class="auth-tab active" onclick="switchAuthTab(this, 'register')">Регистрация</button>
          <button class="auth-tab" onclick="switchAuthTab(this, 'login')">Вход</button>
        </div>

        <div id="register-form">
          <h2>Создание аккаунта</h2>
          <p class="subtitle">Это займет меньше минуты</p>

          <form onsubmit="submitRegister(event)">
            <div class="form-row">
              <div class="form-group">
                <label>Имя</label>
                <input type="text" id="regName" placeholder="Иван" required>
              </div>
              <div class="form-group">
                <label>Фамилия</label>
                <input type="text" id="regSurname" placeholder="Иванов" required>
              </div>
            </div>

            <div class="form-group">
              <label>Email</label>
              <input type="email" id="regEmail" placeholder="example@mail.ru" required>
              <div class="field-error" id="regEmailErr">Введите корректный email</div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Пароль</label>
                <input type="password" id="regPass" placeholder="Минимум 8 символов" required>
              </div>
              <div class="form-group">
                <label>Повторите пароль</label>
                <input type="password" id="regPass2" placeholder="Ещё раз" required>
                <div class="field-error" id="regPassErr">Пароли не совпадают</div>
              </div>
            </div>

            <div class="form-group">
              <label>Ваш город</label>
              <input type="text" id="regCity" placeholder="Москва" required>
            </div>

            <div class="captcha-box">
              <span class="captcha-question" id="captchaQ">2 + 3 = ?</span>
              <input type="text" id="captchaA" placeholder="Ответ" required>
            </div>

            <div class="checkbox-group">
              <input type="checkbox" id="regAgree" required>
              <span>Соглашаюсь с <a href="#">условиями сервиса</a> и <a href="#">политикой обработки персональных данных</a></span>
            </div>

            <button class="btn btn-burgundy" style="width: 100%; padding: 0.9rem; font-size: 1rem;" type="submit">Создать аккаунт</button>
          </form>

          <div class="social-login">
            <p>или продолжить через</p>
            <div class="social-buttons">
              <button class="social-btn" onclick="alert('Вход через VK (демо)')">VK</button>
              <button class="social-btn" onclick="alert('Вход через Telegram (демо)')">Telegram</button>
              <button class="social-btn" onclick="alert('Вход через Instagram (демо)')">Instagram</button>
            </div>
          </div>
        </div>

        <div id="login-form" style="display: none;">
          <h2>Вход в аккаунт</h2>
          <p class="subtitle">С возвращением</p>

          <form onsubmit="submitLogin(event)">
            <div class="form-group">
              <label>Email</label>
              <input type="email" id="loginEmail" placeholder="example@mail.ru" required>
            </div>

            <div class="form-group">
              <label>Пароль</label>
              <input type="password" id="loginPass" placeholder="Введите пароль" required>
            </div>

            <div class="checkbox-group">
              <input type="checkbox">
              <span>Запомнить меня</span>
            </div>

            <button class="btn btn-burgundy" style="width: 100%; padding: 0.9rem; font-size: 1rem;" type="submit">Войти</button>
          </form>

          <div class="social-login">
            <p>или войти через</p>
            <div class="social-buttons">
              <button class="social-btn" onclick="alert('Вход через VK (демо)')">VK</button>
              <button class="social-btn" onclick="alert('Вход через Telegram (демо)')">Telegram</button>
              <button class="social-btn" onclick="alert('Вход через Instagram (демо)')">Instagram</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- АДМИН-ПАНЕЛЬ -->
<div id="admin" class="page">
  <div class="admin-container">
    <div class="admin-sidebar">
      <h2>Админ-панель</h2>
      <ul class="admin-menu">
        <li class="active" onclick="showAdminSection(this, 'dashboard')">Дашборд</li>
        <li onclick="showAdminSection(this, 'restaurants')">Заведения</li>
        <li onclick="showAdminSection(this, 'users')">Пользователи</li>
        <li onclick="showAdminSection(this, 'collections')">Подборки</li>
        <li onclick="showAdminSection(this, 'reviews')">Отзывы</li>
        <li onclick="showAdminSection(this, 'analytics')">Аналитика</li>
        <li onclick="showAdminSection(this, 'settings')">Настройки</li>
      </ul>
    </div>

    <div class="admin-content">
      <div class="admin-header">
        <div>
          <span style="color: var(--text-light);">Главная / </span>
          <strong id="adminBreadcrumb" style="color: var(--burgundy);">Дашборд</strong>
        </div>
        <input type="text" class="admin-search" placeholder="Поиск..." oninput="adminSearchHandler(this.value)">
        <div class="admin-user">
          <img src="img/avatar_admin.jpg" alt="Админ">
          <span>Администратор</span>
        </div>
      </div>

      <div class="admin-section active" id="admin-dashboard">
        <div class="stats-grid">
          <div class="stat-card">
            <h3>Всего пользователей</h3>
            <div class="number" data-stat="users">—</div>
          </div>
          <div class="stat-card">
            <h3>Заведения</h3>
            <div class="number" data-stat="restaurants">—</div>
          </div>
          <div class="stat-card">
            <h3>Бронирования</h3>
            <div class="number" data-stat="bookings">—</div>
          </div>
          <div class="stat-card">
            <h3>Отзывы</h3>
            <div class="number" data-stat="reviews">—</div>
          </div>
        </div>

        <div class="admin-grid">
          <div class="admin-table">
            <h3>Последние заведения</h3>
            <table>
              <thead>
                <tr>
                  <th>Заведение</th>
                  <th>Настроение</th>
                  <th>Рейтинг</th>
                  <th>Отзывы</th>
                  <th>Действие</th>
                </tr>
              </thead>
              <tbody id="adminRestTable">
                <tr><td colspan="5" style="text-align:center;color:var(--text-light);">Загрузка...</td></tr>
              </tbody>
            </table>
          </div>

          <div class="chart-card">
            <h3>Распределение настроений</h3>
            <div class="donut"></div>
            <div class="chart-legend">
              <div class="legend-item"><div class="legend-dot" style="background: var(--burgundy);"></div> Уютный 45%</div>
              <div class="legend-item"><div class="legend-dot" style="background: var(--rose);"></div> Романтика 25%</div>
              <div class="legend-item"><div class="legend-dot" style="background: var(--rose-light);"></div> Деловой 20%</div>
              <div class="legend-item"><div class="legend-dot" style="background: var(--cream);"></div> Быстрый 10%</div>
            </div>
          </div>
        </div>
      </div>

      <div class="admin-section" id="admin-restaurants">
        <button class="btn btn-burgundy" style="margin-bottom: 1rem;" onclick="openAdminEditModal()">+ Добавить заведение</button>
        <div class="admin-table">
          <h3>Все заведения</h3>
          <table>
            <thead><tr><th>Название</th><th>Город</th><th>Рейтинг</th><th>Статус</th><th>Действия</th></tr></thead>
            <tbody id="adminRestFullTable">
              <tr><td colspan="5" style="text-align:center;color:var(--text-light);">Загрузка...</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="admin-section" id="admin-users">
        <div class="admin-table">
          <h3>Пользователи</h3>
          <table>
            <thead><tr><th>Имя</th><th>Email</th><th>Город</th><th>Регистрация</th></tr></thead>
            <tbody id="adminUsersTable">
              <tr><td colspan="4" style="text-align:center;color:var(--text-light);">Загрузка...</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="admin-section" id="admin-collections">
        <div class="admin-table">
          <h3>Подборки</h3>
          <table>
            <thead><tr><th>Название</th><th>Заведений</th><th>Просмотры</th></tr></thead>
            <tbody>
              <tr><td>Лучший корейский BBQ</td><td>10</td><td>1 240</td></tr>
              <tr><td>Стрит-фуд до 500 руб.</td><td>15</td><td>980</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="admin-section" id="admin-reviews">
        <div class="admin-table">
          <h3>Последние отзывы</h3>
          <table>
            <thead><tr><th>Автор</th><th>Заведение</th><th>Оценка</th><th>Текст</th></tr></thead>
            <tbody id="adminReviewsTable">
              <tr><td colspan="4" style="text-align:center;color:var(--text-light);">Загрузка...</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="admin-section" id="admin-analytics">
        <div class="chart-card">
          <h3>Аналитика посещений</h3>
          <p style="color: var(--text-light); margin-top: 1rem;">Модуль аналитики находится в разработке.</p>
        </div>
      </div>

      <div class="admin-section" id="admin-settings">
        <div class="admin-table">
          <h3>Настройки системы</h3>
          <div class="form-group" style="margin-top: 1rem; max-width: 400px;">
            <label>Название сайта</label>
            <input type="text" value="F_Сикура_M">
          </div>
          <div class="form-group" style="max-width: 400px;">
            <label>Email для связи</label>
            <input type="email" value="info@fsikuram.ru">
          </div>
          <button class="btn btn-burgundy" onclick="alert('Настройки сохранены (демо)')">Сохранить изменения</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="blog" class="page">
  <div class="section">
    <h2 class="section-title">Блог о корейской кухне</h2>
    <div class="grid">
      <div class="card">
        <img src="img/blog1.jpg" alt="Статья">
        <div class="card-content">
          <h3>История кимчи</h3>
          <p style="color: var(--text-light); margin-top: 0.5rem;">Как традиционное блюдо покорило мир.</p>
        </div>
      </div>
      <div class="card">
        <img src="img/blog2.jpg" alt="Статья">
        <div class="card-content">
          <h3>Топ-10 ресторанов Москвы</h3>
          <p style="color: var(--text-light); margin-top: 0.5rem;">Ежегодный рейтинг лучших заведений.</p>
        </div>
      </div>
      <div class="card">
        <img src="img/blog3.jpg" alt="Статья">
        <div class="card-content">
          <h3>Как правильно есть BBQ</h3>
          <p style="color: var(--text-light); margin-top: 0.5rem;">Этикет и секреты корейского застолья.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="about" class="page">
  <div class="section">
    <h2 class="section-title">О проекте F_Сикура_M</h2>
    <div style="background: var(--white); padding: 3rem; border-radius: 16px; box-shadow: var(--shadow); max-width: 800px; margin: 0 auto;">
      <p style="font-size: 1rem; line-height: 1.8; margin-bottom: 1.5rem;">
        <strong>F_Сикура_M</strong> — это сервис подбора корейских заведений по настроению, ситуации и бюджету. Мы помогаем найти подходящее место за минуту, избавляя от бесконечного скролла отзывов.
      </p>
      <p style="font-size: 1rem; line-height: 1.8; margin-bottom: 1.5rem;">
        Наша миссия — сделать корейскую кухню в России доступной и понятной. Мы учитываем атмосферу, уровень шума, формат компании и ваш бюджет.
      </p>
      <h3 style="color: var(--burgundy); margin: 2rem 0 1rem; font-size: 1.2rem;">Контакты</h3>
      <p style="font-size: 0.95rem; margin-bottom: 0.5rem;">Телефон: +7 (912) 123-82-54</p>
      <p style="font-size: 0.95rem; margin-bottom: 0.5rem;">Email: F.sikura.M@gmail.com</p>
      <p style="font-size: 0.95rem;">Сайт: F_Sikura_M.com</p>
    </div>
  </div>
</div>


<div id="contacts" class="page">
  <div class="section">
    <h2 class="section-title">Контакты и обратная связь</h2>
    <div style="background: var(--white); padding: 3rem; border-radius: 16px; box-shadow: var(--shadow); max-width: 700px; margin: 0 auto;">
      <p style="margin-bottom: 0.5rem; font-size: 0.95rem;">Телефон: +7 (912) 123-82-54</p>
      <p style="margin-bottom: 0.5rem; font-size: 0.95rem;">Email: F.sikura.M@gmail.com</p>
      <p style="margin-bottom: 2rem; font-size: 0.95rem;">Сайт: F_Sikura_M.com</p>

      <h3 style="color: var(--burgundy); margin-bottom: 1rem; font-size: 1.2rem;">Напишите нам</h3>
      <form onsubmit="submitContact(event)">
        <div class="form-group">
          <label>Ваше имя</label>
          <input type="text" id="contactName" placeholder="Имя" required>
        </div>
        <div class="form-group">
          <label>Email</label>
          <input type="email" id="contactEmail" placeholder="email@mail.ru" required>
        </div>
        <div class="form-group">
          <label>Сообщение</label>
          <textarea id="contactMsg" rows="4" placeholder="Ваш вопрос..." required></textarea>
        </div>
        <div class="captcha-box">
          <span class="captcha-question" id="captchaQ2">4 + 1 = ?</span>
          <input type="text" id="captchaA2" placeholder="Ответ" required>
        </div>
        <button class="btn btn-burgundy" type="submit">Отправить сообщение</button>
        <button class="btn btn-outline" type="button" style="color: var(--burgundy); border-color: var(--burgundy); margin-left: 0.5rem;" onclick="openModal('callModal')">Заказать звонок</button>
      </form>
    </div>
  </div>
</div>

<div id="notfound" class="page">
  <div class="center-page">
    <div class="big">404</div>
    <h2 style="color: var(--burgundy); margin: 1rem 0;">Страница не найдена</h2>
    <p style="color: var(--text-light); margin-bottom: 2rem;">Возможно, страница была удалена или вы ошиблись с адресом.</p>
    <button class="btn btn-burgundy" onclick="showPage('home')">Вернуться на главную</button>
  </div>
</div>

<div id="thanks" class="page">
  <div class="center-page">
    <div class="big" style="color: #4CAF50;">✓</div>
    <h2 style="color: var(--burgundy); margin: 1rem 0;">Успешно отправлено</h2>
    <p style="color: var(--text-light); margin-bottom: 2rem;" id="thanksMsg">Ваша заявка принята. Мы свяжемся с вами в ближайшее время.</p>
    <button class="btn btn-burgundy" onclick="showPage('home')">На главную</button>
  </div>
</div>

<div class="modal-overlay" id="bookModal">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('bookModal')">✕</button>
    <h3>Бронирование стола</h3>
    <form onsubmit="submitBooking(event)">
      <div class="form-group">
        <label>Имя</label>
        <input type="text" id="bookName" placeholder="Имя" required>
      </div>
      <div class="form-group">
        <label>Телефон</label>
        <input type="tel" id="bookPhone" placeholder="+7..." required>
      </div>
      <div class="form-group">
        <label>Дата и время</label>
        <input type="datetime-local" id="bookDate" required>
      </div>
      <div class="form-group">
        <label>Количество гостей</label>
        <select id="bookGuests">
          <option>1</option><option>2</option><option>3</option><option>4</option><option>5 и более</option>
        </select>
      </div>
      <button class="btn btn-burgundy" type="submit">Забронировать</button>
    </form>
  </div>
</div>

<div class="modal-overlay" id="callModal">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('callModal')">✕</button>
    <h3>Заказ обратного звонка</h3>
    <form onsubmit="submitCall(event)">
      <div class="form-group">
        <label>Имя</label>
        <input type="text" id="callName" placeholder="Имя" required>
      </div>
      <div class="form-group">
        <label>Телефон</label>
        <input type="tel" id="callPhone" placeholder="+7..." required>
      </div>
      <button class="btn btn-burgundy" type="submit">Отправить заявку</button>
    </form>
  </div>
</div>

<div class="modal-overlay" id="payModal">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('payModal')">✕</button>
    <h3>Онлайн-оплата</h3>
    <p style="color: var(--text-light); margin-bottom: 1.5rem; font-size: 0.95rem;">Платёжный шлюз находится в разработке. Вы можете оплатить заказ на месте.</p>
    <button class="btn btn-burgundy" onclick="closeModal('payModal')">Понятно</button>
  </div>
</div>

<div class="modal-overlay" id="adminEditModal">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('adminEditModal')">✕</button>
    <h3 id="adminModalTitle">Редактирование заведения</h3>
    <form onsubmit="saveAdminRestaurant(event)">
      <input type="hidden" id="editRestId">
      <div class="form-group">
        <label>Название</label>
        <input type="text" id="editRestName" required>
      </div>
      <div class="form-group">
        <label>Город</label>
        <input type="text" id="editRestCity" required>
      </div>
      <div class="form-group">
        <label>Адрес</label>
        <input type="text" id="editRestAddress">
      </div>
      <div class="form-group">
        <label>Средний чек (₽)</label>
        <input type="number" id="editRestAvgCheck">
      </div>
      <div class="form-group">
        <label>Уровень цен (1-4)</label>
        <input type="number" id="editRestPriceLevel" min="1" max="4">
      </div>
      <div class="form-group">
        <label>Описание</label>
        <textarea id="editRestDesc" rows="3"></textarea>
      </div>
      <button class="btn btn-burgundy" type="submit">Сохранить</button>
    </form>
  </div>
</div>
<script src="js/app.js"></script>
</body>
</html>