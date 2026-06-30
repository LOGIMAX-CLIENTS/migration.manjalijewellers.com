"""
Convert SDLC documentation from Markdown to styled HTML.
Usage: python build_docs.py
Output: .sdlc/docs/*.html
"""
import os
import re
import sys

SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
DOCS_DIR = os.path.join(os.path.dirname(SCRIPT_DIR), "docs")

# Try to use the 'markdown' library for proper conversion
try:
    import markdown
    from markdown.extensions.tables import TableExtension
    from markdown.extensions.fenced_code import FencedCodeExtension
    from markdown.extensions.toc import TocExtension
    HAS_MARKDOWN = True
except ImportError:
    HAS_MARKDOWN = False


def md_to_html_basic(md_text):
    """Fallback converter when 'markdown' package is not installed."""
    html = md_text

    # Fenced code blocks (```lang ... ```)
    html = re.sub(
        r'```(\w*)\n(.*?)```',
        lambda m: f'<pre><code class="language-{m.group(1)}">{_escape(m.group(2))}</code></pre>',
        html, flags=re.DOTALL
    )

    # Inline code
    html = re.sub(r'`([^`]+)`', r'<code>\1</code>', html)

    # Headers
    html = re.sub(r'^#### (.+)$', r'<h4>\1</h4>', html, flags=re.MULTILINE)
    html = re.sub(r'^### (.+)$', r'<h3>\1</h3>', html, flags=re.MULTILINE)
    html = re.sub(r'^## (.+)$', r'<h2>\1</h2>', html, flags=re.MULTILINE)
    html = re.sub(r'^# (.+)$', r'<h1>\1</h1>', html, flags=re.MULTILINE)

    # Bold and italic
    html = re.sub(r'\*\*(.+?)\*\*', r'<strong>\1</strong>', html)
    html = re.sub(r'\*(.+?)\*', r'<em>\1</em>', html)

    # Links
    html = re.sub(r'\[([^\]]+)\]\(([^)]+)\)', r'<a href="\2">\1</a>', html)

    # Horizontal rules
    html = re.sub(r'^---+$', '<hr>', html, flags=re.MULTILINE)

    # Tables
    html = _convert_tables(html)

    # Lists
    lines = html.split('\n')
    result = []
    in_list = False
    for line in lines:
        stripped = line.strip()
        if stripped.startswith('- ') or stripped.startswith('* '):
            if not in_list:
                result.append('<ul>')
                in_list = True
            content = stripped[2:]
            result.append(f'  <li>{content}</li>')
        elif re.match(r'^\d+\. ', stripped):
            if not in_list:
                result.append('<ol>')
                in_list = True
            content = re.sub(r'^\d+\. ', '', stripped)
            result.append(f'  <li>{content}</li>')
        else:
            if in_list:
                result.append('</ul>' if result[-2].strip().startswith('<ul') or any('<ul>' in r for r in result[-5:]) else '</ol>')
                in_list = False
            result.append(line)
    if in_list:
        result.append('</ul>')
    html = '\n'.join(result)

    # Paragraphs (double newlines)
    html = re.sub(r'\n\n(?!<)', '\n\n<p>', html)

    # Blockquotes
    html = re.sub(r'^> (.+)$', r'<blockquote>\1</blockquote>', html, flags=re.MULTILINE)

    return html


def _escape(text):
    return text.replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;')


def _convert_tables(html):
    """Convert markdown tables to HTML tables."""
    lines = html.split('\n')
    result = []
    i = 0
    while i < len(lines):
        if i + 1 < len(lines) and '|' in lines[i] and re.match(r'^[\s|:-]+$', lines[i + 1]):
            # Found a table
            result.append('<table>')
            # Header
            headers = [c.strip() for c in lines[i].split('|') if c.strip()]
            result.append('<thead><tr>')
            for h in headers:
                result.append(f'<th>{h}</th>')
            result.append('</tr></thead>')
            i += 2  # Skip separator line
            result.append('<tbody>')
            while i < len(lines) and '|' in lines[i] and lines[i].strip():
                cells = [c.strip() for c in lines[i].split('|') if c.strip()]
                result.append('<tr>')
                for c in cells:
                    result.append(f'<td>{c}</td>')
                result.append('</tr>')
                i += 1
            result.append('</tbody></table>')
        else:
            result.append(lines[i])
            i += 1
    return '\n'.join(result)


HTML_TEMPLATE = """<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{title} — SDLC Pipeline Engine</title>
    <style>
        :root {{
            --bg: #0d1117;
            --surface: #161b22;
            --border: #30363d;
            --text: #e6edf3;
            --text-muted: #8b949e;
            --accent: #58a6ff;
            --accent2: #3fb950;
            --code-bg: #1c2128;
            --warn: #d29922;
            --error: #f85149;
        }}

        * {{ margin: 0; padding: 0; box-sizing: border-box; }}

        body {{
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Noto Sans', Helvetica, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.7;
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 24px;
        }}

        h1 {{
            font-size: 2em;
            border-bottom: 1px solid var(--border);
            padding-bottom: 12px;
            margin-bottom: 24px;
            color: var(--text);
        }}

        h2 {{
            font-size: 1.5em;
            margin-top: 40px;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
        }}

        h3 {{
            font-size: 1.25em;
            margin-top: 28px;
            margin-bottom: 12px;
            color: var(--text);
        }}

        h4 {{
            font-size: 1.1em;
            margin-top: 20px;
            margin-bottom: 8px;
            color: var(--text-muted);
        }}

        p {{ margin-bottom: 16px; }}

        a {{
            color: var(--accent);
            text-decoration: none;
        }}
        a:hover {{ text-decoration: underline; }}

        code {{
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
            font-size: 0.875em;
            background: var(--code-bg);
            padding: 2px 6px;
            border-radius: 4px;
            border: 1px solid var(--border);
        }}

        pre {{
            background: var(--code-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 16px;
            overflow-x: auto;
            margin: 16px 0;
        }}

        pre code {{
            background: none;
            border: none;
            padding: 0;
            font-size: 0.85em;
            line-height: 1.5;
        }}

        table {{
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
            font-size: 0.9em;
        }}

        th {{
            background: var(--surface);
            border: 1px solid var(--border);
            padding: 10px 14px;
            text-align: left;
            font-weight: 600;
            color: var(--text);
        }}

        td {{
            border: 1px solid var(--border);
            padding: 8px 14px;
            color: var(--text-muted);
        }}

        tr:hover td {{
            background: var(--surface);
            color: var(--text);
        }}

        ul, ol {{
            margin: 8px 0 16px 24px;
        }}

        li {{
            margin-bottom: 6px;
        }}

        blockquote {{
            border-left: 4px solid var(--accent);
            padding: 8px 16px;
            margin: 16px 0;
            background: var(--surface);
            border-radius: 0 8px 8px 0;
            color: var(--text-muted);
        }}

        hr {{
            border: none;
            border-top: 1px solid var(--border);
            margin: 32px 0;
        }}

        strong {{ color: var(--text); }}

        .nav {{
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 32px;
        }}

        .nav a {{
            margin-right: 16px;
            font-size: 0.9em;
        }}

        .badge {{
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75em;
            font-weight: 600;
        }}

        .badge-ok {{ background: #1a3a1a; color: var(--accent2); }}
        .badge-warn {{ background: #3a2a0a; color: var(--warn); }}
        .badge-fail {{ background: #3a1a1a; color: var(--error); }}

        @media (max-width: 768px) {{
            body {{ padding: 16px; }}
            h1 {{ font-size: 1.5em; }}
            table {{ font-size: 0.8em; }}
        }}

        @media print {{
            body {{ background: white; color: #1a1a1a; max-width: none; }}
            pre {{ background: #f5f5f5; border-color: #ddd; }}
            code {{ background: #f0f0f0; border-color: #ddd; }}
            th {{ background: #f0f0f0; }}
            td {{ color: #333; }}
            a {{ color: #0066cc; }}
        }}
    </style>
</head>
<body>
    <nav class="nav">
        <a href="INSTALLATION.html">Installation</a>
        <a href="QUICK_START.html">Quick Start</a>
        <a href="USER_MANUAL.html">User Manual</a>
        <a href="FAQ.html">FAQ</a>
    </nav>
    {content}
    <hr>
    <p style="color: var(--text-muted); font-size: 0.85em; text-align: center;">
        SDLC Pipeline Engine v3.8.1 &mdash; Logimax Technologies
    </p>
</body>
</html>"""


def convert_file(md_path, html_path):
    """Convert a single markdown file to HTML."""
    with open(md_path, 'r', encoding='utf-8') as f:
        md_text = f.read()

    # Extract title from first H1
    title_match = re.search(r'^# (.+)$', md_text, re.MULTILINE)
    title = title_match.group(1) if title_match else os.path.basename(md_path).replace('.md', '')

    # Convert markdown to HTML
    if HAS_MARKDOWN:
        html_content = markdown.markdown(
            md_text,
            extensions=[
                TableExtension(),
                FencedCodeExtension(),
                TocExtension(permalink=False),
                'md_in_html',
            ]
        )
    else:
        html_content = md_to_html_basic(md_text)

    # Wrap in template
    full_html = HTML_TEMPLATE.format(title=title, content=html_content)

    with open(html_path, 'w', encoding='utf-8') as f:
        f.write(full_html)

    return title


def main():
    if not os.path.isdir(DOCS_DIR):
        print(f"  [FAIL] Docs directory not found: {DOCS_DIR}")
        sys.exit(1)

    if not HAS_MARKDOWN:
        print("  [INFO] 'markdown' package not found, using basic converter")
        print("         For better output: pip install markdown")

    converted = 0
    for filename in os.listdir(DOCS_DIR):
        if filename.endswith('.md'):
            md_path = os.path.join(DOCS_DIR, filename)
            html_path = os.path.join(DOCS_DIR, filename.replace('.md', '.html'))
            title = convert_file(md_path, html_path)
            print(f"  [OK] {filename} -> {filename.replace('.md', '.html')}  ({title})")
            converted += 1

    # Also convert README.md from parent
    readme_path = os.path.join(os.path.dirname(DOCS_DIR), "README.md")
    if os.path.exists(readme_path):
        html_path = os.path.join(DOCS_DIR, "index.html")
        title = convert_file(readme_path, html_path)
        print(f"  [OK] README.md -> index.html  ({title})")
        converted += 1

    print(f"\n  Done! {converted} files converted.")
    if HAS_MARKDOWN:
        print("  Used: python-markdown (full features)")
    else:
        print("  Used: basic converter (install 'markdown' for better output)")


if __name__ == "__main__":
    main()
