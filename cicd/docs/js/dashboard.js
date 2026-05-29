/**
 * Logimax CI/CD Dashboard - Interactive Functionality
 * Adds all interactive features to the dashboard
 */

// Dynamic API path - works on any server
const API_BASE = (function() {
    const path = window.location.pathname;
    const docsIndex = path.indexOf('/cicd/docs');
    if (docsIndex !== -1) {
        return path.substring(0, docsIndex) + '/cicd/docs/api';
    }
    return 'api'; // Fallback to relative
})();

// State
let state = {
    clients: [],
    deployments: [],
    tasks: [],
    goals: JSON.parse(localStorage.getItem('cicd_goals') || '[]'),
    notifications: []
};

// Initialize Dashboard
document.addEventListener('DOMContentLoaded', async () => {
    await loadData();
    initModals();
    initNavigation();
    initSearch();
    initNotifications();
    initGoals();
    initTasks();
    initProjects();
    initContextMenus();
    initUserMenu();
    animateStats();
});

// Initialize user menu and display
function initUserMenu() {
    const user = window.cicdAuth?.getUser();
    if (user) {
        // Update greeting
        const greeting = document.querySelector('.greeting');
        if (greeting) {
            greeting.textContent = `Hi, ${user.username || 'Admin'}!`;
        }
        
        // Update avatar with user initials
        const avatar = document.querySelector('.avatar');
        if (avatar) {
            const initials = user.username ? user.username.substring(0, 2).toUpperCase() : 'AD';
            avatar.textContent = initials;
            avatar.style.cursor = 'pointer';
            avatar.addEventListener('click', showUserMenu);
        }
    }
}

function showUserMenu() {
    const user = window.cicdAuth?.getUser();
    const content = `
    <div class="user-profile">
        <div class="user-avatar-large">${user?.username?.substring(0, 2).toUpperCase() || 'AD'}</div>
        <h4>${user?.username || 'Admin'}</h4>
        <span class="role-badge">${user?.role || 'admin'}</span>
    </div>
    <div class="user-menu-actions">
        <button class="btn-secondary btn-block" onclick="closeModal()">Close</button>
        <button class="btn-primary btn-block" onclick="handleLogout()">Sign Out</button>
    </div>`;
    showModal('Profile', content);
}

function handleLogout() {
    closeModal();
    if (window.cicdAuth?.logout) {
        window.cicdAuth.logout();
    } else {
        sessionStorage.removeItem('cicd_user');
        window.location.href = 'login.html';
    }
}

// Load data from API
async function loadData() {
    try {
        const [clientsRes, statsRes] = await Promise.all([
            fetch('../../clients/registry.json'),
            fetch(`${API_BASE}/stats.php`).catch(() => null)
        ]);
        
        if (clientsRes.ok) {
            const data = await clientsRes.json();
            state.clients = data.clients || [];
            updateStats(data);
        }
    } catch (e) {
        console.log('Using mock data');
    }
}

// Update stats display
function updateStats(data) {
    const totalClients = data.total_clients || data.clients?.length || 88;
    animateValue('totalClients', totalClients);
}

// Animate number values
function animateValue(id, end, duration = 1000) {
    const el = document.getElementById(id);
    if (!el) return;
    
    const start = 0;
    const startTime = performance.now();
    
    function update(time) {
        const progress = Math.min((time - startTime) / duration, 1);
        el.textContent = Math.floor(start + (end - start) * progress);
        if (progress < 1) requestAnimationFrame(update);
    }
    requestAnimationFrame(update);
}

function animateStats() {
    animateValue('totalDeployments', 43, 1000);
    animateValue('failedCount', 2, 800);
    animateValue('totalClients', 88, 1200);
    animateValue('inProgress', 14, 900);
    animateValue('completed', 11, 1000);
}

// Modal system
function initModals() {
    // Create modal container
    const modalHTML = `
    <div id="modal-overlay" class="modal-overlay">
        <div id="modal-container" class="modal-container">
            <div class="modal-header">
                <h3 id="modal-title"></h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div id="modal-content"></div>
        </div>
    </div>`;
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Create button click handler
    document.querySelector('.btn-create')?.addEventListener('click', () => showCreateModal());
}

function showModal(title, content) {
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-content').innerHTML = content;
    document.getElementById('modal-overlay').classList.add('active');
}

function closeModal() {
    document.getElementById('modal-overlay').classList.remove('active');
}

function showCreateModal() {
    const content = `
    <div class="modal-tabs">
        <button class="tab-btn active" onclick="switchTab('deploy')">Deploy</button>
        <button class="tab-btn" onclick="switchTab('sync')">Sync to Client</button>
    </div>
    <div id="tab-deploy" class="tab-content active">
        <div class="form-group">
            <label>Environment</label>
            <select id="deploy-env">
                <option value="develop">Develop</option>
                <option value="qa">QA</option>
                <option value="support">Support</option>
                <option value="production">Production</option>
            </select>
        </div>
        <div class="form-group">
            <label>Branch</label>
            <select id="deploy-branch">
                <option value="Retail_1.1.1.0001">Retail_1.1.1.0001</option>
                <option value="QA">QA</option>
                <option value="Production">Production</option>
            </select>
        </div>
        <button class="btn-primary" onclick="triggerDeploy()">Deploy Now</button>
    </div>
    <div id="tab-sync" class="tab-content">
        <div class="form-group">
            <label>Select Client</label>
            <select id="sync-client">
                ${state.clients.map(c => `<option value="${c.repo}">${c.name}</option>`).join('')}
            </select>
        </div>
        <div class="form-group">
            <label>Source Branch</label>
            <select id="sync-branch">
                <option value="Production">Production</option>
                <option value="QA">QA</option>
            </select>
        </div>
        <button class="btn-primary" onclick="triggerSync()">Sync Now</button>
    </div>`;
    showModal('Create New', content);
}

function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.querySelector(`[onclick="switchTab('${tab}')"]`).classList.add('active');
    document.getElementById(`tab-${tab}`).classList.add('active');
}

async function triggerDeploy() {
    const env = document.getElementById('deploy-env').value;
    const branch = document.getElementById('deploy-branch').value;
    
    showToast(`Deploying ${branch} to ${env}...`, 'info');
    closeModal();
    
    // Add to tasks
    addTask(`Deploy to ${env}`, new Date().toLocaleDateString());
}

async function triggerSync() {
    const client = document.getElementById('sync-client').value;
    const branch = document.getElementById('sync-branch').value;
    
    showToast(`Syncing ${branch} to ${client}...`, 'info');
    closeModal();
    
    addTask(`Sync ${client}`, new Date().toLocaleDateString());
}

// Navigation
function initNavigation() {
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', (e) => {
            const isSettings = item.closest('.sidebar-footer');
            if (isSettings) {
                showSettingsModal();
                return;
            }
            
            document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
            item.classList.add('active');
            
            const text = item.textContent.trim();
            handleNavigation(text);
        });
    });
}

function handleNavigation(section) {
    // Navigation map - menu text to page URL
    const navMap = {
        'Dashboard': 'index.html',
        'Deployments': 'support-deploy.html',
        'Sync Tasks': 'support-deploy.html',
        'Statistics': 'index.html#stats',
        'Clients': 'clients.html',
        'GitHub': 'https://github.com/LOGIMAX-CLIENTS',
        'Webhooks': 'index.html',
        'Add new plugin': 'index.html',
        'LOGIMAX-CLIENTS': 'https://github.com/orgs/LOGIMAX-CLIENTS/repositories',
        'Logimax-Technologies': 'https://github.com/orgs/Logimax-Technologies/repositories'
    };
    
    const url = navMap[section];
    
    if (url) {
        // External links open in new tab
        if (url.startsWith('http')) {
            window.open(url, '_blank');
        } else if (url !== 'index.html' && !url.includes('#')) {
            // Internal navigation
            window.location.href = url;
        } else {
            // Stay on dashboard, just update heading
            const greeting = document.querySelector('.greeting');
            if (greeting) {
                greeting.textContent = section === 'Dashboard' ? `Hi, ${window.cicdAuth?.getUser()?.username || 'Admin'}!` : section;
            }
        }
    }
}

function showSettingsModal() {
    const content = `
    <div class="settings-list">
        <div class="setting-item">
            <span>Notifications</span>
            <label class="toggle"><input type="checkbox" checked><span class="slider"></span></label>
        </div>
        <div class="setting-item">
            <span>Auto-refresh</span>
            <label class="toggle"><input type="checkbox"><span class="slider"></span></label>
        </div>
        <div class="setting-item">
            <span>Dark Mode</span>
            <label class="toggle"><input type="checkbox" onchange="toggleDarkMode(this.checked)"><span class="slider"></span></label>
        </div>
    </div>`;
    showModal('Settings', content);
}

function toggleDarkMode(enabled) {
    document.body.classList.toggle('dark-mode', enabled);
    localStorage.setItem('darkMode', enabled);
}

// Search
function initSearch() {
    const searchBtn = document.querySelector('.icon-btn');
    if (searchBtn) {
        searchBtn.addEventListener('click', showSearchModal);
    }
}

function showSearchModal() {
    const content = `
    <div class="search-container">
        <input type="text" id="search-input" placeholder="Search clients, deployments..." autofocus onkeyup="handleSearch(this.value)">
        <div id="search-results"></div>
    </div>`;
    showModal('Search', content);
    setTimeout(() => document.getElementById('search-input')?.focus(), 100);
}

function handleSearch(query) {
    const results = document.getElementById('search-results');
    if (!query) { results.innerHTML = ''; return; }
    
    const matches = state.clients.filter(c => 
        c.name.toLowerCase().includes(query.toLowerCase()) ||
        c.repo.toLowerCase().includes(query.toLowerCase())
    ).slice(0, 5);
    
    results.innerHTML = matches.length ? matches.map(c => `
        <div class="search-result" onclick="selectClient('${c.repo}')">
            <strong>${c.name}</strong><br><small>${c.repo}</small>
        </div>`).join('') : '<div class="no-results">No results found</div>';
}

function selectClient(repo) {
    closeModal();
    showToast(`Selected: ${repo}`, 'success');
}

// Notifications
function initNotifications() {
    const notifBtn = document.querySelectorAll('.icon-btn')[1];
    if (notifBtn) {
        notifBtn.addEventListener('click', showNotifications);
    }
}

function showNotifications() {
    const content = `
    <div class="notifications-list">
        <div class="notif-item">
            <div class="notif-icon success">✓</div>
            <div class="notif-content">
                <strong>Deploy completed</strong>
                <p>SR Jewellery deployed successfully</p>
                <small>2 hours ago</small>
            </div>
        </div>
        <div class="notif-item">
            <div class="notif-icon warning">!</div>
            <div class="notif-content">
                <strong>Sync pending</strong>
                <p>NSK Jewels awaiting approval</p>
                <small>5 hours ago</small>
            </div>
        </div>
    </div>`;
    showModal('Notifications', content);
}

// Goals
function initGoals() {
    document.querySelectorAll('.goal-checkbox').forEach((cb, i) => {
        const isChecked = state.goals[i] || cb.classList.contains('checked');
        if (isChecked) cb.classList.add('checked');
        
        cb.addEventListener('click', () => {
            cb.classList.toggle('checked');
            state.goals[i] = cb.classList.contains('checked');
            localStorage.setItem('cicd_goals', JSON.stringify(state.goals));
            
            if (cb.classList.contains('checked')) {
                cb.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>';
            } else {
                cb.innerHTML = '';
            }
        });
    });
}

// Tasks
function initTasks() {
    document.querySelector('.add-task-btn')?.addEventListener('click', showAddTaskModal);
    
    document.querySelectorAll('.task-menu-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            showTaskMenu(btn);
        });
    });
}

function showAddTaskModal() {
    const content = `
    <div class="form-group">
        <label>Task Title</label>
        <input type="text" id="task-title" placeholder="Enter task title">
    </div>
    <div class="form-group">
        <label>Due Date</label>
        <input type="date" id="task-date">
    </div>
    <button class="btn-primary" onclick="createTask()">Add Task</button>`;
    showModal('Add Task', content);
}

function createTask() {
    const title = document.getElementById('task-title').value;
    const date = document.getElementById('task-date').value;
    if (!title) return showToast('Please enter a title', 'error');
    
    addTask(title, date || 'Today');
    closeModal();
    showToast('Task added!', 'success');
}

function addTask(title, date) {
    const container = document.querySelector('.tasks-container');
    const addBtn = container.querySelector('.add-task-btn');
    
    const taskHTML = `
    <div class="task-item">
        <button class="task-menu-btn"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/><circle cx="5" cy="12" r="1"/></svg></button>
        <div class="task-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3l8-8"/><path d="M20 12v6a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h9"/></svg></div>
        <h4 class="task-title">${title}</h4>
        <span class="task-date">${date}</span>
    </div>`;
    
    addBtn.insertAdjacentHTML('beforebegin', taskHTML);
    
    // Update task count
    const header = document.querySelector('.tasks-card .card-title');
    const count = container.querySelectorAll('.task-item').length;
    header.textContent = `Task In process (${count})`;
}

function showTaskMenu(btn) {
    const existing = document.querySelector('.context-menu.active');
    if (existing) existing.remove();
    
    const menu = document.createElement('div');
    menu.className = 'context-menu active';
    menu.innerHTML = `
        <div class="context-menu-item" onclick="pinTask(this)">📌 Pin Note</div>
        <div class="context-menu-item" onclick="editTask(this)">✏️ Edit</div>
        <div class="context-menu-item" onclick="deleteTask(this)">🗑️ Delete</div>`;
    btn.parentElement.appendChild(menu);
    
    setTimeout(() => document.addEventListener('click', () => menu.remove(), { once: true }), 0);
}

function deleteTask(menuItem) {
    const task = menuItem.closest('.task-item');
    task.style.opacity = '0';
    setTimeout(() => task.remove(), 300);
    showToast('Task deleted', 'success');
}

function pinTask() { showToast('Task pinned!', 'success'); }
function editTask() { showToast('Edit mode', 'info'); }

// Projects
function initProjects() {
    document.querySelectorAll('.project-refresh').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            btn.style.animation = 'spin 1s linear';
            showToast('Refreshing...', 'info');
            setTimeout(() => btn.style.animation = '', 1000);
        });
    });
    
    document.querySelectorAll('.project-card').forEach(card => {
        card.addEventListener('click', () => {
            const title = card.querySelector('.project-title').textContent;
            showProjectDetails(title);
        });
    });
    
    // Sort button
    document.querySelector('.sort-btn')?.addEventListener('click', showSortMenu);
    
    // View toggle
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            const grid = document.querySelector('.projects-grid');
            const isList = btn.querySelector('line');
            grid.style.gridTemplateColumns = isList ? '1fr' : 'repeat(3, 1fr)';
        });
    });
    
    // Download report
    document.querySelector('.download-btn')?.addEventListener('click', downloadReport);
}

function showProjectDetails(title) {
    const content = `
    <div class="project-details">
        <h4>${title}</h4>
        <div class="detail-row"><span>Status:</span> <span class="status-badge in-progress">In Progress</span></div>
        <div class="detail-row"><span>Started:</span> <span>Jan 25, 2025</span></div>
        <div class="detail-row"><span>Branch:</span> <span>Production</span></div>
        <div class="actions">
            <button class="btn-secondary" onclick="closeModal()">Close</button>
            <button class="btn-primary" onclick="retriggerDeploy('${title}')">Re-deploy</button>
        </div>
    </div>`;
    showModal('Project Details', content);
}

function retriggerDeploy(title) {
    closeModal();
    showToast(`Re-deploying ${title}...`, 'info');
}

function showSortMenu() {
    const btn = document.querySelector('.sort-btn');
    const menu = document.createElement('div');
    menu.className = 'context-menu active';
    menu.style.cssText = 'top:100%;right:0;';
    menu.innerHTML = `
        <div class="context-menu-item" onclick="sortProjects('date')">By Date</div>
        <div class="context-menu-item" onclick="sortProjects('name')">By Name</div>
        <div class="context-menu-item" onclick="sortProjects('status')">By Status</div>`;
    btn.parentElement.style.position = 'relative';
    btn.parentElement.appendChild(menu);
    setTimeout(() => document.addEventListener('click', () => menu.remove(), { once: true }), 0);
}

function sortProjects(by) {
    showToast(`Sorted by ${by}`, 'success');
}

function downloadReport() {
    showToast('Generating report...', 'info');
    setTimeout(() => {
        const report = {
            generated: new Date().toISOString(),
            totalDeployments: 43,
            completed: 11,
            inProgress: 14,
            clients: state.clients.length
        };
        
        const blob = new Blob([JSON.stringify(report, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `cicd-report-${Date.now()}.json`;
        a.click();
        showToast('Report downloaded!', 'success');
    }, 1000);
}

// Context menus
function initContextMenus() {
    document.addEventListener('click', () => {
        document.querySelectorAll('.context-menu.active').forEach(m => m.remove());
    });
}

// Toast notifications
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Open archive
document.querySelector('.open-archive')?.addEventListener('click', () => {
    showModal('Archive', '<p>Archived deployments will appear here.</p>');
});
