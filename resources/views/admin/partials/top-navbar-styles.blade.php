.navbar {
    background: var(--white);
    padding: 0 18px;
    height: 76px;
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

.nav-logo { display: flex; align-items: center; gap: 8px; text-decoration: none; flex-shrink: 0; }
.nav-logo img { width: 45px; height: 45px; border-radius: 0; border: none; object-fit: contain; }
.nav-logo span { font-size: 1.1rem; font-weight: 700; color: var(--green-dark); line-height: 1.05; }

.nav-links { display: flex; gap: 4px; list-style: none; flex: 1; min-width: 0; justify-content: center; }
.nav-links a {
    text-decoration: none;
    color: var(--gray-600, #4B5563);
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
.user-info {
    text-align: left;
}
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

@media (max-width: 768px) {
    .navbar { padding: 0 12px; height: 64px; }
    .nav-links { display: none; }
    .user-display { max-width: 170px; }
}
