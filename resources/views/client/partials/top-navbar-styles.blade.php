/* Shared tenant client top nav — include only on client-facing pages */
:root {
    --client-nav-font: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    --client-nav-offset: 108px;
}

.navbar {
    font-family: var(--client-nav-font);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    background: var(--white);
    padding: 0 18px;
    min-height: 76px;
    height: auto;
    padding-top: 10px;
    padding-bottom: 10px;
    display: grid;
    grid-template-columns: auto 1fr auto;
    align-items: center;
    gap: 12px;
    box-shadow: 0 4px 20px rgba(27, 94, 32, 0.1);
    position: fixed !important;
    width: 100%;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
}

.navbar a,
.navbar button {
    font-family: inherit;
}

.nav-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; flex-shrink: 0; min-width: 0; }
.nav-logo img { width: 45px; height: 45px; border-radius: 0; border: none; object-fit: contain; flex-shrink: 0; }
.nav-logo-text { display: flex; flex-direction: column; align-items: flex-start; justify-content: center; gap: 1px; min-width: 0; max-width: min(260px, 36vw); }
.nav-logo-title {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--green-dark);
    line-height: 1.15;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 100%;
}
.nav-logo-subtitle {
    font-size: 0.65rem;
    font-weight: 600;
    color: var(--green-medium);
    line-height: 1.2;
    letter-spacing: 0.02em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    width: 100%;
}

.nav-links { display: flex; gap: 4px; list-style: none; flex: 1; min-width: 0; justify-content: center; }
.nav-links a {
    text-decoration: none;
    color: var(--gray-600);
    font-weight: 600;
    font-size: 0.75rem;
    padding: 8px 10px;
    border-radius: 8px;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}
.nav-links a:hover, .nav-links a.active {
    background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
    color: var(--white);
    box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
}

.nav-actions { display: flex; gap: 8px; align-items: center; justify-self: end; flex-shrink: 0; }
.user-display {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    background: linear-gradient(135deg, var(--green-soft), var(--green-white));
    border-radius: 10px;
    border: 1px solid var(--green-soft);
    max-width: 280px;
    min-width: 0;
}
.user-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
}
.user-info { text-align: left; }
.user-name {
    font-weight: 700;
    color: var(--green-dark);
    font-size: 0.75rem;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
}
.user-role {
    font-size: 0.62rem;
    color: var(--green-medium);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.nav-btn {
    padding: 8px 12px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.72rem;
    text-decoration: none;
    transition: all 0.3s;
    cursor: pointer;
    border: none;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}
.nav-btn.primary {
    background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
    color: var(--white);
}
.nav-btn.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
}

.nav-toggle {
    display: none;
    background: transparent;
    border: 1px solid var(--green-soft);
    color: var(--green-dark);
    width: 40px;
    height: 40px;
    border-radius: 10px;
    cursor: pointer;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
}
.nav-toggle:focus-visible { outline: 2px solid var(--green-primary); outline-offset: 2px; }

@media (max-width: 960px) {
    .nav-toggle { display: inline-flex; order: 2; justify-self: end; }
    .nav-links {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: var(--white);
        flex-direction: column;
        align-items: stretch;
        padding: 12px 14px;
        gap: 6px;
        box-shadow: 0 10px 25px rgba(27, 94, 32, 0.12);
        border-top: 1px solid var(--green-soft);
        max-height: calc(100vh - 64px);
        overflow-y: auto;
    }
    .nav-links a { width: 100%; }
    #appNavbar.nav-open .nav-links { display: flex; }
    .nav-actions { display: none; }
    #appNavbar.nav-open .nav-actions {
        display: flex;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        padding: 0 14px 14px;
        background: var(--white);
        flex-wrap: wrap;
        gap: 10px;
        box-shadow: 0 10px 25px rgba(27, 94, 32, 0.12);
        transform: translateY(calc(100% + 1px));
    }
}

@media (max-width: 768px) {
    .navbar { padding: 8px 12px; min-height: 64px; height: auto; }
    .user-display { max-width: 170px; }
}

/* =================== Global responsive helpers (client pages) =================== */
html, body { max-width: 100%; overflow-x: hidden; }
img, video, iframe { max-width: 100%; height: auto; }

@media (max-width: 768px) {
    .card, .panel { overflow-x: auto; }
    table { display: block; width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .main-content, main.main-content { padding-left: 14px !important; padding-right: 14px !important; }
    .kpi-grid, .stats-grid, .grid-2, .grid-3, .grid-4, .accommodations-grid { grid-template-columns: 1fr !important; gap: 14px !important; }
    h1, .page-title { font-size: 1.5rem !important; }
}

@media (max-width: 480px) {
    .nav-btn, .btn, button.primary { width: 100%; justify-content: center; }
}