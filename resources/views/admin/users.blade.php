<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Management - Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --green-dark: #1B5E20;
            --green-primary: #2E7D32;
            --green-medium: #43A047;
            --green-light: #66BB6A;
            --green-pale: #81C784;
            --green-soft: #C8E6C9;
            --green-white: #E8F5E9;
            --white: #FFFFFF;
            --cream: #F1F8E9;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%);
            min-height: 100vh;
        }
        
        /* Same styles as admin/dashboard.blade.php for navbar and sidebar */
        .navbar {
            background: var(--green-dark);
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.2);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }
        
        .nav-logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .nav-logo img {
            width: 80px;
            height: 60px;
            border-radius: 8px;
            object-fit: contain;
            background: white;
            padding: 3px;
        }
        
        .nav-logo span {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--white);
        }
        
        .dashboard-layout {
            display: flex;
            padding-top: 80px;
        }
        
        .sidebar {
            width: 300px;
            background: var(--white);
            min-height: calc(100vh - 80px);
            padding: 30px 0;
            box-shadow: 2px 0 20px rgba(27, 94, 32, 0.1);
        }
        
        .sidebar-section {
            margin-bottom: 25px;
        }
        
        .sidebar-title {
            font-size: 0.75rem;
            color: var(--green-medium);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 0 25px;
            margin-bottom: 12px;
            font-weight: 600;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 25px;
            color: var(--green-dark);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: var(--green-soft);
            border-left-color: var(--green-primary);
        }
        
        .sidebar-menu li a .icon {
            font-size: 1.3rem;
        }
        
        .sidebar-menu li a .badge {
            margin-left: auto;
            background: var(--danger);
            color: white;
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 0.75rem;
        }
        
        .main-content {
            flex: 1;
            padding: 30px 40px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-header h1 {
            font-size: 2rem;
            color: var(--green-dark);
        }
        
        .search-filter {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .search-input {
            flex: 1;
            max-width: 400px;
            padding: 12px 20px;
            border: 2px solid var(--green-soft);
            border-radius: 10px;
            font-size: 1rem;
            outline: none;
        }
        
        .search-input:focus {
            border-color: var(--green-primary);
        }
        
        .filter-select {
            padding: 12px 20px;
            border: 2px solid var(--green-soft);
            border-radius: 10px;
            font-size: 1rem;
            outline: none;
            background: white;
            cursor: pointer;
        }
        
        .card {
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1);
        }
        
        .card-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--green-soft);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-header h3 {
            font-size: 1.15rem;
            color: var(--green-dark);
            font-weight: 600;
        }
        
        .card-body {
            padding: 0;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--green-primary), var(--green-medium));
            color: var(--white);
        }
        
        .btn-secondary {
            background: var(--green-soft);
            color: var(--green-dark);
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }
        
        .table-container {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid var(--green-soft);
        }
        
        th {
            font-weight: 600;
            color: var(--green-dark);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: var(--cream);
        }
        
        td {
            color: var(--green-medium);
            font-size: 0.9rem;
        }
        
        tr:hover {
            background: var(--green-white);
        }
        
        .user-info-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .user-avatar-small {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--green-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .user-details h4 {
            color: var(--green-dark);
            margin-bottom: 2px;
            font-size: 0.95rem;
        }
        
        .user-details p {
            font-size: 0.8rem;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-badge.active { background: var(--green-soft); color: var(--green-dark); }
        .status-badge.pending { background: #FFF3E0; color: #E65100; }
        .status-badge.inactive { background: #FFEBEE; color: #C62828; }
        
        .role-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .role-badge.client { background: #E3F2FD; color: #1565C0; }
        .role-badge.owner { background: #FFF3E0; color: #E65100; }
        .role-badge.admin { background: var(--green-soft); color: var(--green-dark); }
        
        .action-btns {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .action-btn.view { background: var(--green-soft); color: var(--green-primary); }
        .action-btn.edit { background: #E3F2FD; color: #1976D2; }
        .action-btn.message { background: #FFF3E0; color: #E65100; }
        .action-btn.delete { background: #FFEBEE; color: #C62828; }
        
        .action-btn:hover { transform: scale(1.1); }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 20px;
        }
        
        .pagination button {
            padding: 8px 15px;
            border: 2px solid var(--green-soft);
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .pagination button.active {
            background: var(--green-primary);
            color: white;
            border-color: var(--green-primary);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-logo">
            <img src="/SYSTEMLOGO.jpg" alt="System Logo">
            <span>Admin Panel</span>
        </div>
    </nav>
    
    <!-- Dashboard Layout -->
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-section">
                <h3 class="sidebar-title">Main Menu</h3>
                <ul class="sidebar-menu">
                    <li><a href="{{ route('admin.dashboard') }}"><span class="icon">📊</span> Dashboard</a></li>
                    <li><a href="#" class="active"><span class="icon">👥</span> Users</a></li>
                    <li><a href="#"><span class="icon">🏠</span> Properties</a></li>
                    <li><a href="#"><span class="icon">📅</span> Bookings</a></li>
                    <li><a href="#"><span class="icon">💬</span> Messages</a></li>
                </ul>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="page-header">
                <h1>User Management</h1>
                <button class="btn btn-primary">+ Add New User</button>
            </div>
            
            <div class="search-filter">
                <input type="text" class="search-input" placeholder="Search users by name or email...">
                <select class="filter-select">
                    <option value="">All Roles</option>
                    <option value="client">Clients</option>
                    <option value="owner">Owners</option>
                    <option value="admin">Admins</option>
                </select>
                <select class="filter-select">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>All Users (156)</h3>
                    <button class="btn btn-secondary btn-sm">Export CSV</button>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="user-info-cell">
                                            <div class="user-avatar-small">JM</div>
                                            <div class="user-details">
                                                <h4>Juan Miguel</h4>
                                                <p>juan@email.com</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="role-badge client">Client</span></td>
                                    <td>+63 912 345 6789</td>
                                    <td><span class="status-badge active">Active</span></td>
                                    <td>2 hours ago</td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="action-btn view" title="View">👁️</button>
                                            <button class="action-btn edit" title="Edit">✏️</button>
                                            <button class="action-btn message" title="Message">💬</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="user-info-cell">
                                            <div class="user-avatar-small">SC</div>
                                            <div class="user-details">
                                                <h4>Sarah Chen</h4>
                                                <p>sarah@email.com</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="role-badge owner">Owner</span></td>
                                    <td>+63 923 456 7890</td>
                                    <td><span class="status-badge active">Active</span></td>
                                    <td>1 day ago</td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="action-btn view" title="View">👁️</button>
                                            <button class="action-btn edit" title="Edit">✏️</button>
                                            <button class="action-btn message" title="Message">💬</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="user-info-cell">
                                            <div class="user-avatar-small">RP</div>
                                            <div class="user-details">
                                                <h4>Robert Perez</h4>
                                                <p>robert@email.com</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="role-badge client">Client</span></td>
                                    <td>+63 934 567 8901</td>
                                    <td><span class="status-badge pending">Pending</span></td>
                                    <td>Never</td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="action-btn view" title="View">👁️</button>
                                            <button class="action-btn edit" title="Verify">✓</button>
                                            <button class="action-btn delete" title="Reject">✗</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="user-info-cell">
                                            <div class="user-avatar-small">ML</div>
                                            <div class="user-details">
                                                <h4>Maria Lopez</h4>
                                                <p>maria@email.com</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="role-badge owner">Owner</span></td>
                                    <td>+63 945 678 9012</td>
                                    <td><span class="status-badge active">Active</span></td>
                                    <td>3 days ago</td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="action-btn view" title="View">👁️</button>
                                            <button class="action-btn edit" title="Edit">✏️</button>
                                            <button class="action-btn message" title="Message">💬</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="user-info-cell">
                                            <div class="user-avatar-small">JD</div>
                                            <div class="user-details">
                                                <h4>John Doe</h4>
                                                <p>john@email.com</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="role-badge client">Client</span></td>
                                    <td>+63 956 789 0123</td>
                                    <td><span class="status-badge inactive">Inactive</span></td>
                                    <td>30 days ago</td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="action-btn view" title="View">👁️</button>
                                            <button class="action-btn edit" title="Edit">✏️</button>
                                            <button class="action-btn delete" title="Delete">🗑️</button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="pagination">
                        <button><</button>
                        <button class="active">1</button>
                        <button>2</button>
                        <button>3</button>
                        <button>...</button>
                        <button>16</button>
                        <button>></button>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

