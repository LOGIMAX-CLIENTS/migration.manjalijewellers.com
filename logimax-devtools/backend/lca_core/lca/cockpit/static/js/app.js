/**
 * LCA Cockpit Application Logic
 */

const App = {
  state: {
    currentView: "dashboard",
    theme: localStorage.getItem("theme") || "light",
    density: localStorage.getItem("density") || "medium",
    network: null, // Vis.js instance
  },

  init() {
    this.applyTheme();
    this.applyDensity();
    this.bindEvents();
    this.loadView("dashboard");
  },

  bindEvents() {
    // Navigation
    document.querySelectorAll(".nav-link").forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault();
        const view = e.currentTarget.dataset.view;
        this.loadView(view);
      });
    });

    // Search Input
    const searchInput = document.getElementById("global-search");
    if (searchInput) {
      searchInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
          this.performSearch(e.target.value);
        }
      });
    }

        // Search Input specific to "Search & Impact" page
        const pageSearchInput = document.getElementById('page-search');
        const pageSearchBtn = document.getElementById('page-search-btn');

        if (pageSearchInput) {
            pageSearchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.performSearch(e.target.value);
            });
        }
        if (pageSearchBtn) {
            pageSearchBtn.addEventListener('click', () => {
                if (pageSearchInput) this.performSearch(pageSearchInput.value);
            });
        }
        
        // Toggles
    document
      .getElementById("toggle-theme")
      .addEventListener("click", () => this.toggleTheme());
    document
      .getElementById("toggle-density")
      .addEventListener("click", () => this.toggleDensity());
  },

  loadView(viewName) {
    // UI Update
    document
      .querySelectorAll(".nav-link")
      .forEach((el) => el.classList.remove("active"));
    document
      .querySelector(`[data-view="${viewName}"]`)
      ?.classList.add("active");

    document
      .querySelectorAll(".view-section")
      .forEach((el) => el.classList.remove("active"));
    document.getElementById(`view-${viewName}`).classList.add("active");

    document.getElementById("page-title").textContent =
      viewName.charAt(0).toUpperCase() + viewName.slice(1);

    // Logic
    if (viewName === "modules") this.loadModulesGraph();
    if (viewName === "dashboard") this.loadDashboardStats();
  },

  // --- API Calls ---

  async performSearch(query) {
    if (!query) return;
    this.loadView("search");
    const container = document.getElementById("search-results");
    container.innerHTML = '<div class="loader">Searching...</div>';

    try {
      const res = await fetch(`/api/search?q=${encodeURIComponent(query)}`);
      const data = await res.json();
      this.renderSearchResults(data.results);
    } catch (e) {
      container.innerHTML = `<div class="error">Error: ${e.message}</div>`;
    }
  },

  async loadModulesGraph() {
    const container = document.getElementById("graph-container");
    if (!container) return; // Not in view or already loaded?

    // Check if already init
    if (this.state.network) {
      // Refit or reload?
      // this.state.network.fit();
      return;
    }

    try {
      const res = await fetch("/api/modules");
      const data = await res.json();
      this.renderVisGraph(container, data);
    } catch (e) {
      container.innerHTML = `Error loading modules: ${e.message}`;
    }
  },

  async loadImpact(functionName, viewMode = 'network') {
    this.loadView("impact");
    this._currentImpactFunction = functionName; // Store for drill-down
    this._currentViewMode = viewMode;
    const container = document.getElementById("impact-graph");
    container.innerHTML = "<div class='loader'>Analyzing dependencies...</div>";

    try {
      const res = await fetch(
        `/api/impact?function_name=${encodeURIComponent(functionName)}&depth=2`,
      );
      const data = await res.json();
      this._lastImpactData = data; // Store for view switching
      
      // Build Stats Bar and Risk Badge HTML
      const riskColors = {
        HIGH: "background: #fee2e2; color: #b91c1c; border-color: #f87171;",
        MEDIUM: "background: #fef9c3; color: #a16207; border-color: #facc15;",
        LOW: "background: #dcfce7; color: #166534; border-color: #4ade80;"
      };
      
      const statsHtml = `
        <div style="display: flex; gap: 1rem; margin-bottom: 1rem; flex-wrap: wrap; align-items: center;">
          <h3 style="margin: 0; font-family: monospace; color: var(--primary);">${functionName}</h3>
          <span class="badge" style="${riskColors[data.riskLevel]}; padding: 0.25rem 0.75rem; border-radius: 999px; font-weight: 600; font-size: 0.8em;">
            ${data.riskLevel} RISK
          </span>
          <div style="display: flex; gap: 0.5rem;">
            <div class="stat-card" style="background: var(--surface-elevated); padding: 0.5rem 1rem; border-radius: 8px; text-align: center;">
              <div style="font-size: 1.5rem; font-weight: 700; color: #9333ea;">${data.stats.upstream}</div>
              <div style="font-size: 0.75rem; color: var(--text-secondary);">← Called By</div>
            </div>
            <div class="stat-card" style="background: var(--surface-elevated); padding: 0.5rem 1rem; border-radius: 8px; text-align: center;">
              <div style="font-size: 1.5rem; font-weight: 700; color: #2563eb;">${data.stats.downstream}</div>
              <div style="font-size: 0.75rem; color: var(--text-secondary);">Calls →</div>
            </div>
          </div>
          ${data.definition ? `<div style="font-size: 0.8em; color: var(--text-secondary);">📍 ${data.definition.file}:${data.definition.line}</div>` : ''}
          <!-- View Toggle -->
          <div style="margin-left: auto; display: flex; gap: 0.5rem;">
            <button class="btn btn-sm ${viewMode === 'network' ? 'btn-primary' : ''}" onclick="App.switchImpactView('network')" title="Interactive Network Graph">
              🕸️ Network
            </button>
            <button class="btn btn-sm ${viewMode === 'mindmap' ? 'btn-primary' : ''}" onclick="App.switchImpactView('mindmap')" title="Mind Map Flowchart">
              🧠 Mind Map
            </button>
          </div>
        </div>
      `;
      
      // Create container for graph + lists
      const listsHtml = `
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
          <!-- Upstream List -->
          <div style="background: linear-gradient(135deg, #f3e8ff 0%, #faf5ff 100%); border: 1px solid #c084fc; border-radius: 8px; padding: 1rem;">
            <h4 style="margin: 0 0 0.5rem; color: #7c3aed; font-size: 0.9rem;">🔼 Called By (Upstream)</h4>
            ${data.upstreamList && data.upstreamList.length > 0 ? `
              <ul style="list-style: none; padding: 0; margin: 0; max-height: 150px; overflow-y: auto;">
                ${data.upstreamList.map(item => `
                  <li style="font-family: monospace; font-size: 0.85rem; padding: 0.25rem 0.5rem; cursor: pointer; border-radius: 4px; color: #6b21a8;" 
                      class="clickable-func" 
                      onclick="App.loadImpact('${item.name}', '${viewMode}')"
                      onmouseenter="this.style.background='#ede9fe'" 
                      onmouseleave="this.style.background='transparent'">
                    ${item.name}
                  </li>
                `).join('')}
              </ul>
            ` : '<p style="color: #a1a1aa; font-size: 0.85rem; margin: 0;">No known callers in index</p>'}
          </div>
          <!-- Downstream List -->
          <div style="background: linear-gradient(135deg, #ecfdf5 0%, #f0fdf4 100%); border: 1px solid #34d399; border-radius: 8px; padding: 1rem;">
            <h4 style="margin: 0 0 0.5rem; color: #059669; font-size: 0.9rem;">🔽 Calls (Downstream)</h4>
            ${data.downstreamList && data.downstreamList.length > 0 ? `
              <ul style="list-style: none; padding: 0; margin: 0; max-height: 150px; overflow-y: auto;">
                ${data.downstreamList.map(item => `
                  <li style="font-family: monospace; font-size: 0.85rem; padding: 0.25rem 0.5rem; cursor: pointer; border-radius: 4px; color: #065f46;" 
                      class="clickable-func"
                      onclick="App.loadImpact('${item.name}', '${viewMode}')"
                      onmouseenter="this.style.background='#d1fae5'" 
                      onmouseleave="this.style.background='transparent'">
                    ${item.name}
                  </li>
                `).join('')}
              </ul>
            ` : '<p style="color: #a1a1aa; font-size: 0.85rem; margin: 0;">No outgoing calls in index</p>'}
          </div>
        </div>
      `;
      
      container.innerHTML = statsHtml + `<div id="impact-network" style="height: calc(50vh - 80px); border: 1px solid var(--border-color); border-radius: 8px; overflow: auto; background: var(--surface);"></div>` + listsHtml;
      
      const graphContainer = document.getElementById("impact-network");
      
      if (viewMode === 'mindmap') {
        this.renderMermaidGraph(graphContainer, data, functionName);
      } else {
        this.renderVisGraph(graphContainer, data, functionName);
      }
    } catch (e) {
      container.innerHTML = `<div class="error">Error: ${e.message}</div>`;
    }
  },
  
  switchImpactView(mode) {
    if (this._currentImpactFunction) {
      this.loadImpact(this._currentImpactFunction, mode);
    }
  },
  
  renderMermaidGraph(container, data, centerNode) {
    // Build node name mapping for click handlers
    const nodeNameMap = new Map();
    data.nodes.forEach(node => {
      nodeNameMap.set(node.id, node.label);
    });
    
    // Build Mermaid flowchart from graph data
    let mermaidCode = 'flowchart LR\n';
    
    // Style definitions
    mermaidCode += '  classDef center fill:#6366f1,stroke:#4f46e5,color:#fff,stroke-width:3px\n';
    mermaidCode += '  classDef upstream fill:#f3e8ff,stroke:#a855f7,color:#581c87\n';
    mermaidCode += '  classDef downstream fill:#ecfdf5,stroke:#10b981,color:#064e3b\n';
    
    // Escape function names for Mermaid (replace special chars)
    const sanitize = (name) => name.replace(/[:\\.]/g, '_').replace(/[^a-zA-Z0-9_]/g, '');
    
    // Add nodes with labels
    const nodeIds = new Map();
    data.nodes.forEach((node, idx) => {
      const id = sanitize(node.id) || `node_${idx}`;
      nodeIds.set(node.id, id);
      const label = node.label.length > 30 ? node.label.substring(0, 27) + '...' : node.label;
      mermaidCode += `  ${id}["${label}"]\n`;
    });
    
    // Add edges
    data.edges.forEach(edge => {
      const fromId = nodeIds.get(edge.from);
      const toId = nodeIds.get(edge.to);
      if (fromId && toId) {
        mermaidCode += `  ${fromId} --> ${toId}\n`;
      }
    });
    
    // Apply classes
    const centerSanitized = sanitize(centerNode);
    mermaidCode += `  class ${centerSanitized} center\n`;
    
    data.nodes.forEach(node => {
      const id = nodeIds.get(node.id);
      if (node.level === -1) {
        mermaidCode += `  class ${id} upstream\n`;
      } else if (node.level === 1) {
        mermaidCode += `  class ${id} downstream\n`;
      }
    });
    
    // Render with Mermaid
    container.innerHTML = `<pre class="mermaid" style="display: flex; justify-content: center; align-items: center; min-height: 400px;">${mermaidCode}</pre>`;
    
    // Store reference for click handlers
    const self = this;
    
    // Initialize Mermaid
    if (window.mermaid) {
      mermaid.initialize({ 
        startOnLoad: false,
        theme: document.documentElement.classList.contains('dark') ? 'dark' : 'default',
        flowchart: { 
          useMaxWidth: false,
          htmlLabels: true,
          curve: 'basis'
        }
      });
      mermaid.run({ nodes: container.querySelectorAll('.mermaid') }).then(() => {
        // Add click handlers AFTER Mermaid has rendered the SVG
        setTimeout(() => {
          const nodes = container.querySelectorAll('.node');
          nodes.forEach(node => {
            // Extract function name from the node text
            const textEl = node.querySelector('.nodeLabel') || node.querySelector('text');
            if (!textEl) return;
            
            let funcName = textEl.textContent.trim();
            // Skip if it's the center node (already viewing it)
            if (funcName === centerNode || funcName.startsWith(centerNode.substring(0, 20))) return;
            
            // Style as clickable
            node.style.cursor = 'pointer';
            node.setAttribute('title', 'Click to explore: ' + funcName);
            
            // Add hover effect
            node.addEventListener('mouseenter', () => {
              node.style.filter = 'brightness(1.15)';
              node.style.transform = 'scale(1.05)';
              node.style.transition = 'all 0.2s ease';
            });
            node.addEventListener('mouseleave', () => {
              node.style.filter = '';
              node.style.transform = '';
            });
            
            // Add click handler for drill-down
            node.addEventListener('click', (e) => {
              e.preventDefault();
              e.stopPropagation();
              self.loadImpact(funcName, 'mindmap');
            });
          });
        }, 300); // Small delay to ensure SVG is fully rendered
      });
    }
  },

  async loadDashboardStats() {
    // Fetch suggestions count, etc.
    // Placeholder for now
  },

  // --- Rendering ---

  renderSearchResults(results) {
    const container = document.getElementById("search-results");
    if (results.length === 0) {
      container.innerHTML = "<p>No results found.</p>";
      return;
    }

    const html = results
      .map(
        (item) => `
            <div class="card result-item">
                <div style="display:flex; justify-content:space-between;">
                    <h3 style="font-family: monospace; color: var(--primary);">${item.name}</h3>
                    <span class="badge">${item.type}</span>
                </div>
                <div style="color: var(--text-secondary); font-size: 0.9em; margin-top: 0.5rem;">
                    ${item.file}:${item.line}
                </div>
                <div style="margin-top: 1rem;">
                    <button class="btn btn-sm" onclick="App.loadImpact('${item.name}')">View Impact</button>
                </div>
            </div>
        `,
      )
      .join("");
    container.innerHTML = `<div class="grid">${html}</div>`;
  },

  renderVisGraph(container, data, centerNode = null) {
    // Vis.js Logic
    const nodes = new vis.DataSet(data.nodes);
    const edges = new vis.DataSet(data.edges);

    const options = {
      nodes: {
        shape: "dot",
        size: 16,
        font: {
          color: getComputedStyle(document.body).getPropertyValue(
            "--text-primary",
          ),
        },
        // Style groups
        groups: {
          php: { color: { background: "#8b5cf6", border: "#7c3aed" } },
          javascript: { color: { background: "#f59e0b", border: "#d97706" } },
          default: { color: { background: "#94a3b8", border: "#64748b" } }
        }
      },
      edges: {
        color: { color: "#94a3b8" },
        arrows: "to",
        smooth: { type: "continuous" },
      },
      physics: {
        stabilization: false,
        barnesHut: {
          gravitationalConstant: -2000,
          springConstant: 0.04,
        },
      },
      layout: { randomSeed: 2 }, // Consistent layout
      interaction: {
        hover: true,
        tooltipDelay: 200
      }
    };

    this.state.network = new vis.Network(container, { nodes, edges }, options);
    
    // Highlight center node
    if (centerNode) {
      this.state.network.once("stabilized", () => {
        this.state.network.focus(centerNode, { scale: 1.2, animation: true });
      });
    }

    // Single Click: Show tooltip / Log
    this.state.network.on("click", (params) => {
      if (params.nodes.length > 0) {
        const nodeId = params.nodes[0];
        console.log("Clicked:", nodeId);
      }
    });
    
    // Double Click: Drill-down (Explore that node)
    this.state.network.on("doubleClick", (params) => {
      if (params.nodes.length > 0) {
        const nodeId = params.nodes[0];
        // Don't drill into the same node
        if (nodeId !== this._currentImpactFunction) {
          this.loadImpact(nodeId);
        }
      }
    });
  },

  // --- Settings ---

  toggleTheme() {
    this.state.theme = this.state.theme === "light" ? "dark" : "light";
    localStorage.setItem("theme", this.state.theme);
    this.applyTheme();
  },

  applyTheme() {
    if (this.state.theme === "dark") {
      document.documentElement.classList.add("dark");
    } else {
      document.documentElement.classList.remove("dark");
    }
    // Force Vis.js redraw if needed for colors
  },

  toggleDensity() {
    this.state.density = this.state.density === "medium" ? "compact" : "medium";
    localStorage.setItem("density", this.state.density);
    this.applyDensity();
  },

  applyDensity() {
    document.body.setAttribute("data-density", this.state.density);
  },
};

// Start
document.addEventListener("DOMContentLoaded", () => App.init());
