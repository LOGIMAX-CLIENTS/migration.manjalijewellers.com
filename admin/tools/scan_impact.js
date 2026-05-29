const fs = require("fs");
const path = require("path");

const filePath = path.join(__dirname, '../assets/js/ret_estimation.js');
// Adjust path if strictly running from admin/tools/

function scanFile(file) {
  const content = fs.readFileSync(file, "utf8");
  const lines = content.split("\n");

  const functions = [];
  const calls = {}; // Map of function -> [called functions]
  const stringMap = {}; // Map of "String" -> [functions containing it]
  const currentScope = []; // Stack to track which function we are in

  // Regex patterns
  const funcDefRegex = /function\s+([a-zA-Z0-9_]+)\s*\(/;
  const funcCallRegex = /([a-zA-Z0-9_]+)\s*\(/g;
  const stringRegex = /['"]([^'"]{3,50})['"]/g; // Strings 3-50 chars long

  lines.forEach((line, index) => {
    const lineNum = index + 1;

    // Detect Function Start
    const defMatch = line.match(funcDefRegex);
    if (defMatch) {
      const funcName = defMatch[1];
      functions.push({ name: funcName, line: lineNum });
      currentScope.push(funcName);
    }

    // Simple heuristic for scope end (formatting reliant, but works for prototype)
    // In reality we'd use an AST parser, but this is a concept proof.
    if (line.match(/^}/)) {
      // Very naive check
      // currentScope.pop();
    }

    const activeFunc =
      currentScope.length > 0
        ? currentScope[currentScope.length - 1]
        : "GLOBAL";

    // Detect Function Calls
    let callMatch;
    while ((callMatch = funcCallRegex.exec(line)) !== null) {
      const calledFunc = callMatch[1];
      // Filter out keywords
      if (
        ![
          "if",
          "for",
          "while",
          "switch",
          "function",
          "parseFloat",
          "parseInt",
          "alert",
        ].includes(calledFunc)
      ) {
        if (!calls[activeFunc]) calls[activeFunc] = new Set();
        calls[activeFunc].add(calledFunc);
      }
    }

    // Detect Strings (UI Labels)
    let strMatch;
    while ((strMatch = stringRegex.exec(line)) !== null) {
      const str = strMatch[1];
      if (!stringMap[str]) stringMap[str] = new Set();
      stringMap[str].add(activeFunc);
    }
  });

  return { functions, calls, stringMap };
}

try {
  const result = scanFile(filePath);

  console.log("--- SCAN COMPLETE ---");
  console.log(`Found ${result.functions.length} functions.`);

  // Demonstrate "Search by String"
  const searchTerms = ["Platinum", "Gold", "Rate", "Calculate"];
  console.log("\n--- SEARCH DEMO (UI to Code) ---");
  searchTerms.forEach((term) => {
    console.log(`\nSearching for concept: "${term}"...`);
    const matches = Object.keys(result.stringMap).filter((s) =>
      s.toLowerCase().includes(term.toLowerCase()),
    );
    matches.slice(0, 5).forEach((m) => {
      const funcs = Array.from(result.stringMap[m]);
      console.log(`  Found string "${m}" in functions: ${funcs.join(", ")}`);
    });
  });

  console.log("\n--- CALL GRAPH DEMO (Code to Code) ---");
  const keyFunc = "calculate_sales_details";
  if (result.calls[keyFunc]) {
    console.log(
      `${keyFunc} calls:`,
      Array.from(result.calls[keyFunc]).join(", "),
    );
  } else {
    console.log(
      `${keyFunc} found, but calls could not be parsed with simple regex (Use AST for full prod).`,
    );
  }
} catch (e) {
  console.error("Error:", e.message);
}
