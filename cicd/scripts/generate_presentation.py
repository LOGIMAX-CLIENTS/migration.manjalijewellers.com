"""
CI/CD Pipeline Demo Presentation Generator
Creates a professional PPTX for CTO Demo
"""

from pptx import Presentation
from pptx.util import Inches, Pt
from pptx.dml.color import RGBColor
from pptx.enum.text import PP_ALIGN, MSO_ANCHOR
from pptx.enum.shapes import MSO_SHAPE
from pptx.oxml.ns import nsmap
import os

# Colors
DARK_BG = RGBColor(15, 15, 35)  # Dark blue-black
ACCENT = RGBColor(0, 212, 170)  # Teal accent
WHITE = RGBColor(255, 255, 255)
GRAY = RGBColor(150, 150, 160)
GREEN = RGBColor(34, 197, 94)
BLUE = RGBColor(59, 130, 246)
ORANGE = RGBColor(249, 115, 22)
RED = RGBColor(239, 68, 68)

def set_slide_bg(slide, color):
    """Set slide background color"""
    background = slide.background
    fill = background.fill
    fill.solid()
    fill.fore_color.rgb = color

def add_title_slide(prs, title, subtitle=""):
    """Add a title slide"""
    slide = prs.slides.add_slide(prs.slide_layouts[6])  # Blank
    set_slide_bg(slide, DARK_BG)
    
    # Title
    title_box = slide.shapes.add_textbox(Inches(0.5), Inches(2.5), Inches(9), Inches(1.5))
    tf = title_box.text_frame
    p = tf.paragraphs[0]
    p.text = title
    p.font.size = Pt(44)
    p.font.bold = True
    p.font.color.rgb = WHITE
    p.alignment = PP_ALIGN.CENTER
    
    # Subtitle
    if subtitle:
        sub_box = slide.shapes.add_textbox(Inches(0.5), Inches(4), Inches(9), Inches(1))
        tf = sub_box.text_frame
        p = tf.paragraphs[0]
        p.text = subtitle
        p.font.size = Pt(24)
        p.font.color.rgb = ACCENT
        p.alignment = PP_ALIGN.CENTER
    
    return slide

def add_content_slide(prs, title, bullets, highlight_indices=None):
    """Add a content slide with bullets"""
    slide = prs.slides.add_slide(prs.slide_layouts[6])
    set_slide_bg(slide, DARK_BG)
    
    # Title
    title_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.4), Inches(9), Inches(0.8))
    tf = title_box.text_frame
    p = tf.paragraphs[0]
    p.text = title
    p.font.size = Pt(32)
    p.font.bold = True
    p.font.color.rgb = ACCENT
    
    # Accent line
    line = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(0.5), Inches(1.1), Inches(2), Inches(0.05))
    line.fill.solid()
    line.fill.fore_color.rgb = ACCENT
    line.line.fill.background()
    
    # Bullets
    bullet_box = slide.shapes.add_textbox(Inches(0.5), Inches(1.5), Inches(9), Inches(5))
    tf = bullet_box.text_frame
    tf.word_wrap = True
    
    for i, bullet in enumerate(bullets):
        if i == 0:
            p = tf.paragraphs[0]
        else:
            p = tf.add_paragraph()
        
        p.text = f"• {bullet}"
        p.font.size = Pt(22)
        p.font.color.rgb = WHITE if not highlight_indices or i not in highlight_indices else ACCENT
        p.space_after = Pt(12)
    
    return slide

def add_env_diagram(prs):
    """Add environment flow diagram"""
    slide = prs.slides.add_slide(prs.slide_layouts[6])
    set_slide_bg(slide, DARK_BG)
    
    # Title
    title_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.2), Inches(9), Inches(0.6))
    tf = title_box.text_frame
    p = tf.paragraphs[0]
    p.text = "🌍 4 Deployment Environments (Source Version)"
    p.font.size = Pt(28)
    p.font.bold = True
    p.font.color.rgb = ACCENT
    
    # Environment boxes with descriptions
    envs = [
        ("DEVELOP", "test_etail_v3", "Retail_1.1.1.0001", "Dev Team", BLUE, "🔧"),
        ("QA", "QA", "QA", "Code & Functional Testing", ORANGE, "🧪"),
        ("SUPPORT", "Support", "support", "Client-based Testing", GRAY, "🔧"),
        ("PRODUCTION", "etail_v3", "Production", "Demo, Training & New Clients", GREEN, "🚀")
    ]
    
    y_pos = 0.9
    box_width = 2.2
    spacing = 0.18
    
    for i, (name, folder, branch, desc, color, emoji) in enumerate(envs):
        x_pos = 0.3 + i * (box_width + spacing)
        
        # Box
        box = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, 
                                      Inches(x_pos), Inches(y_pos), 
                                      Inches(box_width), Inches(2.0))
        box.fill.solid()
        box.fill.fore_color.rgb = color
        box.line.fill.background()
        
        # Text in box
        tf = box.text_frame
        tf.paragraphs[0].text = f"{emoji} {name}"
        tf.paragraphs[0].font.size = Pt(15)
        tf.paragraphs[0].font.bold = True
        tf.paragraphs[0].font.color.rgb = WHITE
        tf.paragraphs[0].alignment = PP_ALIGN.CENTER
        
        p = tf.add_paragraph()
        p.text = desc
        p.font.size = Pt(10)
        p.font.color.rgb = WHITE
        p.alignment = PP_ALIGN.CENTER
        
        p = tf.add_paragraph()
        p.text = f"📁 {folder}"
        p.font.size = Pt(10)
        p.font.color.rgb = WHITE
        p.alignment = PP_ALIGN.CENTER
        
        p = tf.add_paragraph()
        p.text = f"🔀 {branch}"
        p.font.size = Pt(10)
        p.font.color.rgb = WHITE
        p.alignment = PP_ALIGN.CENTER
        
        tf.paragraphs[0].space_before = Pt(10)
    
    # Flow arrows
    arrow_y = y_pos + 2.15
    for i in range(3):
        x = 0.3 + (i + 1) * (box_width + spacing) - 0.25
        arrow = slide.shapes.add_shape(MSO_SHAPE.RIGHT_ARROW, 
                                        Inches(x - 0.1), Inches(arrow_y), 
                                        Inches(0.4), Inches(0.25))
        arrow.fill.solid()
        arrow.fill.fore_color.rgb = ACCENT
        arrow.line.fill.background()
    
    # Team responsibilities
    team_box = slide.shapes.add_textbox(Inches(0.3), Inches(3.5), Inches(9.4), Inches(2))
    tf = team_box.text_frame
    
    p = tf.paragraphs[0]
    p.text = "� Team Responsibilities:"
    p.font.size = Pt(16)
    p.font.bold = True
    p.font.color.rgb = ACCENT
    
    teams = [
        "• DEVELOP: Development team builds & tests new features",
        "• QA: QA team performs code review & functional testing",
        "• SUPPORT: Support & Implementation team does client-based testing",
        "• PRODUCTION: Stable code for demos, training & new client deployments"
    ]
    
    for team in teams:
        p = tf.add_paragraph()
        p.text = team
        p.font.size = Pt(13)
        p.font.color.rgb = WHITE
    
    return slide

def add_git_flow_slide(prs):
    """Add Git branching strategy"""
    slide = prs.slides.add_slide(prs.slide_layouts[6])
    set_slide_bg(slide, DARK_BG)
    
    # Title
    title_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.3), Inches(9), Inches(0.8))
    tf = title_box.text_frame
    p = tf.paragraphs[0]
    p.text = "🔀 Feature Branch Workflow"
    p.font.size = Pt(32)
    p.font.bold = True
    p.font.color.rgb = ACCENT
    
    # Flow description
    steps_box = slide.shapes.add_textbox(Inches(0.5), Inches(1.2), Inches(9), Inches(5))
    tf = steps_box.text_frame
    
    steps = [
        ("1️⃣", "Create Feature Branch", "git checkout -b feature/new-billing-module"),
        ("2️⃣", "Make Changes & Commit", "git add . && git commit -m 'Add billing feature'"),
        ("3️⃣", "Push to Remote", "git push origin feature/new-billing-module"),
        ("4️⃣", "Create Pull Request", "GitHub → New PR → Retail_1.1.1.0001"),
        ("5️⃣", "Code Review & Merge", "Reviewer approves → Merge to develop"),
        ("6️⃣", "Auto Deploy to Develop", "Webhook triggers → test_etail_v3 updated"),
    ]
    
    for i, (emoji, title, cmd) in enumerate(steps):
        if i == 0:
            p = tf.paragraphs[0]
        else:
            p = tf.add_paragraph()
        
        p.text = f"{emoji}  {title}"
        p.font.size = Pt(18)
        p.font.bold = True
        p.font.color.rgb = WHITE
        p.space_before = Pt(8)
        
        p = tf.add_paragraph()
        p.text = f"      {cmd}"
        p.font.size = Pt(14)
        p.font.color.rgb = GRAY
        p.space_after = Pt(6)
    
    return slide

def add_naming_conventions(prs):
    """Add branch naming conventions"""
    bullets = [
        "feature/description → New features (feature/add-gst-report)",
        "bugfix/description → Bug fixes (bugfix/fix-login-error)",
        "hotfix/description → Urgent production fixes",
        "release/version → Release preparation (release/v1.2.0)",
        "Always use lowercase with hyphens",
        "Keep names short but descriptive"
    ]
    return add_content_slide(prs, "📝 Branch Naming Conventions", bullets, [0, 1, 2, 3])

def add_commit_guidelines(prs):
    """Add commit message guidelines"""
    bullets = [
        "Use present tense: 'Add feature' not 'Added feature'",
        "First line: brief summary (50 chars max)",
        "Format: type(scope): description",
        "Types: feat, fix, docs, style, refactor, test",
        "Example: feat(billing): add GST calculation module",
        "Example: fix(auth): resolve session timeout issue"
    ]
    return add_content_slide(prs, "💬 Commit Message Guidelines", bullets, [2, 4, 5])

def add_cicd_flow(prs):
    """Add CI/CD pipeline flow"""
    slide = prs.slides.add_slide(prs.slide_layouts[6])
    set_slide_bg(slide, DARK_BG)
    
    # Title
    title_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.3), Inches(9), Inches(0.8))
    tf = title_box.text_frame
    p = tf.paragraphs[0]
    p.text = "⚙️ CI/CD Pipeline Flow"
    p.font.size = Pt(32)
    p.font.bold = True
    p.font.color.rgb = ACCENT
    
    # Pipeline steps
    steps = [
        ("👨‍💻 Developer", "Push code", BLUE),
        ("🐙 GitHub", "Trigger workflow", GRAY),
        ("📡 Webhook", "Notify server", ORANGE),
        ("🖥️ Server", "Deploy code", GREEN),
        ("📧 Notify", "Send email", ACCENT)
    ]
    
    box_width = 1.5
    y_pos = 1.5
    
    for i, (title, desc, color) in enumerate(steps):
        x_pos = 0.3 + i * (box_width + 0.3)
        
        box = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, 
                                      Inches(x_pos), Inches(y_pos), 
                                      Inches(box_width), Inches(1.2))
        box.fill.solid()
        box.fill.fore_color.rgb = color
        box.line.fill.background()
        
        tf = box.text_frame
        tf.paragraphs[0].text = title
        tf.paragraphs[0].font.size = Pt(14)
        tf.paragraphs[0].font.bold = True
        tf.paragraphs[0].font.color.rgb = WHITE
        tf.paragraphs[0].alignment = PP_ALIGN.CENTER
        
        p = tf.add_paragraph()
        p.text = desc
        p.font.size = Pt(11)
        p.font.color.rgb = WHITE
        p.alignment = PP_ALIGN.CENTER
        
        tf.paragraphs[0].space_before = Pt(15)
        
        # Arrow
        if i < len(steps) - 1:
            arrow = slide.shapes.add_shape(MSO_SHAPE.RIGHT_ARROW, 
                                            Inches(x_pos + box_width + 0.05), Inches(y_pos + 0.5), 
                                            Inches(0.25), Inches(0.2))
            arrow.fill.solid()
            arrow.fill.fore_color.rgb = ACCENT
            arrow.line.fill.background()
    
    # Details
    details_box = slide.shapes.add_textbox(Inches(0.5), Inches(3.0), Inches(9), Inches(2.5))
    tf = details_box.text_frame
    
    details = [
        "✅ Automatic deployment on push",
        "✅ Environment-specific configuration",
        "✅ Email notifications on success/failure",
        "✅ Deployment history & logging",
        "✅ Zero-downtime production releases"
    ]
    
    for i, detail in enumerate(details):
        if i == 0:
            p = tf.paragraphs[0]
        else:
            p = tf.add_paragraph()
        p.text = detail
        p.font.size = Pt(18)
        p.font.color.rgb = WHITE
        p.space_after = Pt(8)
    
    return slide

def add_dashboard_slide(prs):
    """Add CI/CD Dashboard info"""
    bullets = [
        "📊 Real-time deployment status for all environments",
        "📜 Complete deployment history with commit details",
        "🔔 Client setup wizard for new projects",
        "📈 Success/failure statistics",
        "🔗 URL: retail.logimaxindia.com/test_etail_v3/cicd/docs/"
    ]
    return add_content_slide(prs, "🖥️ CI/CD Dashboard", bullets, [4])

def add_symlink_slide(prs):
    """Add symlink deployment explanation"""
    slide = prs.slides.add_slide(prs.slide_layouts[6])
    set_slide_bg(slide, DARK_BG)
    
    # Title
    title_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.3), Inches(9), Inches(0.8))
    tf = title_box.text_frame
    p = tf.paragraphs[0]
    p.text = "🔗 Symlink Deployment (Production)"
    p.font.size = Pt(32)
    p.font.bold = True
    p.font.color.rgb = ACCENT
    
    # Structure
    structure_box = slide.shapes.add_textbox(Inches(0.5), Inches(1.2), Inches(4.5), Inches(4))
    tf = structure_box.text_frame
    
    tf.paragraphs[0].text = "📁 Directory Structure"
    tf.paragraphs[0].font.size = Pt(18)
    tf.paragraphs[0].font.bold = True
    tf.paragraphs[0].font.color.rgb = ACCENT
    
    structure = """
etail_v3/
├── admin → releases/current
├── releases/
│   ├── release_20260112/
│   ├── release_20260119/
│   └── current → release_20260119
└── shared/
    ├── database.php
    ├── .htaccess
    └── assets/
"""
    
    for line in structure.strip().split('\n'):
        p = tf.add_paragraph()
        p.text = line
        p.font.size = Pt(14)
        p.font.color.rgb = WHITE
        p.font.name = "Consolas"
    
    # Benefits
    benefits_box = slide.shapes.add_textbox(Inches(5.2), Inches(1.2), Inches(4.3), Inches(4))
    tf = benefits_box.text_frame
    
    tf.paragraphs[0].text = "✨ Benefits"
    tf.paragraphs[0].font.size = Pt(18)
    tf.paragraphs[0].font.bold = True
    tf.paragraphs[0].font.color.rgb = ACCENT
    
    benefits = [
        "⚡ Zero-downtime deployment",
        "↩️ Instant rollback capability",
        "📦 Clean release isolation",
        "🔄 Atomic switch via symlink",
        "📂 Shared config & assets"
    ]
    
    for benefit in benefits:
        p = tf.add_paragraph()
        p.text = benefit
        p.font.size = Pt(16)
        p.font.color.rgb = WHITE
        p.space_after = Pt(10)
    
    return slide

def add_example_intro(prs):
    """Add intro slide for the 5-commit example"""
    bullets = [
        "Real-world scenario: Adding GST Report Feature",
        "Developer: Priya (Junior Developer)",
        "Reviewer: Ravi (Senior Developer)",
        "5 commits showing the complete journey",
        "Includes: success, rejection, rework, and merge"
    ]
    return add_content_slide(prs, "📚 Real-World Example: 5 Commits Journey", bullets, [0])

def add_commit_1_slide(prs):
    """Commit 1: Initial feature branch"""
    slide = prs.slides.add_slide(prs.slide_layouts[6])
    set_slide_bg(slide, DARK_BG)
    
    # Title
    title_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.2), Inches(9), Inches(0.6))
    tf = title_box.text_frame
    p = tf.paragraphs[0]
    p.text = "📝 Commit 1: Create Feature Branch & Initial Work"
    p.font.size = Pt(26)
    p.font.bold = True
    p.font.color.rgb = ACCENT
    
    # Content
    content_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.9), Inches(9), Inches(4.5))
    tf = content_box.text_frame
    
    steps = [
        ("👩‍💻 Priya starts the task", "", WHITE),
        ("", "git checkout Retail_1.1.1.0001", GRAY),
        ("", "git pull origin Retail_1.1.1.0001", GRAY),
        ("", "git checkout -b feature/gst-report", GRAY),
        ("", "", WHITE),
        ("📝 Makes initial changes & commits", "", WHITE),
        ("", "# Edit files: gst_report.php, report_model.php", GRAY),
        ("", "git add .", GRAY),
        ("", "git commit -m 'feat(reports): add basic GST report structure'", ACCENT),
        ("", "", WHITE),
        ("🚀 Pushes to remote", "", WHITE),
        ("", "git push origin feature/gst-report", GRAY),
    ]
    
    for label, cmd, color in steps:
        p = tf.add_paragraph()
        if label:
            p.text = label
            p.font.size = Pt(16)
            p.font.bold = True
            p.font.color.rgb = color
        else:
            p.text = f"    {cmd}"
            p.font.size = Pt(13)
            p.font.color.rgb = color
            p.font.name = "Consolas"
    
    # Status box
    status_box = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, 
                                         Inches(7), Inches(4.5), 
                                         Inches(2.8), Inches(0.8))
    status_box.fill.solid()
    status_box.fill.fore_color.rgb = BLUE
    status_box.line.fill.background()
    tf = status_box.text_frame
    tf.paragraphs[0].text = "📋 Status: In Progress"
    tf.paragraphs[0].font.size = Pt(14)
    tf.paragraphs[0].font.color.rgb = WHITE
    tf.paragraphs[0].alignment = PP_ALIGN.CENTER
    
    return slide

def add_commit_2_slide(prs):
    """Commit 2: Create PR"""
    slide = prs.slides.add_slide(prs.slide_layouts[6])
    set_slide_bg(slide, DARK_BG)
    
    # Title
    title_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.2), Inches(9), Inches(0.6))
    tf = title_box.text_frame
    p = tf.paragraphs[0]
    p.text = "📝 Commit 2: Create Pull Request"
    p.font.size = Pt(26)
    p.font.bold = True
    p.font.color.rgb = ACCENT
    
    # Content
    content_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.9), Inches(9), Inches(4.5))
    tf = content_box.text_frame
    
    steps = [
        ("👩‍💻 Priya creates a Pull Request on GitHub", "", WHITE),
        ("", "• Go to: github.com/Logimax-Technologies/etail_development_src", GRAY),
        ("", "• Click 'Compare & pull request'", GRAY),
        ("", "• Base: Retail_1.1.1.0001 ← Compare: feature/gst-report", GRAY),
        ("", "", WHITE),
        ("📋 PR Details:", "", WHITE),
        ("", "Title: feat(reports): Add GST Report Module", ACCENT),
        ("", "Description:", GRAY),
        ("", "  - Added new GST report generation", GRAY),
        ("", "  - Shows tax breakdown by category", GRAY),
        ("", "  - Includes date range filter", GRAY),
        ("", "", WHITE),
        ("👨‍💼 Assigns Ravi as reviewer", "", WHITE),
    ]
    
    for label, cmd, color in steps:
        p = tf.add_paragraph()
        if label:
            p.text = label
            p.font.size = Pt(15)
            p.font.bold = True
            p.font.color.rgb = color
        else:
            p.text = f"    {cmd}"
            p.font.size = Pt(13)
            p.font.color.rgb = color
    
    # Status box
    status_box = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, 
                                         Inches(7), Inches(4.5), 
                                         Inches(2.8), Inches(0.8))
    status_box.fill.solid()
    status_box.fill.fore_color.rgb = ORANGE
    status_box.line.fill.background()
    tf = status_box.text_frame
    tf.paragraphs[0].text = "⏳ Status: Pending Review"
    tf.paragraphs[0].font.size = Pt(14)
    tf.paragraphs[0].font.color.rgb = WHITE
    tf.paragraphs[0].alignment = PP_ALIGN.CENTER
    
    return slide

def add_commit_3_slide(prs):
    """Commit 3: PR Rejected with feedback"""
    slide = prs.slides.add_slide(prs.slide_layouts[6])
    set_slide_bg(slide, DARK_BG)
    
    # Title
    title_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.2), Inches(9), Inches(0.6))
    tf = title_box.text_frame
    p = tf.paragraphs[0]
    p.text = "❌ Commit 3: PR Rejected - Changes Requested"
    p.font.size = Pt(26)
    p.font.bold = True
    p.font.color.rgb = RED
    
    # Content
    content_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.9), Inches(9), Inches(4.5))
    tf = content_box.text_frame
    
    steps = [
        ("👨‍💼 Ravi reviews and requests changes:", "", WHITE),
        ("", "", WHITE),
        ("💬 Review Comments:", "", ORANGE),
        ("", "1. 'Missing input validation on date range'", GRAY),
        ("", "2. 'SQL query vulnerable to injection - use parameterized queries'", GRAY),
        ("", "3. 'Add pagination for large datasets'", GRAY),
        ("", "4. 'Follow naming convention: getGstReport() not get_gst_report()'", GRAY),
        ("", "", WHITE),
        ("📋 Ravi's feedback:", "", WHITE),
        ("", "'Good start! Fix these issues and request re-review.'", ACCENT),
        ("", "", WHITE),
        ("⚠️ PR Status: Changes Requested", "", RED),
    ]
    
    for label, cmd, color in steps:
        p = tf.add_paragraph()
        if label:
            p.text = label
            p.font.size = Pt(15)
            p.font.bold = True
            p.font.color.rgb = color
        else:
            p.text = f"    {cmd}"
            p.font.size = Pt(13)
            p.font.color.rgb = color
    
    # Status box
    status_box = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, 
                                         Inches(7), Inches(4.5), 
                                         Inches(2.8), Inches(0.8))
    status_box.fill.solid()
    status_box.fill.fore_color.rgb = RED
    status_box.line.fill.background()
    tf = status_box.text_frame
    tf.paragraphs[0].text = "❌ Status: Rejected"
    tf.paragraphs[0].font.size = Pt(14)
    tf.paragraphs[0].font.color.rgb = WHITE
    tf.paragraphs[0].alignment = PP_ALIGN.CENTER
    
    return slide

def add_commit_4_slide(prs):
    """Commit 4: Rework and fix issues"""
    slide = prs.slides.add_slide(prs.slide_layouts[6])
    set_slide_bg(slide, DARK_BG)
    
    # Title
    title_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.2), Inches(9), Inches(0.6))
    tf = title_box.text_frame
    p = tf.paragraphs[0]
    p.text = "🔧 Commit 4: Rework - Fix All Issues"
    p.font.size = Pt(26)
    p.font.bold = True
    p.font.color.rgb = ORANGE
    
    # Content
    content_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.9), Inches(9), Inches(4.5))
    tf = content_box.text_frame
    
    steps = [
        ("👩‍💻 Priya addresses all feedback:", "", WHITE),
        ("", "", WHITE),
        ("✅ Fix 1: Add input validation", "", GREEN),
        ("", "git commit -m 'fix(reports): add date range validation'", GRAY),
        ("", "", WHITE),
        ("✅ Fix 2: Use parameterized queries", "", GREEN),
        ("", "git commit -m 'fix(reports): use prepared statements for SQL'", GRAY),
        ("", "", WHITE),
        ("✅ Fix 3: Add pagination", "", GREEN),
        ("", "git commit -m 'feat(reports): add pagination for GST report'", GRAY),
        ("", "", WHITE),
        ("✅ Fix 4: Fix naming convention", "", GREEN),
        ("", "git commit -m 'refactor(reports): rename to camelCase methods'", GRAY),
        ("", "", WHITE),
        ("🚀 Push all fixes", "", WHITE),
        ("", "git push origin feature/gst-report", ACCENT),
    ]
    
    for label, cmd, color in steps:
        p = tf.add_paragraph()
        if label:
            p.text = label
            p.font.size = Pt(14)
            p.font.bold = True
            p.font.color.rgb = color
        else:
            p.text = f"    {cmd}"
            p.font.size = Pt(12)
            p.font.color.rgb = color
            p.font.name = "Consolas"
    
    # Status box
    status_box = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, 
                                         Inches(7), Inches(4.5), 
                                         Inches(2.8), Inches(0.8))
    status_box.fill.solid()
    status_box.fill.fore_color.rgb = ORANGE
    status_box.line.fill.background()
    tf = status_box.text_frame
    tf.paragraphs[0].text = "🔄 Status: Re-review"
    tf.paragraphs[0].font.size = Pt(14)
    tf.paragraphs[0].font.color.rgb = WHITE
    tf.paragraphs[0].alignment = PP_ALIGN.CENTER
    
    return slide

def add_commit_5_slide(prs):
    """Commit 5: Approved and merged"""
    slide = prs.slides.add_slide(prs.slide_layouts[6])
    set_slide_bg(slide, DARK_BG)
    
    # Title
    title_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.2), Inches(9), Inches(0.6))
    tf = title_box.text_frame
    p = tf.paragraphs[0]
    p.text = "✅ Commit 5: Approved & Merged to Develop"
    p.font.size = Pt(26)
    p.font.bold = True
    p.font.color.rgb = GREEN
    
    # Content
    content_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.9), Inches(4.5), Inches(4.5))
    tf = content_box.text_frame
    
    steps = [
        ("👨‍💼 Ravi approves the PR:", "", WHITE),
        ("", "'LGTM! All issues fixed. Approved.'", ACCENT),
        ("", "", WHITE),
        ("🔀 Merge to Retail_1.1.1.0001", "", WHITE),
        ("", "Click 'Merge Pull Request'", GRAY),
        ("", "Select 'Squash and merge'", GRAY),
        ("", "", WHITE),
        ("⚡ Auto Deploy Triggered:", "", WHITE),
        ("", "✓ Webhook received", GREEN),
        ("", "✓ Deploy to test_etail_v3", GREEN),
        ("", "✓ Email notification sent", GREEN),
        ("", "", WHITE),
        ("🧹 Cleanup:", "", WHITE),
        ("", "git checkout Retail_1.1.1.0001", GRAY),
        ("", "git pull origin Retail_1.1.1.0001", GRAY),
        ("", "git branch -d feature/gst-report", GRAY),
    ]
    
    for label, cmd, color in steps:
        p = tf.add_paragraph()
        if label:
            p.text = label
            p.font.size = Pt(14)
            p.font.bold = True
            p.font.color.rgb = color
        else:
            p.text = f"  {cmd}"
            p.font.size = Pt(12)
            p.font.color.rgb = color
    
    # Timeline on right side
    timeline_box = slide.shapes.add_textbox(Inches(5.2), Inches(0.9), Inches(4.5), Inches(4))
    tf = timeline_box.text_frame
    
    tf.paragraphs[0].text = "📊 Deployment Path"
    tf.paragraphs[0].font.size = Pt(16)
    tf.paragraphs[0].font.bold = True
    tf.paragraphs[0].font.color.rgb = ACCENT
    
    path = [
        ("1. Develop", "test_etail_v3 ✓", GREEN),
        ("2. QA Testing", "QA branch (next)", ORANGE),
        ("3. Support", "support branch", GRAY),
        ("4. Production", "Production branch", BLUE),
    ]
    
    for stage, desc, color in path:
        p = tf.add_paragraph()
        p.text = f"{stage}"
        p.font.size = Pt(14)
        p.font.bold = True
        p.font.color.rgb = color
        p.space_before = Pt(10)
        
        p = tf.add_paragraph()
        p.text = f"   → {desc}"
        p.font.size = Pt(12)
        p.font.color.rgb = GRAY
    
    # Status box
    status_box = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, 
                                         Inches(7), Inches(4.5), 
                                         Inches(2.8), Inches(0.8))
    status_box.fill.solid()
    status_box.fill.fore_color.rgb = GREEN
    status_box.line.fill.background()
    tf = status_box.text_frame
    tf.paragraphs[0].text = "✅ Status: Deployed"
    tf.paragraphs[0].font.size = Pt(14)
    tf.paragraphs[0].font.color.rgb = WHITE
    tf.paragraphs[0].alignment = PP_ALIGN.CENTER
    
    return slide

def add_promotion_flow_slide(prs):
    """Show how code moves from Develop to Production"""
    slide = prs.slides.add_slide(prs.slide_layouts[6])
    set_slide_bg(slide, DARK_BG)
    
    # Title
    title_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.2), Inches(9), Inches(0.6))
    tf = title_box.text_frame
    p = tf.paragraphs[0]
    p.text = "🚀 Promoting Code to Production"
    p.font.size = Pt(28)
    p.font.bold = True
    p.font.color.rgb = ACCENT
    
    # Flow boxes
    stages = [
        ("DEVELOP", "Retail_1.1.1.0001", "Dev Team\nFeature development", BLUE),
        ("QA", "QA", "Code & Functional\nTesting", ORANGE),
        ("SUPPORT", "support", "Client-based\nTesting", GRAY),
        ("PRODUCTION", "Production", "Stable: Demo,\nTraining, New Clients", GREEN),
    ]
    
    box_width = 2.0
    y_pos = 1.0
    
    for i, (name, branch, desc, color) in enumerate(stages):
        x_pos = 0.5 + i * (box_width + 0.4)
        
        box = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, 
                                      Inches(x_pos), Inches(y_pos), 
                                      Inches(box_width), Inches(1.5))
        box.fill.solid()
        box.fill.fore_color.rgb = color
        box.line.fill.background()
        
        tf = box.text_frame
        tf.paragraphs[0].text = name
        tf.paragraphs[0].font.size = Pt(16)
        tf.paragraphs[0].font.bold = True
        tf.paragraphs[0].font.color.rgb = WHITE
        tf.paragraphs[0].alignment = PP_ALIGN.CENTER
        
        for line in desc.split('\n'):
            p = tf.add_paragraph()
            p.text = line
            p.font.size = Pt(11)
            p.font.color.rgb = WHITE
            p.alignment = PP_ALIGN.CENTER
        
        # Arrow
        if i < len(stages) - 1:
            arrow = slide.shapes.add_shape(MSO_SHAPE.RIGHT_ARROW, 
                                            Inches(x_pos + box_width + 0.05), Inches(y_pos + 0.65), 
                                            Inches(0.35), Inches(0.2))
            arrow.fill.solid()
            arrow.fill.fore_color.rgb = ACCENT
            arrow.line.fill.background()
    
    # Merge commands
    cmd_box = slide.shapes.add_textbox(Inches(0.5), Inches(2.8), Inches(9), Inches(2.5))
    tf = cmd_box.text_frame
    
    tf.paragraphs[0].text = "🔀 Promotion Commands:"
    tf.paragraphs[0].font.size = Pt(18)
    tf.paragraphs[0].font.bold = True
    tf.paragraphs[0].font.color.rgb = ACCENT
    
    commands = [
        ("Develop → QA:", "git checkout QA && git merge Retail_1.1.1.0001 && git push"),
        ("QA → Support:", "git checkout support && git merge QA && git push"),
        ("Support → Production:", "git checkout Production && git merge support && git push"),
    ]
    
    for label, cmd in commands:
        p = tf.add_paragraph()
        p.text = label
        p.font.size = Pt(14)
        p.font.bold = True
        p.font.color.rgb = WHITE
        p.space_before = Pt(8)
        
        p = tf.add_paragraph()
        p.text = f"  {cmd}"
        p.font.size = Pt(12)
        p.font.color.rgb = GRAY
        p.font.name = "Consolas"
    
    return slide

def add_workflow_summary(prs):
    """Summary of the complete workflow"""
    slide = prs.slides.add_slide(prs.slide_layouts[6])
    set_slide_bg(slide, DARK_BG)
    
    # Title
    title_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.2), Inches(9), Inches(0.6))
    tf = title_box.text_frame
    p = tf.paragraphs[0]
    p.text = "📋 Workflow Summary"
    p.font.size = Pt(28)
    p.font.bold = True
    p.font.color.rgb = ACCENT
    
    # Summary
    summary_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.9), Inches(9), Inches(4.5))
    tf = summary_box.text_frame
    
    items = [
        ("1️⃣", "Create feature branch from Retail_1.1.1.0001", WHITE),
        ("2️⃣", "Make commits with proper messages (feat, fix, refactor)", WHITE),
        ("3️⃣", "Push and create Pull Request", WHITE),
        ("4️⃣", "Address reviewer feedback (may need multiple iterations)", ORANGE),
        ("5️⃣", "Once approved, merge to Retail_1.1.1.0001", GREEN),
        ("6️⃣", "Auto-deploy to test_etail_v3 (Develop)", BLUE),
        ("7️⃣", "After QA testing, promote to QA branch", WHITE),
        ("8️⃣", "Client approval, promote to Support", WHITE),
        ("9️⃣", "Release to Production (symlink deployment)", GREEN),
        ("🔟", "Delete feature branch after merge", GRAY),
    ]
    
    for emoji, text, color in items:
        p = tf.add_paragraph()
        p.text = f"{emoji}  {text}"
        p.font.size = Pt(16)
        p.font.color.rgb = color
        p.space_after = Pt(6)
    
    return slide

def add_future_steps(prs):
    """Add future improvements"""
    bullets = [
        "🗃️ SQL schema migration automation",
        "🧪 Automated testing before deployment",
        "📊 Performance monitoring integration",
        "🔔 Slack/Teams notifications",
        "🐳 Docker containerization (future)",
        "☁️ Cloud deployment options (AWS/GCP)"
    ]
    return add_content_slide(prs, "🚀 Future Roadmap", bullets)

def add_quick_reference(prs):
    """Add quick reference commands"""
    slide = prs.slides.add_slide(prs.slide_layouts[6])
    set_slide_bg(slide, DARK_BG)
    
    # Title
    title_box = slide.shapes.add_textbox(Inches(0.5), Inches(0.3), Inches(9), Inches(0.6))
    tf = title_box.text_frame
    p = tf.paragraphs[0]
    p.text = "📋 Quick Reference Commands"
    p.font.size = Pt(28)
    p.font.bold = True
    p.font.color.rgb = ACCENT
    
    # Commands
    commands_box = slide.shapes.add_textbox(Inches(0.5), Inches(1.0), Inches(9), Inches(4.5))
    tf = commands_box.text_frame
    
    commands = [
        ("Create feature branch", "git checkout -b feature/your-feature"),
        ("Switch branches", "git checkout Retail_1.1.1.0001"),
        ("Pull latest changes", "git pull origin Retail_1.1.1.0001"),
        ("Push your changes", "git push origin feature/your-feature"),
        ("Check status", "git status"),
        ("View commit history", "git log --oneline -10"),
    ]
    
    for i, (desc, cmd) in enumerate(commands):
        if i == 0:
            p = tf.paragraphs[0]
        else:
            p = tf.add_paragraph()
        
        p.text = desc
        p.font.size = Pt(16)
        p.font.bold = True
        p.font.color.rgb = WHITE
        p.space_before = Pt(10)
        
        p = tf.add_paragraph()
        p.text = f"  {cmd}"
        p.font.size = Pt(14)
        p.font.color.rgb = ACCENT
        p.font.name = "Consolas"
    
    return slide

def add_thank_you(prs):
    """Add closing slide"""
    slide = prs.slides.add_slide(prs.slide_layouts[6])
    set_slide_bg(slide, DARK_BG)
    
    # Thank you
    title_box = slide.shapes.add_textbox(Inches(0.5), Inches(2), Inches(9), Inches(1.5))
    tf = title_box.text_frame
    p = tf.paragraphs[0]
    p.text = "🙏 Thank You!"
    p.font.size = Pt(54)
    p.font.bold = True
    p.font.color.rgb = ACCENT
    p.alignment = PP_ALIGN.CENTER
    
    # Questions
    q_box = slide.shapes.add_textbox(Inches(0.5), Inches(3.5), Inches(9), Inches(0.8))
    tf = q_box.text_frame
    p = tf.paragraphs[0]
    p.text = "Questions & Discussion"
    p.font.size = Pt(28)
    p.font.color.rgb = WHITE
    p.alignment = PP_ALIGN.CENTER
    
    # Contact
    contact_box = slide.shapes.add_textbox(Inches(0.5), Inches(4.8), Inches(9), Inches(0.5))
    tf = contact_box.text_frame
    p = tf.paragraphs[0]
    p.text = "CI/CD Dashboard: retail.logimaxindia.com/test_etail_v3/cicd/docs/"
    p.font.size = Pt(14)
    p.font.color.rgb = GRAY
    p.alignment = PP_ALIGN.CENTER
    
    return slide


def create_presentation():
    """Create the full presentation"""
    prs = Presentation()
    prs.slide_width = Inches(10)
    prs.slide_height = Inches(5.625)  # 16:9
    
    # Slides
    add_title_slide(prs, "🚀 CI/CD Pipeline & Git Workflow", "Logimax Retail - Developer Guide")
    
    add_content_slide(prs, "📋 Agenda", [
        "Understanding 4 Deployment Environments",
        "Feature Branch Workflow",
        "Real-World Example: 5 Commits Journey",
        "Commit Guidelines & Best Practices",
        "CI/CD Pipeline Flow",
        "Promoting Code to Production",
        "Symlink Deployment",
        "CI/CD Dashboard & Future Roadmap"
    ])
    
    # Part 1: Environment Overview
    add_env_diagram(prs)
    add_git_flow_slide(prs)
    add_naming_conventions(prs)
    add_commit_guidelines(prs)
    
    # Part 2: Real-World 5 Commit Example
    add_example_intro(prs)
    add_commit_1_slide(prs)
    add_commit_2_slide(prs)
    add_commit_3_slide(prs)
    add_commit_4_slide(prs)
    add_commit_5_slide(prs)
    
    # Part 3: Deployment Flow
    add_promotion_flow_slide(prs)
    add_workflow_summary(prs)
    add_cicd_flow(prs)
    add_symlink_slide(prs)
    
    # Part 4: Tools & Future
    add_dashboard_slide(prs)
    add_future_steps(prs)
    add_quick_reference(prs)
    add_thank_you(prs)
    
    # Save
    output_path = os.path.join(os.path.dirname(__file__), "..", "cicd_demo_presentation.pptx")
    prs.save(output_path)
    print(f"✅ Presentation saved to: {output_path}")
    print(f"📊 Total slides: {len(prs.slides)}")
    return output_path


if __name__ == "__main__":
    create_presentation()
