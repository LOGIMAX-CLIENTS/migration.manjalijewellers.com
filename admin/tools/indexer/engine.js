const fs = require('fs');
const path = require('path');

// 1. Argument Parsing
const args = process.argv.slice(2);
const moduleArg = args.find(a => a.startsWith('--module='));

if (!moduleArg) {
    console.error('Error: Please specify target module. Example: node engine.js --module=estimation');
    process.exit(1);
}

const moduleName = moduleArg.split('=')[1];
const rootDir = path.resolve(__dirname, '../../..'); // c:/xampp/htdocs/etail_v3
const configPath = path.join(__dirname, 'config.json');

// 2. Load Config
if (!fs.existsSync(configPath)) {
    console.error('Error: Config file not found at ' + configPath);
    process.exit(1);
}

const config = JSON.parse(fs.readFileSync(configPath, 'utf8'));

if (!config.modules[moduleName]) {
    console.error(`Error: Module '${moduleName}' not found in config.`);
    process.exit(1);
}

const moduleConfig = config.modules[moduleName];
console.log(`🚀 Starting Universal Indexer for: ${moduleConfig.name}`);

// 3. The Knowledge Graph Structure
const knowledgeGraph = {
    meta: {
        module: moduleName,
        generated_at: new Date().toISOString(),
        version: "1.0"
    },
    functions: {},       // logic
    ui_map: {},          // dom_id -> logic
    api_endpoints: {},   // ajax -> controller
    files_scanned: []
};

// 4. Regex Patterns (The "Eyes")
const patterns = {
    // JavaScript Patterns
    js_function: /function\s+([a-zA-Z0-9_]+)\s*\(/g,
    js_jq_id_click: /\$\(['"]#([a-zA-Z0-9_-]+)['"]\)\.(?:click|on\('click')/g,
    js_ajax_url: /url\s*:\s*['"]([^'"]+)['"]/g,
    
    // PHP Patterns
    php_function: /public\s+function\s+([a-zA-Z0-9_]+)\s*\(/g,
    php_model_load: /load->model\(['"]([^'"]+)['"]\)/g
};

// 5. The Scanner Loop (Pass 1: Discovery)
moduleConfig.paths.forEach(relativePath => {
    let filesToScan = [];
    const fullPath = path.join(rootDir, relativePath);

    if (fs.existsSync(fullPath)) {
        filesToScan.push({ path: fullPath, type: path.extname(fullPath).substring(1) });
    } else {
        console.warn(`⚠️ Warning: File not found: ${relativePath}`);
    }

    filesToScan.forEach(fileObj => {
        knowledgeGraph.files_scanned.push(relativePath);
        const content = fs.readFileSync(fileObj.path, 'utf8');
        const lines = content.split('\n');

        // A. Parse JavaScript
        if (fileObj.type === 'js') {
            console.log(`   Scanning JS (Discovery): ${path.basename(fileObj.path)}`);
            
            // Extract Functions
            let match;
            while ((match = patterns.js_function.exec(content)) !== null) {
                const funcName = match[1];
                const lineNum = content.substring(0, match.index).split('\n').length;
                
                knowledgeGraph.functions[funcName] = {
                    file: relativePath,
                    line: lineNum,
                    type: 'client_logic'
                };
            }

            // Extract UI Bindings (ID clicks)
            while ((match = patterns.js_jq_id_click.exec(content)) !== null) {
                const domId = match[1];
                knowledgeGraph.ui_map[domId] = {
                    file: relativePath,
                    trigger: 'click'
                };
            }
        }

        // B. Parse PHP
        if (fileObj.type === 'php') {
            console.log(`   Scanning PHP (Discovery): ${path.basename(fileObj.path)}`);
            
            while ((match = patterns.php_function.exec(content)) !== null) {
                const funcName = match[1];
                const lineNum = content.substring(0, match.index).split('\n').length;
                
                knowledgeGraph.api_endpoints[funcName] = {
                    file: relativePath,
                    line: lineNum,
                    type: 'server_handler'
                };
            }
        }
    });
});

// 6. Pass 2: Dependency Linking (Call Graph)
console.log('🔄 Building Call Graph (Pass 2)...');
const allKnownFunctions = Object.keys(knowledgeGraph.functions);

knowledgeGraph.calls = []; // Store edges: { source: 'funcA', target: 'funcB' }

moduleConfig.paths.forEach(relativePath => {
    const fullPath = path.join(rootDir, relativePath);
    if (!fs.existsSync(fullPath)) return;
    
    // Only scan JS for calls for now (PHP is complex with $this->)
    if (path.extname(fullPath) !== '.js') return;
    
    const content = fs.readFileSync(fullPath, 'utf8');
    
    // Naive Call Scraper: Check if Function B is mentioned inside Function A
    // Optimized: We iterate functions, find their BODY, then search for other functions.
    // Super Naive (POC): Just substring search. 
    // Better (POC): Regex to find body? No, too hard with nested braces.
    // "Approximation": If Func B appears in file, and we are "below" Func A's start and "above" Func A's end...
    // Let's assume sequential functions for this legacy file.
    
    // 1. Sort functions by line number
    const fileFuncs = [];
    for (const [name, meta] of Object.entries(knowledgeGraph.functions)) {
        if (meta.file === relativePath) {
            fileFuncs.push({ name, line: meta.line });
        }
    }
    fileFuncs.sort((a, b) => a.line - b.line);
    
    // 2. Scan ranges
    const lines = content.split('\n');
    
    fileFuncs.forEach((func, idx) => {
        const nextFunc = fileFuncs[idx + 1];
        const endLine = nextFunc ? nextFunc.line : lines.length; // Approximate scope
        
        // Scan body lines
        for (let i = func.line; i < endLine; i++) {
            const lineContent = lines[i] || '';
            
            // Check for calls to ANY known function
            allKnownFunctions.forEach(target => {
                if (target === func.name) return; // Recursion ignored for now
                
                // Regex: target( or target (
                const callPattern = new RegExp(`\\b${target}\\s*\\(`, 'g');
                if (callPattern.test(lineContent)) {
                    knowledgeGraph.calls.push({
                        source: func.name,
                        target: target,
                        line: i + 1
                    });
                }
            });
        }
    });
});
console.log(`   Edges Found: ${knowledgeGraph.calls.length}`);

// 7. Save Output
const outputDir = path.join(rootDir, 'admin/knowledge_base/indexes');
if (!fs.existsSync(outputDir)) {
    fs.mkdirSync(outputDir, { recursive: true });
}

const outputPath = path.join(outputDir, `${moduleName}_index.json`);
fs.writeFileSync(outputPath, JSON.stringify(knowledgeGraph, null, 2));

console.log(`✅ Indexing Complete!`);
console.log(`   Functions Found: ${Object.keys(knowledgeGraph.functions).length}`);
console.log(`   API Endpoints: ${Object.keys(knowledgeGraph.api_endpoints).length}`);
console.log(`   Saved to: ${outputPath}`);
