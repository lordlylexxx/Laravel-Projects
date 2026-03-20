.navbar {
    background: var(--white);
    padding: 0 40px;
    height: 70px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 20px rgba(27, 94, 32, 0.1);
    position: fixed;
    width: 100%;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
}

.nav-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
.nav-logo img { width: 45px; height: 45px; border-radius: 0; border: none; object-fit: contain; }
.nav-logo span { font-size: 1.3rem; font-weight: 700; color: var(--green-dark); }

.nav-links { display: flex; gap: 8px; list-style: none; }
.nav-links a {
    text-decoration: none;
    color: var(--gray-600, #4B5563);
    font-weight: 500;
    padding: 10px 16px;
    border-radius: 8px;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
}
.nav-links a:hover, .nav-links a.active {
    background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
    color: var(--white);
    box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
}

.nav-actions { display: flex; gap: 15px; align-items: center; }
.user-display {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 8px 16px;
    background: linear-gradient(135deg, var(--green-soft), var(--green-white));
    border-radius: 10px;
    border: 1px solid var(--green-soft);
}
.user-avatar {
    width: 38px;
    height: 38px;
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
    font-size: 0.95rem;
    line-height: 1.2;
}
.user-role {
    font-size: 0.75rem;
    color: var(--green-medium);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.nav-btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
    cursor: pointer;
    border: none;
    display: flex;
    align-items: center;
    gap: 8px;
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
    .navbar { padding: 0 20px; height: 60px; }
    .nav-links { display: none; }
}
