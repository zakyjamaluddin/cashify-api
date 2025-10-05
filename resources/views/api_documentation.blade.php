<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cashify API Documentation</title>
    <style>
        html { scroll-behavior: smooth; }
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, 'Helvetica Neue', Arial, 'Noto Sans', 'Apple Color Emoji', 'Segoe UI Emoji'; margin: 2rem; line-height: 1.5; color: #0f172a; }
        code, pre { background: #0b1220; color: #e5e7eb; padding: 0.5rem 0.75rem; border-radius: 6px; overflow-x: auto; display: block; }
        h1, h2, h3 { color: #111827; }
        .text-white { color: #ffffff; }
        .tag { display: inline-block; font-size: 0.8rem; padding: 0.15rem 0.5rem; border-radius: 999px; background: #e5e7eb; color: #111827; margin-right: .5rem; }
        .method { font-weight: 700; }
        .get { background:#dbeafe; color:#1e40af; }
        .post { background:#dcfce7; color:#166534; }
        .put { background:#fef9c3; color:#854d0e; }
        .delete { background:#fee2e2; color:#991b1b; }
        .group { margin: 2rem 0; }
        .endpoint { margin: 1.25rem 0; }
        .url { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', monospace; }
        .note { color:#475569; font-size:.95rem; }
        .layout { display: flex; gap: 2rem; }
        .sidebar { background: linear-gradient(180deg,rgb(240, 240, 240) 10%,rgb(194, 194, 194) 100%); color: #ffffff; border-radius: 8px; padding: 1rem; height: 100vh; min-height: 100vh; overflow:auto; box-shadow: 0 2px 8px rgba(2,8,23,.08); }
        .sidebar .brand { display:flex; align-items:center; gap:.5rem; padding: .5rem .5rem 1rem .5rem; font-weight: 700; letter-spacing:.3px; border-bottom: 1px solid rgba(255,255,255,.15); }
        .sidebar .brand .dot { width:10px; height:10px; border-radius: 999px; background:#fff; display:inline-block; }
        .sidebar .nav { list-style: none; margin: 0; padding: .75rem 0 0 0; }
        .sidebar .nav-item { margin: .15rem 0; }
        .sidebar .nav-link { display:flex; align-items:center; gap:.5rem; color:#ffffff; text-decoration:none; padding:.65rem 1rem; border-left: .25rem solid transparent; opacity:.95; }
        .sidebar .nav-link:hover { background: rgba(255,255,255,.08); opacity:1; }
        .sidebar .nav-link.active { border-left-color: #ffffff; background: rgba(255,255,255,.12); }
        .sidebar .icon { width: 1rem; height: 1rem; display:inline-block; }
        .content { flex: 1; min-width: 0; }
        .sidebar-toggle { display:none; }

        .nav a {
            display: block;
            width: 100%;
            padding: 0.5rem 0;
            color: inherit;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.15s;
        }
        .nav a:hover, .nav a.active {
            background: rgba(255,255,255,0.10);
        }

        @media (min-width:1024px) {
            .sidebar { position: sticky; top: 1rem; width: 260px; }
        }

        @media (max-width:1023px) {
            .layout { position: relative; }
            .sidebar { position: fixed; top:0; left:0; bottom:0; width: 80%; max-width: 280px; transform: translateX(-100%); transition: transform .2s ease; z-index: 50; }
            .sidebar.open { transform: translateX(0); }
            .sidebar-toggle { display: inline-block; position: fixed; top: .75rem; left: .75rem; z-index: 60; background:#111827; color:#fff; border:0; border-radius: 6px; padding:.5rem .75rem; }
        }
    </style>
    <meta name="robots" content="noindex" />
    <meta name="turbolinks-cache-control" content="no-cache" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="doc-generated-at" content="{{ now()->toIso8601String() }}" />
    <meta name="doc-version" content="v1" />
    <meta name="doc-app" content="Cashify API" />
    <link rel="icon" href="/favicon.ico" />
    <script>
        function curl(host, method, path, body, token) {
            const h = token ? ` -H "Authorization: Bearer ${token}"` : '';
            const d = body ? ` -H "Content-Type: application/json" -d '${JSON.stringify(body)}'` : '';
            return `curl -X ${method} ${h}${d} ${host}${path}`.trim();
        }
        function toggleSidebar() {
            var el = document.getElementById('sidebar');
            if (el) { el.classList.toggle('open'); }
        }
        function closeSidebarOnMobile() {
            var el = document.getElementById('sidebar');
            if (el && window.innerWidth < 1024) { el.classList.remove('open'); }
        }
        document.addEventListener('DOMContentLoaded', function(){
            var links = document.querySelectorAll('#sidebar a[href^="#"]');
            links.forEach(function(a){
                a.addEventListener('click', function(){
                    // allow default anchor jump (smooth via CSS), then close on mobile
                    setTimeout(closeSidebarOnMobile, 50);
                });
            });
            // Optional: mark active link on scroll
            var sections = ['section-auth','section-wallets','section-categories','section-transactions'];
            var navMap = {};
            links.forEach(function(a){ navMap[a.getAttribute('href').slice(1)] = a; });
            var observer = new IntersectionObserver(function(entries){
                entries.forEach(function(e){
                    if (e.isIntersecting) {
                        Object.values(navMap).forEach(function(l){ l.classList.remove('active'); });
                        var id = e.target.getAttribute('id');
                        if (navMap[id]) { navMap[id].classList.add('active'); }
                    }
                });
            }, { rootMargin: '-30% 0px -60% 0px', threshold: [0, 1] });
            sections.forEach(function(id){ var el = document.getElementById(id); if (el) observer.observe(el); });
        });
    </script>
    <noscript></noscript>
    <!-- Static HTML only; no runtime JS dependency -->
    <!-- This page intentionally avoids build steps for easy hosting in Laravel -->
</head>
<body>
<button class="sidebar-toggle" onclick="toggleSidebar()">Menu</button>

<div class="layout">
    <aside class="sidebar" id="sidebar">
        <h2>Daftar Isi</h2>
        <hr>
        <nav>
            <a style="color: black; text-decoration: none; display: block; padding: 0.5rem 0;" href="#section-auth">Authentication</a>
            <a style="color: black; text-decoration: none; display: block; padding: 0.5rem 0;" href="#section-wallets">Wallets</a>
            <a style="color: black; text-decoration: none; display: block; padding: 0.5rem 0;" href="#section-categories">Categories</a>
            <a style="color: black; text-decoration: none; display: block; padding: 0.5rem 0;" href="#section-transactions">Transactions</a>
        </nav>
    </aside>
    <main class="content">
        <h1>Cashify API Documentation</h1>

        <p class="note">Semua endpoint (kecuali Register & Login) membutuhkan Authorization Bearer Token (Sanctum Personal Access Token).</p>

        <div class="group" id="section-auth">
            <h2>Authentication</h2>

    <div class="endpoint">
        <span class="tag post method">POST</span>
        <span class="url">/api/register</span>
        <p>Register user baru.</p>
        <strong>Request</strong>
        <pre>{
  "display_name": "Budi",
  "email": "budi@example.com",
  "password": "secret123"
}</pre>
        <strong>Response 201</strong>
        <pre>{
  "user": {
    "id": "9e9f1a7e-6b1c-47da-8796-6d7d6b0b1a01",
    "email": "budi@example.com",
    "display_name": "Budi",
    "subscription_status": "Free",
    "is_email_verified": false,
    "active_wallet_id": null,
    "created_at": "2025-10-05T10:00:00.000000Z",
    "updated_at": "2025-10-05T10:00:00.000000Z"
  },
  "token": "PASTE_YOUR_TOKEN_HERE"
}</pre>
        <strong>cURL</strong>
        <pre>curl -X POST -H "Content-Type: application/json" -d '{"display_name":"Budi","email":"budi@example.com","password":"secret123"}' {{ url('/api/register') }}</pre>
    </div>

    <div class="endpoint">
        <span class="tag post method">POST</span>
        <span class="url">/api/login</span>
        <p>Login dan mendapatkan token.</p>
        <strong>Request</strong>
        <pre>{
  "email": "budi@example.com",
  "password": "secret123"
}</pre>
        <strong>Response 200</strong>
        <pre>{
  "user": { /* sama seperti di atas */ },
  "token": "PASTE_YOUR_TOKEN_HERE"
}</pre>
        <strong>cURL</strong>
        <pre>curl -X POST -H "Content-Type: application/json" -d '{"email":"budi@example.com","password":"secret123"}' {{ url('/api/login') }}</pre>
    </div>

    <div class="endpoint">
        <span class="tag post method">POST</span>
        <span class="url">/api/logout</span>
        <p>Menghapus semua token aktif user saat ini.</p>
        <strong>Headers</strong>
        <pre>Authorization: Bearer YOUR_TOKEN</pre>
        <strong>Response 200</strong>
        <pre>{
  "message": "Logout berhasil"
}</pre>
        <strong>cURL</strong>
        <pre>curl -X POST -H "Authorization: Bearer YOUR_TOKEN" {{ url('/api/logout') }}</pre>
    </div>
        </div>

        <div class="group" id="section-wallets">
            <h2>Wallets</h2>

    <div class="endpoint">
        <span class="tag get method get">GET</span>
        <span class="url">/api/wallets</span>
        <p>List dompet milik/diikuti user.</p>
        <strong>Headers</strong>
        <pre>Authorization: Bearer YOUR_TOKEN</pre>
        <strong>Response 200</strong>
        <pre>[
  {
    "id": "...",
    "name": "Dompet Utama",
    "current_balance": "0.00",
    "privacy": "Private",
    "admin_id": "...",
    "member_count": 1,
    "created_at": "...",
    "updated_at": "...",
    "members_count": 1
  }
]</pre>
        <strong>cURL</strong>
        <pre>curl -H "Authorization: Bearer YOUR_TOKEN" {{ url('/api/wallets') }}</pre>
    </div>

    <div class="endpoint">
        <span class="tag post method post">POST</span>
        <span class="url">/api/wallets</span>
        <p>Buat dompet baru.</p>
        <strong>Headers</strong>
        <pre>Authorization: Bearer YOUR_TOKEN
Content-Type: application/json</pre>
        <strong>Request</strong>
        <pre>{
  "name": "Dompet Utama",
  "privacy": "Private"
}</pre>
        <strong>Response 201</strong>
        <pre>{
  "id": "...",
  "name": "Dompet Utama",
  "current_balance": "0.00",
  "privacy": "Private",
  "admin_id": "...",
  "member_count": 1,
  "created_at": "...",
  "updated_at": "..."
}</pre>
        <strong>cURL</strong>
        <pre>curl -X POST -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"name":"Dompet Utama","privacy":"Private"}' {{ url('/api/wallets') }}</pre>
    </div>

    <div class="endpoint">
        <span class="tag put method put">PUT</span>
        <span class="url">/api/wallets/{wallet}</span>
        <p>Update dompet.</p>
        <strong>Request</strong>
        <pre>{
  "name": "Dompet Ganti Nama"
}</pre>
        <strong>cURL</strong>
        <pre>curl -X PUT -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"name":"Dompet Ganti Nama"}' {{ url('/api/wallets/REPLACE_ID') }}</pre>
    </div>

    <div class="endpoint">
        <span class="tag delete method delete">DELETE</span>
        <span class="url">/api/wallets/{wallet}</span>
        <p>Hapus dompet.</p>
        <strong>cURL</strong>
        <pre>curl -X DELETE -H "Authorization: Bearer YOUR_TOKEN" {{ url('/api/wallets/REPLACE_ID') }}</pre>
    </div>
        </div>

        <div class="group" id="section-categories">
            <h2>Categories</h2>
    <div class="endpoint">
        <span class="tag get method get">GET</span>
        <span class="url">/api/categories</span>
        <p>Daftar kategori (default + milik user).</p>
        <strong>cURL</strong>
        <pre>curl -H "Authorization: Bearer YOUR_TOKEN" {{ url('/api/categories') }}</pre>
    </div>

    <div class="endpoint">
        <span class="tag post method post">POST</span>
        <span class="url">/api/categories</span>
        <p>Buat kategori milik user.</p>
        <strong>Request</strong>
        <pre>{
  "name": "Gadget",
  "type": "Expense",
  "icon": "device-phone",
  "color": "#3B82F6"
}</pre>
        <strong>Response 201</strong>
        <pre>{
  "id": "...",
  "name": "Gadget",
  "type": "Expense",
  "icon": "device-phone",
  "color": "#3B82F6",
  "user_id": "...",
  "is_default": false,
  "created_at": "..."
}</pre>
        <strong>cURL</strong>
        <pre>curl -X POST -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"name":"Gadget","type":"Expense","icon":"device-phone","color":"#3B82F6"}' {{ url('/api/categories') }}</pre>
    </div>

    <div class="endpoint">
        <span class="tag put method put">PUT</span>
        <span class="url">/api/categories/{category}</span>
        <strong>cURL</strong>
        <pre>curl -X PUT -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"name":"Hobi"}' {{ url('/api/categories/REPLACE_ID') }}</pre>
    </div>

    <div class="endpoint">
        <span class="tag delete method delete">DELETE</span>
        <span class="url">/api/categories/{category}</span>
        <strong>cURL</strong>
        <pre>curl -X DELETE -H "Authorization: Bearer YOUR_TOKEN" {{ url('/api/categories/REPLACE_ID') }}</pre>
    </div>
        </div>

        <div class="group" id="section-transactions">
            <h2>Transactions</h2>
    <div class="endpoint">
        <span class="tag get method get">GET</span>
        <span class="url">/api/transactions?wallet_id=&type=&from=&to=</span>
        <p>List transaksi dengan filter.</p>
        <strong>cURL</strong>
        <pre>curl -H "Authorization: Bearer YOUR_TOKEN" "{{ url('/api/transactions') }}?wallet_id=REPLACE_WALLET_ID&type=Expense&from=2025-10-01&to=2025-10-05"</pre>
    </div>

    <div class="endpoint">
        <span class="tag post method post">POST</span>
        <span class="url">/api/transactions</span>
        <p>Buat transaksi baru.</p>
        <strong>Request</strong>
        <pre>{
  "wallet_id": "REPLACE_WALLET_ID",
  "type": "Expense",
  "category_id": "REPLACE_CATEGORY_ID",
  "amount": 125000,
  "description": "Beli headset",
  "date": "2025-10-05T12:00:00Z"
}</pre>
        <strong>Response 201</strong>
        <pre>{
  "id": "...",
  "wallet_id": "REPLACE_WALLET_ID",
  "type": "Expense",
  "category_id": "REPLACE_CATEGORY_ID",
  "amount": "125000.00",
  "description": "Beli headset",
  "date": "2025-10-05T12:00:00.000000Z",
  "proof_url": null,
  "recorded_by": "...",
  "is_recurring": false,
  "recurring_schedule_id": null,
  "created_at": "...",
  "updated_at": "..."
}</pre>
        <strong>cURL</strong>
        <pre>curl -X POST -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"wallet_id":"REPLACE_WALLET_ID","type":"Expense","category_id":"REPLACE_CATEGORY_ID","amount":125000,"description":"Beli headset","date":"2025-10-05T12:00:00Z"}' {{ url('/api/transactions') }}</pre>
    </div>

    <div class="endpoint">
        <span class="tag put method put">PUT</span>
        <span class="url">/api/transactions/{transaction}</span>
        <strong>cURL</strong>
        <pre>curl -X PUT -H "Authorization: Bearer YOUR_TOKEN" -H "Content-Type: application/json" -d '{"description":"Beli headset (diskon)"}' {{ url('/api/transactions/REPLACE_ID') }}</pre>
    </div>

    <div class="endpoint">
        <span class="tag delete method delete">DELETE</span>
        <span class="url">/api/transactions/{transaction}</span>
        <strong>cURL</strong>
        <pre>curl -X DELETE -H "Authorization: Bearer YOUR_TOKEN" {{ url('/api/transactions/REPLACE_ID') }}</pre>
    </div>
        </div>

        <p class="note">Tips: Jika perubahan tidak terlihat di UI build, jalankan <code>composer run dev</code> atau <code>npm run dev</code> sesuai kebutuhan frontend.</p>
    </main>
</div>

</body>
</html>



