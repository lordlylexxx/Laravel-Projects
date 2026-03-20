<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Management - Admin Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --green-dark: #1B5E20; --green-primary: #2E7D32; --green-medium: #43A047;
            --green-light: #66BB6A; --green-pale: #81C784; --green-soft: #C8E6C9;
            --green-white: #E8F5E9; --white: #FFFFFF; --cream: #F1F8E9;
            --gray-600: #4B5563;
        }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, var(--green-white) 0%, var(--cream) 50%, var(--green-soft) 100%); min-height: 100vh; }
        .navbar { background: var(--white); padding: 0 40px; height: 70px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 20px rgba(27, 94, 32, 0.1); position: fixed; width: 100%; top: 0; left: 0; right: 0; z-index: 1000; }
        .nav-logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .nav-logo img { width: 45px; height: 45px; border-radius: 0; border: none; object-fit: contain; }
        .nav-logo span { font-size: 1.3rem; font-weight: 700; color: var(--green-dark); }
        .nav-links { display: flex; gap: 8px; list-style: none; }
        .nav-links a { text-decoration: none; color: var(--gray-600); font-weight: 500; padding: 10px 16px; border-radius: 8px; transition: all 0.3s; display: flex; align-items: center; gap: 8px; }
        .nav-links a:hover, .nav-links a.active { background: linear-gradient(135deg, var(--green-primary), var(--green-medium)); color: var(--white); box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3); }
        .nav-actions { display: flex; gap: 15px; align-items: center; }
        .user-display { display: flex; align-items: center; gap: 12px; padding: 8px 16px; background: linear-gradient(135deg, var(--green-soft), var(--green-white)); border-radius: 10px; border: 1px solid var(--green-soft); }
        .user-avatar { width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(135deg, var(--green-dark), var(--green-primary)); color: var(--white); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; }
        .user-info { text-align: left; }
        .user-name { font-weight: 700; color: var(--green-dark); font-size: 0.95rem; line-height: 1.2; }
        .user-role { font-size: 0.75rem; color: var(--green-medium); text-transform: uppercase; letter-spacing: 0.5px; }
        .nav-btn { padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; transition: all 0.3s; cursor: pointer; border: none; display: flex; align-items: center; gap: 8px; }
        .nav-btn.primary { background: linear-gradient(135deg, var(--green-dark), var(--green-primary)); color: var(--white); }
        .nav-btn.primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4); }
        .dashboard-layout { display: flex; padding-top: 80px; }
        .main-content { flex: 1; padding: 30px 40px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h1 { font-size: 2rem; color: var(--green-dark); }
        .search-filter { display: flex; gap: 15px; margin-bottom: 25px; }
        .search-input { flex: 1; max-width: 400px; padding: 12px 20px; border: 2px solid var(--green-soft); border-radius: 10px; font-size: 1rem; outline: none; }
        .search-input:focus { border-color: var(--green-primary); }
        .filter-select { padding: 12px 20px; border: 2px solid var(--green-soft); border-radius: 10px; font-size: 1rem; background: white; cursor: pointer; }
        .card { background: var(--white); border-radius: 15px; box-shadow: 0 8px 30px rgba(27, 94, 32, 0.1); }
        .card-header { padding: 20px 25px; border-bottom: 1px solid var(--green-soft); display: flex; justify-content: space-between; align-items: center; }
        .card-header h3 { font-size: 1.15rem; color: var(--green-dark); font-weight: 600; }
        .btn { padding: 10px 20px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary { background: linear-gradient(135deg, var(--green-primary), var(--green-medium)); color: var(--white); }
        .btn-secondary { background: var(--green-soft); color: var(--green-dark); }
        .btn-sm { padding: 6px 12px; font-size: 0.8rem; }
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid var(--green-soft); }
        th { font-weight: 600; color: var(--green-dark); font-size: 0.8rem; text-transform: uppercase; background: var(--cream); }
        tr:hover { background: var(--green-white); }
        .property-info { display: flex; align-items: center; gap: 15px; }
        .property-thumb { width: 60px; height: 60px; border-radius: 10px; object-fit: cover; }
        .status-badge { display: inline-block; padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 600; }
        .status-badge.pending { background: #FFF3E0; color: #E65100; }
        .status-badge.confirmed { background: var(--green-soft); color: var(--green-dark); }
        .status-badge.cancelled { background: #FFEBEE; color: #C62828; }
        .status-badge.completed { background: #E3F2FD; color: #1565C0; }
        .status-badge.paid { background: #E9; color: #2E78F5ED32; }
        @media (max-width: 768px) {
            .navbar { padding: 0 20px; height: 60px; }
            .nav-links { display: none; }
            .main-content { padding: 20px; }
        }

        @include('admin.partials.top-navbar-styles')
    </style>
</head>
<body>
    @include('admin.partials.top-navbar', ['active' => 'bookings'])
    
    <div class="dashboard-layout">
        <main class="main-content">
            <div class="page-header">
                <h1>Bookings Management</h1>
            </div>
            
            <div class="search-filter">
                <input type="text" class="search-input" placeholder="Search bookings...">
                <select class="filter-select">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="completed">Completed</option>
                </select>
                <select class="filter-select">
                    <option value="">This Week</option>
                    <option value="">This Month</option>
                    <option value="">This Year</option>
                </select>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>All Bookings (892)</h3>
                    <button class="btn btn-secondary btn-sm">Export Report</button>
                </div>
                <div class="card-body">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Property</th>
                                    <th>Guest</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="property-info">
                                            <img src="/COMMUNAL.jpg" alt="Property" class="property-thumb">
                                            <div><strong>Mountain View Inn</strong><br><small>Traveller-Inn</small></div>
                                        </div>
                                    </td>
                                    <td>Juan Miguel<br><small>juan@email.com</small></td>
                                    <td>Dec 15, 2024</td>
                                    <td>Dec 18, 2024</td>
                                    <td>₱4,500</td>
                                    <td><span class="status-badge pending">Pending</span></td>
                                    <td>Dec 10, 2024</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="property-info">
                                            <img src="/1.jpg" alt="Property" class="property-thumb">
                                            <div><strong>Cozy Garden House</strong><br><small>Airbnb</small></div>
                                        </div>
                                    </td>
                                    <td>Sarah Chen<br><small>sarah@email.com</small></td>
                                    <td>Dec 20, 2024</td>
                                    <td>Dec 25, 2024</td>
                                    <td>₱16,800</td>
                                    <td><span class="status-badge confirmed">Confirmed</span></td>
                                    <td>Dec 8, 2024</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="property-info">
                                            <img src="/2.jpg" alt="Property" class="property-thumb">
                                            <div><strong>Riverside Apartment</strong><br><small>Daily Rental</small></div>
                                        </div>
                                    </td>
                                    <td>Robert Perez<br><small>robert@email.com</small></td>
                                    <td>Dec 10, 2024</td>
                                    <td>Dec 12, 2024</td>
                                    <td>₱2,400</td>
                                    <td><span class="status-badge completed">Completed</span></td>
                                    <td>Dec 5, 2024</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="property-info">
                                            <img src="/airbnb1.jpg" alt="Property" class="property-thumb">
                                            <div><strong>Forest Cabin</strong><br><small>Airbnb</small></div>
                                        </div>
                                    </td>
                                    <td>Maria Lopez<br><small>maria@email.com</small></td>
                                    <td>Dec 22, 2024</td>
                                    <td>Dec 28, 2024</td>
                                    <td>₱24,500</td>
                                    <td><span class="status-badge paid">Paid</span></td>
                                    <td>Dec 9, 2024</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="property-info">
                                            <img src="/inn1.jpg" alt="Property" class="property-thumb">
                                            <div><strong>Town Inn</strong><br><small>Traveller-Inn</small></div>
                                        </div>
                                    </td>
                                    <td>John Doe<br><small>john@email.com</small></td>
                                    <td>Dec 12, 2024</td>
                                    <td>Dec 14, 2024</td>
                                    <td>₱2,400</td>
                                    <td><span class="status-badge cancelled">Cancelled</span></td>
                                    <td>Dec 6, 2024</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>

