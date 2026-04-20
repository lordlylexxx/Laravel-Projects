* { margin: 0; padding: 0; box-sizing: border-box; }

:root {
    --green-dark: #1B5E20;
    --green-primary: #2E7D32;
    --green-medium: #43A047;
    --green-soft: #C8E6C9;
    --green-white: #E8F5E9;
    --cream: #F1F8E9;
    --white: #FFFFFF;
    --gray-200: #E5E7EB;
    --gray-300: #D1D5DB;
    --gray-500: #6B7280;
    --gray-700: #374151;
    --gray-800: #1F2937;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
    min-height: 100vh;
    color: var(--gray-800);
}

.dashboard-layout {
    padding-top: 82px;
}

.main-content {
    padding: 28px 36px;
}

.main-content-narrow {
    max-width: 720px;
    margin-left: auto;
    margin-right: auto;
}

.page-header {
    margin-bottom: 20px;
}

.page-header h1 {
    font-size: 2rem;
    color: var(--green-dark);
    margin-bottom: 6px;
}

.page-header p {
    color: var(--gray-500);
}

.page-header-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;
}

.page-header-row .page-header {
    margin-bottom: 0;
}

.flash {
    background: #ECFDF5;
    border: 1px solid #86EFAC;
    color: #166534;
    padding: 10px 12px;
    border-radius: 10px;
    margin-bottom: 16px;
    font-weight: 600;
}

.card {
    background: var(--white);
    border-radius: 14px;
    border: 1px solid var(--green-soft);
    box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1);
    overflow: hidden;
}

.card-padded {
    padding: 24px;
}

@media (min-width: 768px) {
    .card-padded {
        padding: 28px 32px;
    }
}

.btn-admin-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    border: none;
    cursor: pointer;
    background: linear-gradient(135deg, var(--green-dark), var(--green-primary));
    color: var(--white);
    box-shadow: 0 4px 15px rgba(46, 125, 50, 0.25);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.btn-admin-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(46, 125, 50, 0.35);
}

.btn-admin-secondary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    border: 2px solid var(--green-soft);
    background: var(--white);
    color: var(--green-dark);
    cursor: pointer;
    transition: background 0.15s ease;
}

.btn-admin-secondary:hover {
    background: var(--green-white);
}

.btn-admin-sm {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 10px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.72rem;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: opacity 0.15s ease;
}

.btn-admin-sm:hover {
    opacity: 0.92;
}

.btn-admin-sm-emerald {
    background: #059669;
    color: #fff;
}

.btn-admin-sm-outline {
    background: var(--white);
    color: var(--green-dark);
    border: 2px solid var(--green-soft);
}

.btn-admin-sm-danger {
    background: #b91c1c;
    color: #fff;
}

.btn-admin-sm-amber {
    background: #fff7ed;
    color: #9a3412;
    border: 1px solid #fdba74;
}

.btn-admin-sm-mint {
    background: #ecfdf5;
    color: #166534;
    border: 1px solid #86efac;
}

@include('admin.partials.top-navbar-styles')
