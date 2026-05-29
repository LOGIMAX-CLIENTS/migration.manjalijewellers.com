<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>eTail v3 Knowledge Base Explorer</title>
    
    <!-- Tailwind CSS (Local - Faster Loading) -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/tailwind.min.css'); ?>">
    
    <!-- Marked.js -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    
    <!-- Mermaid.js (Fixed Version for Compatibility) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mermaid/9.4.3/mermaid.min.js"></script>
    
    <!-- Highlight.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

    <style>
        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .prose { max-width: 100%; color: #334155; }
        .prose h1 { color: #1e293b; font-weight: 800; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem; margin-top: 1.5rem; }
        .prose h2 { color: #334155; font-weight: 700; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.3rem; margin-top: 2rem; }
        .prose h3 { color: #475569; font-weight: 600; margin-top: 1.5rem; }
        .prose a { color: #2563eb; text-decoration: none; font-weight: 500; }
        .prose a:hover { text-decoration: underline; }
        .prose blockquote { border-left: 4px solid #3b82f6; background: #eff6ff; padding: 1rem; font-style: normal; border-radius: 0.5rem; color: #1e40af; }
        .prose table { width: 100%; border-collapse: collapse; margin: 1rem 0; font-size: 0.95rem; }
        .prose th { background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.75rem; text-align: left; font-weight: 600; color: #475569; }
        .prose td { border: 1px solid #e2e8f0; padding: 0.75rem; color: #334155; }
        .prose tr:nth-child(even) { background-color: #fcfcfc; }
        .prose img { border-radius: 0.5rem; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); max-width: 100%; }
        .prose code { color: #d946ef; font-weight: 600; font-size: 0.9em; background: #fdf4ff; padding: 0.1rem 0.3rem; border-radius: 0.25rem; }
        .prose pre { background: #1e293b; color: #f8fafc; padding: 1rem; border-radius: 0.5rem; overflow-x: auto; }
        .prose pre code { color: inherit; background: transparent; padding: 0; font-weight: normal; }

        .mermaid { background: white; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e2e8f0; text-align: center; margin: 1.5rem 0; }
    </style>
</head>
<body class="bg-gray-50 h-screen flex flex-col font-sans">
    <!-- Header -->
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shadow-sm flex-shrink-0 z-10">
        <div class="flex items-center gap-3">
            <div class="bg-indigo-600 text-white p-2 rounded-lg shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-800">Secure Knowledge Base</h1>
                <div class="text-sm text-gray-500 flex items-center gap-1">
                    <a href="<?php echo site_url('admin_knowledge_base'); ?>" class="hover:text-indigo-600">Root</a>
                    <?php
                    if ($userDir) {
                        $parts = explode('/', trim($userDir, '/'));
                        $path = '';
                        foreach ($parts as $part) {
                            $path .= $part . '/';
                            echo '<span class="text-gray-400">/</span>';
                            echo '<a href="' . site_url('admin_knowledge_base?dir=' . urlencode(trim($path, '/'))) . '" class="hover:text-indigo-600 font-medium">' . ucfirst($part) . '</a>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <div>
           <a href="<?php echo site_url('admin/dashboard'); ?>" class="text-gray-500 hover:text-gray-700 font-medium">Dashboard ↩</a>
        </div>
    </header>

    <!-- Main Layout -->
    <div class="flex flex-1 overflow-hidden">
        
        <!-- Sidebar -->
        <aside class="w-80 bg-white border-r border-gray-200 flex flex-col overflow-y-auto">
            
            <!-- Live Code Search -->
            <div class="p-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-xs font-semibold text-indigo-600 uppercase tracking-wider mb-2">Live Code Map</h3>
                <div class="relative">
                    <input type="text" id="impact-search" placeholder="Search (e.g. Platinum)..." 
                        class="w-full text-sm border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <button id="btn-search" class="absolute right-2 top-2 text-indigo-600 hover:text-indigo-800">
                        🔍
                    </button>
                </div>
            </div>

            <!-- AI Request Button -->
            <div class="p-4 border-b border-gray-100">
                <button id="btn-open-wizard" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white text-sm font-semibold py-2 px-4 rounded-md shadow hover:from-purple-700 hover:to-indigo-700 transition-all flex items-center justify-center gap-2">
                    <span>⚡ Create Clean Request</span>
                </button>
            </div>

            <?php if (!empty($dirs)): ?>
            <div class="p-4 border-b border-gray-100">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Folders</h3>
                <nav class="space-y-1">
                    <?php if ($userDir): ?>
                        <a href="<?php echo site_url('admin_knowledge_base?dir=' . urlencode(dirname($userDir) === '.' ? '' : dirname($userDir))); ?>" class="block px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-md">
                            📁 .. (Back)
                        </a>
                    <?php endif; ?>
                    
                    <?php foreach ($dirs as $dir): ?>
                        <?php $targetDir = $userDir ? $userDir . '/' . $dir : $dir; ?>
                        <a href="<?php echo site_url('admin_knowledge_base?dir=' . urlencode($targetDir)); ?>" class="block px-3 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 rounded-md transition-colors">
                            📁 <?php echo ucfirst($dir); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>
            <?php endif; ?>

            <div class="p-4">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Documents</h3>
                <nav class="space-y-1">
                    <?php if (empty($mdFiles)): ?>
                        <div class="text-sm text-gray-400 italic px-3">No Markdown files here.</div>
                    <?php else: ?>
                        <?php foreach ($mdFiles as $file): ?>
                            <?php
                            $isActive = ($file === $activeFile);
                            $activeClass = $isActive 
                                ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-600 font-medium' 
                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-l-4 border-transparent';
                            
                            $icon = '📄';
                            if (strpos($file, 'INDEX') !== false) $icon = '🏠';
                            if (strpos($file, 'Summary') !== false) $icon = '🚀';
                            ?>
                            <a href="<?php echo site_url('admin_knowledge_base?dir=' . urlencode($userDir) . '&file=' . urlencode($file)); ?>" class="block px-4 py-3 text-sm transition-colors duration-150 <?php echo $activeClass; ?>">
                                <span class="mr-2"><?php echo $icon; ?></span><?php echo str_replace(['.md', '_'], ['',' '], ucfirst($file)); ?>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </nav>
            </div>
        </aside>

        <!-- Content Area -->
        <main class="flex-1 overflow-y-auto bg-white p-8 lg:p-12">
            <article class="prose prose-slate max-w-4xl mx-auto" id="content">
                <?php
                if ($content) {
                    echo "<div id='raw-markdown' style='display:none;'>" . htmlspecialchars($content) . "</div>";
                } elseif (empty($mdFiles) && empty($dirs)) {
                    echo "<div class='text-center py-20 text-gray-400'>Empty directory.</div>";
                } elseif (!$activeFile) {
                    echo "<div class='text-center py-20 text-gray-500'>Select a document to view.</div>";
                } else {
                    echo "<div class='text-center py-20 text-red-500'>File not found.</div>";
                }
                ?>
            </article>
        </main>
    </div>

    <!-- Wizard Modal -->
    <div id="wizard-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50 backdrop-blur-sm">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl transform transition-all scale-95 opacity-0" id="wizard-panel">
            <!-- Modal Header -->
            <div class="bg-indigo-600 px-6 py-4 rounded-t-xl flex justify-between items-center">
                <h2 class="text-white text-lg font-bold flex items-center gap-2">
                    ⚡ Clean Change Request
                    <span class="text-xs bg-indigo-500 text-indigo-100 px-2 py-0.5 rounded-full">AI Architect</span>
                </h2>
                <button id="btn-close-wizard" class="text-indigo-200 hover:text-white text-2xl leading-none">&times;</button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Request Type</label>
                        <select id="req-type" class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="bug">🐛 Bug Fix</option>
                            <option value="feature">✨ New Feature</option>
                            <option value="refactor">♻️ Logic Refactor</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Target Module/File</label>
                        <input type="text" id="req-target" value="ret_estimation.js" class="w-full border border-gray-300 rounded-md p-2 text-sm font-mono text-gray-600 focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Filename...">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Summary (The "Soft" Spec)</label>
                    <input type="text" id="req-title" class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="e.g. Allow Platinum Rate > 0 manually">
                </div>

                <div id="group-business-value">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Value (The Why)</label>
                    <input type="text" id="req-value" class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="e.g. Mandatory compliance, Saves 10 clicks...">
                </div>

                <div>
                    <label id="lbl-desc" class="block text-sm font-medium text-gray-700 mb-1">Context & Details (User Story)</label>
                    <textarea id="req-desc" rows="3" class="w-full border border-gray-300 rounded-md p-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="As an admin, when I enter 50 for Platinum, it reverts to 0..."></textarea>
                </div>
                
                <!-- Function Name Extraction -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Function (for Impact Analysis)</label>
                    <div class="flex gap-2">
                        <input type="text" id="req-function" class="flex-1 border border-gray-300 rounded-md p-2 text-sm font-mono focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="e.g. calculate_platinum, getBillDetails">
                        <button type="button" id="btn-scan-impact" class="px-4 py-2 bg-amber-500 text-white rounded-md text-sm font-medium hover:bg-amber-600 transition-colors flex items-center gap-1">
                            🔍 Scan
                        </button>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Enter a function name to see what will be affected by changes</p>
                </div>
                
                <!-- Impact Preview (populated by Scan) -->
                <div id="impact-preview" class="hidden bg-amber-50 border border-amber-200 rounded-md p-4 text-sm">
                    <h4 class="font-bold text-amber-700 mb-2 flex items-center gap-2">
                        📊 Impact Preview
                        <span id="impact-count" class="text-xs bg-amber-200 text-amber-800 px-2 py-0.5 rounded-full">0 functions</span>
                    </h4>
                    <div id="impact-content" class="text-gray-600 space-y-2"></div>
                </div>
                
                <!-- AI Output Preview Area -->
                <div id="ai-preview" class="hidden bg-gray-50 border border-gray-200 rounded-md p-4 text-sm">
                    <h4 class="font-bold text-gray-700 mb-2">🤖 AI Analysis Plan:</h4>
                    <div id="ai-content" class="prose prose-sm max-w-none text-gray-600"></div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end gap-3 border-t border-gray-100">
                <button id="btn-cancel-wizard" class="px-4 py-2 text-gray-600 hover:bg-gray-200 rounded-md text-sm font-medium transition-colors">Cancel</button>
                <button id="btn-submit-wizard" class="px-6 py-2 bg-indigo-600 text-white rounded-md text-sm font-bold shadow hover:bg-indigo-700 transition-transform transform active:scale-95 flex items-center gap-2">
                    <span>🚀 Analyze & Plan</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
             // ... [Existing Markdown & Impact Map Logic] ...
             
            // --- UI Dynamic Logic ---
            const reqType = document.getElementById('req-type');
            const groupBizValue = document.getElementById('group-business-value');
            const lblDesc = document.getElementById('lbl-desc');
            const reqDesc = document.getElementById('req-desc');

            if (reqType) {
                reqType.addEventListener('change', () => {
                    const val = reqType.value;
                    if (val === 'bug') {
                        // Bug Mode
                        if(groupBizValue) groupBizValue.classList.add('hidden');
                        if(lblDesc) lblDesc.textContent = 'Steps to Reproduce & Error Msg';
                        if(reqDesc) reqDesc.placeholder = '1. Go to Sales\n2. Click Save\n3. Error: "Invalid Tax"...';
                    } else {
                        // Feature Mode
                        if(groupBizValue) groupBizValue.classList.remove('hidden');
                        if(lblDesc) lblDesc.textContent = 'Context & Details (User Story)';
                        if(reqDesc) reqDesc.placeholder = 'As an admin, when I enter 50 for Platinum, it reverts to 0...';
                    }
                });
                // Init
                reqType.dispatchEvent(new Event('change'));
            }

            const rawEl = document.getElementById('raw-markdown');
            const contentEl = document.getElementById('content');
            
            // --- Markdown Rendering Logic ---
            if (rawEl) {
                // Check Libraries
                if (typeof marked === 'undefined') {
                    contentEl.innerHTML = '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert"><p class="font-bold">Error</p><p>Markdown library (Marked.js) failed to load. Please check your internet connection (CDNs require access).</p></div><div class="mt-4 p-4 bg-gray-100 rounded overflow-auto"><pre>' + rawEl.innerText + '</pre></div>';
                } else {
                    try {
                        // Configure Marked
                        marked.use({
                            renderer: {
                                code(code, lang) {
                                    let text = code;
                                    let language = lang;
                                    if (typeof code === 'object' && code !== null && code.text) {
                                        text = code.text;
                                        language = code.lang;
                                    }

                                    if (language && language.trim() === 'mermaid') {
                                        return '<div class="mermaid">' + text + '</div>';
                                    }
                                    
                                    try {
                                        const strCode = String(text);
                                        const validLang = !!(language && typeof hljs !== 'undefined' && hljs.getLanguage(language)) ? language : 'plaintext';
                                        
                                        if (typeof hljs !== 'undefined') {
                                            const highlighted = hljs.highlight(strCode, { language: validLang }).value;
                                            return '<pre><code class="hljs language-' + validLang + '">' + highlighted + '</code></pre>';
                                        }
                                    } catch (err) {
                                        console.warn('Highlighting failed, falling back to raw:', err);
                                    }
                                    return '<pre><code class="language-' + (language || 'plaintext') + '">' + text + '</code></pre>';
                                }
                            }
                        });

                        // Render Markdown
                        const rawMarkdown = rawEl.innerText;
                        contentEl.innerHTML = marked.parse(rawMarkdown);

                        // Render Mermaid if available
                        if (typeof mermaid !== 'undefined') {
                            mermaid.initialize({ startOnLoad: false, theme: 'default', securityLevel: 'loose' });
                            setTimeout(() => {
                                mermaid.init(undefined, document.querySelectorAll('.mermaid'));
                            }, 50);
                        } else {
                            console.warn('Mermaid.js not loaded. Diagrams will show as code blocks.');
                        }

                    } catch (e) {
                        console.error('Rendering Error:', e);
                        contentEl.innerHTML = '<div class="text-red-500">Error rendering content: ' + e.message + '</div><pre>' + rawEl.innerText + '</pre>';
                    }
                }
            }

            // --- Impact Map Rendering Logic (Universal Indexer) ---
            function renderImpactMap(data) {
                let mermaidCode = "graph TD;\n";
                mermaidCode += "    ROOT[Search: " + data.term + "]:::root;\n";
                
                // 1. Defined Functions (Logic Nodes)
                if(data.functions && data.functions.length > 0) {
                    data.functions.forEach((func, index) => {
                        const nodeId = `FUNC${index}`;
                        // Square node for Logic
                        mermaidCode += `    ${nodeId}[ƒ: ${func.name}]:::func;\n`; 
                        mermaidCode += `    ROOT --> ${nodeId};\n`;
                        // Click event could go here
                    });
                }

                // 2. UI/Context Matches (Usage Nodes)
                if(data.ui_matches && data.ui_matches.length > 0) {
                    data.ui_matches.forEach((match, index) => {
                        const nodeId = `UI${index}`;
                        // Rounded node for Context/UI
                        // Truncate context for display
                        const label = match.context.replace(/["()]/g, '').substring(0, 30) + '...';
                        mermaidCode += `    ${nodeId}(Line ${match.line}: ${label}):::ui;\n`;
                        
                        // Link to Function if known, else link to ROOT
                        if(match.func && match.func !== 'GLOBAL') {
                            mermaidCode += `    ROOT -.-> ${nodeId};\n`;
                        } else {
                            mermaidCode += `    ROOT -.-> ${nodeId};\n`;
                        }
                    });
                }
                
                // 3. Calls (Dependency Nodes - Phase 2)
                if(data.calls && data.calls.length > 0) {
                        data.calls.forEach((call, index) => {
                        const nodeId = `CALL${index}`;
                        mermaidCode += `    ${nodeId}{{Calls: ${call.target}}}:::call;\n`;
                        mermaidCode += `    ROOT --> ${nodeId};\n`;
                        });
                }

                if((!data.functions || data.functions.length === 0) && (!data.ui_matches || data.ui_matches.length === 0)) {
                        mermaidCode += "    EMPTY[No Direct Impacts Found]:::empty;\n";
                        mermaidCode += "    ROOT --> EMPTY;\n";
                }

                // Styling
                mermaidCode += "    classDef root fill:#f9f,stroke:#333,stroke-width:2px;\n";
                mermaidCode += "    classDef func fill:#bbf,stroke:#333,stroke-width:1px;\n"; // Logic = Blue
                mermaidCode += "    classDef ui fill:#bfb,stroke:#333,stroke-width:1px,stroke-dasharray: 5 5;\n"; // Usage = Green Dashed
                mermaidCode += "    classDef call fill:#fbb,stroke:#333,stroke-width:1px;\n"; // Calls = Red
                mermaidCode += "    classDef empty fill:#eee,stroke:#999,stroke-width:1px;\n";

                const container = document.getElementById('impact-map');
                container.innerHTML = `<div class="mermaid">${mermaidCode}</div>`;
                
                // Re-init Mermaid
                setTimeout(() => {
                    mermaid.init(undefined, container.querySelectorAll('.mermaid'));
                }, 50);
            }

            // --- Impact Map Search Logic ---
            const searchInput = document.getElementById('impact-search');
            const searchBtn = document.getElementById('btn-search');

            function performSearch() {
                const term = searchInput.value.trim();
                if (!term) return;

                // Show Loading
                contentEl.innerHTML = '<div class="flex items-center justify-center h-64"><div class="text-indigo-600 text-lg animate-pulse">Scanning Codebase for "'+term+'"...</div></div>';

                fetch('<?php echo site_url("admin_knowledge_base/search_impact"); ?>?term=' + encodeURIComponent(term))
                .then(res => res.json())
                .then(data => {
                    if (data.error) throw new Error(data.error);
                    renderImpactMap(data);
                })
                .catch(err => {
                    contentEl.innerHTML = '<div class="p-4 bg-red-50 text-red-600 rounded">Error: ' + err.message + '</div>';
                });
            }

            function renderImpactMap(data) {
                const safeTerm = data.term.replace(/[^a-zA-Z0-9_]/g, '_');
                
                // Separate calls into downstream (calls) and upstream (called_by)
                const downstream = data.calls.filter(c => c.type === 'calls');
                const upstream = data.calls.filter(c => c.type === 'called_by');
                
                // Stats
                const statsHtml = `
                    <div class="flex gap-4 mb-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2 text-center">
                            <div class="text-2xl font-bold text-blue-600">${downstream.length}</div>
                            <div class="text-xs text-blue-500">Calls →</div>
                        </div>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg px-4 py-2 text-center">
                            <div class="text-2xl font-bold text-purple-600">${upstream.length}</div>
                            <div class="text-xs text-purple-500">← Called By</div>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 text-center">
                            <div class="text-2xl font-bold text-gray-600">${data.ui_matches.length}</div>
                            <div class="text-xs text-gray-500">References</div>
                        </div>
                    </div>
                `;
                
                // Build Mermaid Graph
                let graphDef = 'graph LR\n';
                
                // Central Node (The searched term)
                graphDef += `  Term["🔍 ${data.term}"]:::term\n`;
                
                // Upstream (Called By) - Left side
                if (upstream.length > 0) {
                    upstream.slice(0, 8).forEach((c, i) => {
                        const safeTarget = c.target.replace(/[^a-zA-Z0-9_]/g, '_');
                        graphDef += `  U${i}["${c.target}"]:::caller --> Term\n`;
                    });
                    if (upstream.length > 8) {
                        graphDef += `  UMore["... +${upstream.length - 8} more"]:::more --> Term\n`;
                    }
                }
                
                // Downstream (Calls) - Right side
                if (downstream.length > 0) {
                    downstream.slice(0, 8).forEach((c, i) => {
                        const safeTarget = c.target.replace(/[^a-zA-Z0-9_]/g, '_');
                        graphDef += `  Term --> D${i}["${c.target}"]:::callee\n`;
                    });
                    if (downstream.length > 8) {
                        graphDef += `  Term --> DMore["... +${downstream.length - 8} more"]:::more\n`;
                    }
                }
                
                // Level 2 Dependencies (2-hop depth)
                const level2 = data.level2 || {};
                let l2Count = 0;
                
                // For downstream functions, show what THEY call (L2 downstream)
                downstream.slice(0, 5).forEach((c, i) => {
                    const l2Data = level2[c.target];
                    if (l2Data && l2Data.calls && l2Data.calls.length > 0) {
                        l2Data.calls.slice(0, 3).forEach((l2Func, j) => {
                            graphDef += `  D${i} -.-> L2_${l2Count}["${l2Func}"]:::level2\n`;
                            l2Count++;
                        });
                        if (l2Data.calls.length > 3) {
                            graphDef += `  D${i} -.-> L2More${i}["...+${l2Data.calls.length - 3}"]:::more\n`;
                        }
                    }
                });
                
                // For upstream functions, show what calls THEM (L2 upstream)
                upstream.slice(0, 5).forEach((c, i) => {
                    const l2Data = level2[c.target];
                    if (l2Data && l2Data.called_by && l2Data.called_by.length > 0) {
                        l2Data.called_by.slice(0, 3).forEach((l2Func, j) => {
                            graphDef += `  L2U_${l2Count}["${l2Func}"]:::level2 -.-> U${i}\n`;
                            l2Count++;
                        });
                        if (l2Data.called_by.length > 3) {
                            graphDef += `  L2UMore${i}["...+${l2Data.called_by.length - 3}"]:::more -.-> U${i}\n`;
                        }
                    }
                });
                
                // Function definitions
                if (data.functions.length > 0 && downstream.length === 0 && upstream.length === 0) {
                    data.functions.forEach((f, i) => {
                        graphDef += `  Term -.-|defined| F${i}["📄 ${f.file.split('/').pop()}:${f.line}"]:::file\n`;
                    });
                }
                
                // Styles
                graphDef += `  classDef term fill:#818cf8,stroke:#4338ca,color:white,font-weight:bold\n`;
                graphDef += `  classDef caller fill:#bbf7d0,stroke:#22c55e,color:#166534\n`;
                graphDef += `  classDef callee fill:#bfdbfe,stroke:#3b82f6,color:#1e40af\n`;
                graphDef += `  classDef file fill:#fef3c7,stroke:#f59e0b,color:#92400e\n`;
                graphDef += `  classDef more fill:#f3f4f6,stroke:#9ca3af,color:#6b7280,stroke-dasharray:5\n`;
                graphDef += `  classDef level2 fill:#fef9c3,stroke:#ca8a04,color:#854d0e,stroke-dasharray:3\n`;
                

                // Build HTML
                let html = `
                    <h1 class="text-2xl font-bold mb-4">
                        Impact Analysis: <span class="text-indigo-600">${data.term}</span>
                    </h1>
                    ${statsHtml}
                    <div class="mermaid bg-white border border-gray-200 rounded-lg p-4 mb-6">${graphDef}</div>
                `;
                
                // Dependency Lists
                html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">';
                
                // Upstream List (Called By)
                html += '<div class="bg-purple-50 border border-purple-200 rounded-lg p-4">';
                html += '<h3 class="font-semibold text-purple-700 mb-2">🔼 Called By (Upstream)</h3>';
                if (upstream.length > 0) {
                    html += '<ul class="space-y-1 text-sm">';
                    upstream.forEach(c => {
                        html += `<li class="flex justify-between"><span class="font-mono text-purple-600">${c.target}</span><span class="text-gray-400">Line ${c.line}</span></li>`;
                    });
                    html += '</ul>';
                } else {
                    html += '<p class="text-gray-400 text-sm italic">No known callers in index</p>';
                }
                html += '</div>';
                
                // Downstream List (Calls)
                html += '<div class="bg-blue-50 border border-blue-200 rounded-lg p-4">';
                html += '<h3 class="font-semibold text-blue-700 mb-2">🔽 Calls (Downstream)</h3>';
                if (downstream.length > 0) {
                    html += '<ul class="space-y-1 text-sm">';
                    downstream.forEach(c => {
                        html += `<li class="flex justify-between"><span class="font-mono text-blue-600">${c.target}</span><span class="text-gray-400">Line ${c.line}</span></li>`;
                    });
                    html += '</ul>';
                } else {
                    html += '<p class="text-gray-400 text-sm italic">No outgoing calls in index</p>';
                }
                html += '</div>';
                
                html += '</div>';
                
                // Function Definitions
                if (data.functions.length > 0) {
                    html += '<div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">';
                    html += '<h3 class="font-semibold text-amber-700 mb-2">📌 Definition Location</h3>';
                    html += '<ul class="space-y-1 text-sm">';
                    data.functions.forEach(f => {
                        html += `<li><span class="font-mono">${f.file}</span> : <strong>Line ${f.line}</strong></li>`;
                    });
                    html += '</ul></div>';
                }
                
                // String References
                if (data.ui_matches.length > 0) {
                    html += '<details class="bg-gray-50 border border-gray-200 rounded-lg p-4">';
                    html += '<summary class="cursor-pointer font-semibold text-gray-700">📝 String References (' + data.ui_matches.length + ')</summary>';
                    html += '<ul class="mt-2 space-y-2 text-sm max-h-64 overflow-y-auto">';
                    data.ui_matches.forEach(m => {
                        html += `<li class="border-l-2 border-gray-300 pl-2"><span class="text-gray-500">Line ${m.line}</span><br><code class="text-xs bg-gray-200 px-1 rounded">${m.context}</code></li>`;
                    });
                    html += '</ul></details>';
                }
                
                contentEl.innerHTML = html;
                
                // Render Mermaid and Add Click Handlers
                setTimeout(() => {
                    mermaid.init(undefined, document.querySelectorAll('.mermaid'));
                    
                    // Add click handlers to all Mermaid nodes (after rendering)
                    setTimeout(() => {
                        const nodes = document.querySelectorAll('.mermaid .node');
                        nodes.forEach(node => {
                            // Skip the "more" nodes and the central term node
                            const nodeId = node.id || '';
                            if (nodeId.includes('More') || nodeId.includes('Term')) return;
                            
                            // Extract function name from the node text
                            const textEl = node.querySelector('.nodeLabel') || node.querySelector('text');
                            if (!textEl) return;
                            
                            let funcName = textEl.textContent.trim();
                            // Remove emoji prefix if present
                            funcName = funcName.replace(/^[🔍📄]\s*/, '').trim();
                            
                            // Style as clickable
                            node.style.cursor = 'pointer';
                            node.setAttribute('title', 'Click to explore: ' + funcName);
                            
                            // Add hover effect
                            node.addEventListener('mouseenter', () => {
                                node.style.filter = 'brightness(1.1)';
                                node.style.transform = 'scale(1.05)';
                            });
                            node.addEventListener('mouseleave', () => {
                                node.style.filter = '';
                                node.style.transform = '';
                            });
                            
                            // Add click handler
                            node.addEventListener('click', (e) => {
                                e.preventDefault();
                                e.stopPropagation();
                                
                                // Update search input and trigger search
                                searchInput.value = funcName;
                                performSearch();
                            });
                        });
                        
                        // Also make list items clickable
                        document.querySelectorAll('.font-mono.text-purple-600, .font-mono.text-blue-600').forEach(el => {
                            el.style.cursor = 'pointer';
                            el.setAttribute('title', 'Click to explore');
                            el.addEventListener('click', () => {
                                searchInput.value = el.textContent.trim();
                                performSearch();
                            });
                            el.addEventListener('mouseenter', () => {
                                el.style.textDecoration = 'underline';
                            });
                            el.addEventListener('mouseleave', () => {
                                el.style.textDecoration = '';
                            });
                        });
                    }, 300);
                }, 100);
            }



            // Event Listeners
            if(searchBtn) {
                console.log('Search Initialized'); // Debug
                searchBtn.addEventListener('click', performSearch);
                searchInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') performSearch();
                });
            } else {
                console.error('Search Button not found');
            }
            
            // --- Wizard Logic ---
            const modal = document.getElementById('wizard-modal');
            const panel = document.getElementById('wizard-panel');
            const btnOpen = document.getElementById('btn-open-wizard');
            const btnClose = document.getElementById('btn-close-wizard');
            const btnCancel = document.getElementById('btn-cancel-wizard');
            const btnSubmit = document.getElementById('btn-submit-wizard');

            function toggleModal(show) {
                if(show) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    setTimeout(() => {
                        panel.classList.remove('scale-95', 'opacity-0');
                        panel.classList.add('scale-100', 'opacity-100');
                    }, 50);
                } else {
                    panel.classList.remove('scale-100', 'opacity-100');
                    panel.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                    }, 200);
                }
            }

            if(btnOpen) btnOpen.addEventListener('click', () => toggleModal(true));
            if(btnClose) btnClose.addEventListener('click', () => toggleModal(false));
            if(btnCancel) btnCancel.addEventListener('click', () => toggleModal(false));
            
            // --- Auto-Save Logic ---
            const formFields = ['req-title', 'req-type', 'req-target', 'req-desc', 'req-value', 'req-function'];
            const SAVE_KEY = 'issue_tracker_draft';
            
            // 1. Load Draft
            function loadDraft() {
                const saved = localStorage.getItem(SAVE_KEY);
                if (saved) {
                    try {
                        const data = JSON.parse(saved);
                        formFields.forEach(id => {
                            const el = document.getElementById(id);
                            if (el && data[id]) el.value = data[id];
                        });
                        // Trigger change for dynamic UI
                        if (data['req-type']) reqType.dispatchEvent(new Event('change'));
                        
                        // Optional: Toast or Console
                        console.log('Draft restored');
                    } catch (e) {
                        console.error('Failed to load draft', e);
                    }
                }
            }
            
            // 2. Save Draft
            function saveDraft() {
                const data = {};
                formFields.forEach(id => {
                    const el = document.getElementById(id);
                    if (el) data[id] = el.value;
                });
                localStorage.setItem(SAVE_KEY, JSON.stringify(data));
            }
            
            // Bind Listeners
            formFields.forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('input', saveDraft);
                    el.addEventListener('change', saveDraft); // For select
                }
            });
            
            // Init Load
            loadDraft();

            // Clear on Submit
            // (See submit handler)
            
            // --- Scan Impact Button Logic ---
            const btnScanImpact = document.getElementById('btn-scan-impact');
            const impactPreview = document.getElementById('impact-preview');
            const impactContent = document.getElementById('impact-content');
            const impactCount = document.getElementById('impact-count');
            const reqFunction = document.getElementById('req-function');
            
            if (btnScanImpact) {
                btnScanImpact.addEventListener('click', () => {
                    const funcName = reqFunction.value.trim();
                    if (!funcName) {
                        alert('Please enter a function name to scan');
                        return;
                    }
                    
                    // Show loading
                    btnScanImpact.disabled = true;
                    btnScanImpact.innerHTML = '⏳ Scanning...';
                    impactPreview.classList.remove('hidden');
                    impactContent.innerHTML = '<p class="animate-pulse text-amber-600">Analyzing dependencies...</p>';
                    
                    // Fetch impact data
                    fetch('<?php echo site_url("admin_knowledge_base/search_impact"); ?>?term=' + encodeURIComponent(funcName))
                    .then(res => res.json())
                    .then(data => {
                        const downstream = data.calls.filter(c => c.type === 'calls');
                        const upstream = data.calls.filter(c => c.type === 'called_by');
                        const total = downstream.length + upstream.length;
                        
                        impactCount.textContent = total + ' functions affected';
                        
                        let html = '';
                        
                        // Risk Assessment
                        let riskLevel = 'Low';
                        let riskColor = 'green';
                        if (total > 10) { riskLevel = 'High'; riskColor = 'red'; }
                        else if (total > 5) { riskLevel = 'Medium'; riskColor = 'yellow'; }
                        
                        html += `<div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-medium ${riskColor === 'red' ? 'text-red-600 bg-red-100' : riskColor === 'yellow' ? 'text-yellow-600 bg-yellow-100' : 'text-green-600 bg-green-100'} px-2 py-0.5 rounded">
                                ${riskLevel} Impact Risk
                            </span>
                        </div>`;
                        
                        // Breakdown
                        html += '<div class="grid grid-cols-2 gap-2 text-xs">';
                        
                        // Upstream (Callers - who will break if we change this)
                        html += '<div class="bg-white/50 p-2 rounded">';
                        html += '<span class="font-semibold text-purple-600">⚠️ May break ' + upstream.length + ' callers:</span>';
                        if (upstream.length > 0) {
                            html += '<ul class="mt-1 space-y-0.5 text-gray-600">';
                            upstream.slice(0, 5).forEach(c => {
                                html += `<li>• ${c.target}</li>`;
                            });
                            if (upstream.length > 5) html += `<li class="text-gray-400">... +${upstream.length - 5} more</li>`;
                            html += '</ul>';
                        } else {
                            html += '<p class="text-gray-400 italic mt-1">No known callers</p>';
                        }
                        html += '</div>';
                        
                        // Downstream (Calls - dependencies we rely on)
                        html += '<div class="bg-white/50 p-2 rounded">';
                        html += '<span class="font-semibold text-blue-600">🔗 Depends on ' + downstream.length + ' functions:</span>';
                        if (downstream.length > 0) {
                            html += '<ul class="mt-1 space-y-0.5 text-gray-600">';
                            downstream.slice(0, 5).forEach(c => {
                                html += `<li>• ${c.target}</li>`;
                            });
                            if (downstream.length > 5) html += `<li class="text-gray-400">... +${downstream.length - 5} more</li>`;
                            html += '</ul>';
                        } else {
                            html += '<p class="text-gray-400 italic mt-1">No dependencies</p>';
                        }
                        html += '</div>';
                        
                        html += '</div>';
                        
                        // Definition location
                        if (data.functions.length > 0) {
                            html += `<p class="text-xs text-gray-500 mt-2">📍 Defined in: <code class="bg-gray-200 px-1 rounded">${data.functions[0].file}:${data.functions[0].line}</code></p>`;
                        }
                        
                        impactContent.innerHTML = html;
                        
                        // Reset button
                        btnScanImpact.disabled = false;
                        btnScanImpact.innerHTML = '🔍 Scan';
                    })
                    .catch(err => {
                        impactContent.innerHTML = `<p class="text-red-600">Error: ${err.message}</p>`;
                        btnScanImpact.disabled = false;
                        btnScanImpact.innerHTML = '🔍 Scan';
                    });
                });
            }
            
            if(btnSubmit) {
                btnSubmit.addEventListener('click', () => {
                    const title = document.getElementById('req-title').value;
                    const type = document.getElementById('req-type').value;
                    const target = document.getElementById('req-target').value;
                    const desc = document.getElementById('req-desc').value;
                    const value = document.getElementById('req-value').value;

                    // --- AI Quality Check 👮 ---
                    if(!title || title.length < 5) { alert('Please provide a descriptive Summary/Title'); return; }
                    
                    if (type === 'feature') {
                        if (!value || value.length < 5) {
                            alert('⚠️ Quality Check Failed:\n\nPlease explain the Business Value (The Why). This helps us prioritize.');
                            document.getElementById('req-value').focus();
                            return;
                        }
                    } else if (type === 'bug') {
                         if (!desc || desc.length < 10) {
                            alert('⚠️ Quality Check Failed:\n\nPlease provide detailed Steps to Reproduce.');
                            document.getElementById('req-desc').focus();
                            return;
                        }
                    }

                    // UI State Loading
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML = '⚡ Submitting to AI...';
                    
                    const payload = {
                        title: title,
                        type: type,
                        target: target,
                        description: desc,
                        business_value: value
                    };

                    fetch('<?php echo site_url("admin_knowledge_base/submit_change_request"); ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            // 🧹 Clear Draft on Success
                            localStorage.removeItem(SAVE_KEY);
                            
                            const preview = document.getElementById('ai-preview');
                            const content = document.getElementById('ai-content');
                            
                            preview.classList.remove('hidden');
                            content.innerHTML = `
                                <div class="bg-green-50 text-green-700 p-3 rounded mb-2 border border-green-200">
                                    <strong>✅ Request Received!</strong><br>
                                    Ticket ID: <code>${data.ticket_id}</code>
                                </div>
                                <p><strong>Next Step:</strong> The AI Agent is analyzing <code>${target}</code>. Check back in 1 minute for the Plan.</p>
                            `;
                            btnSubmit.innerHTML = '🚀 Request Sent';
                        } else {
                            throw new Error(data.error || 'Server Error');
                        }
                    })
                    .catch(err => {
                        alert('Error: ' + err.message);
                        btnSubmit.innerHTML = '🚀 Analyze & Plan'; // Reset
                        btnSubmit.disabled = false;
                    });
                });
            }
        });
    </script>
</body>
</html>
