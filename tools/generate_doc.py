#!/usr/bin/env python3
"""
GYRS Portal - Comprehensive Project Documentation & Architecture Generator
Generates a complete Microsoft Word (.docx) document using Python's standard library (zipfile & OpenXML).
"""

import os
import zipfile
import html
from datetime import datetime

def xml_escape(text):
    if text is None:
        return ""
    return (str(text)
            .replace("&", "&amp;")
            .replace("<", "&lt;")
            .replace(">", "&gt;")
            .replace('"', "&quot;")
            .replace("'", "&apos;"))

class DocxBuilder:
    def __init__(self):
        self.body_elements = []

    def add_title(self, text, subtitle=None):
        p = f'''<w:p>
          <w:pPr>
            <w:jc w:val="center"/>
            <w:spacing w:before="360" w:after="120"/>
          </w:pPr>
          <w:r>
            <w:rPr>
              <w:rFonts w:ascii="Plus Jakarta Sans" w:hAnsi="Plus Jakarta Sans"/>
              <w:b/>
              <w:sz w:val="52"/>
              <w:color w:val="0D6EFD"/>
            </w:rPr>
            <w:t>{xml_escape(text)}</w:t>
          </w:r>
        </w:p>'''
        self.body_elements.append(p)

        if subtitle:
            p_sub = f'''<w:p>
              <w:pPr>
                <w:jc w:val="center"/>
                <w:spacing w:before="60" w:after="360"/>
              </w:pPr>
              <w:r>
                <w:rPr>
                  <w:rFonts w:ascii="Inter" w:hAnsi="Inter"/>
                  <w:sz w:val="26"/>
                  <w:color w:val="495057"/>
                </w:rPr>
                <w:t>{xml_escape(subtitle)}</w:t>
              </w:r>
            </w:p>'''
            self.body_elements.append(p_sub)

    def add_heading_1(self, text):
        p = f'''<w:p>
          <w:pPr>
            <w:spacing w:before="400" w:after="160"/>
            <w:pBdr>
              <w:bottom w:val="single" w:sz="12" w:space="4" w:color="0D6EFD"/>
            </w:pBdr>
          </w:pPr>
          <w:r>
            <w:rPr>
              <w:rFonts w:ascii="Plus Jakarta Sans" w:hAnsi="Plus Jakarta Sans"/>
              <w:b/>
              <w:sz w:val="34"/>
              <w:color w:val="0D6EFD"/>
            </w:rPr>
            <w:t>{xml_escape(text)}</w:t>
          </w:r>
        </w:p>'''
        self.body_elements.append(p)

    def add_heading_2(self, text):
        p = f'''<w:p>
          <w:pPr>
            <w:spacing w:before="280" w:after="100"/>
          </w:pPr>
          <w:r>
            <w:rPr>
              <w:rFonts w:ascii="Plus Jakarta Sans" w:hAnsi="Plus Jakarta Sans"/>
              <w:b/>
              <w:sz w:val="28"/>
              <w:color w:val="1E293B"/>
            </w:rPr>
            <w:t>{xml_escape(text)}</w:t>
          </w:r>
        </w:p>'''
        self.body_elements.append(p)

    def add_heading_3(self, text):
        p = f'''<w:p>
          <w:pPr>
            <w:spacing w:before="200" w:after="80"/>
          </w:pPr>
          <w:r>
            <w:rPr>
              <w:rFonts w:ascii="Plus Jakarta Sans" w:hAnsi="Plus Jakarta Sans"/>
              <w:b/>
              <w:sz w:val="24"/>
              <w:color w:val="334155"/>
            </w:rPr>
            <w:t>{xml_escape(text)}</w:t>
          </w:r>
        </w:p>'''
        self.body_elements.append(p)

    def add_paragraph(self, text, bold_prefix=None, italic=False, color="212529"):
        p = '<w:p><w:pPr><w:spacing w:before="60" w:after="100" w:line="276" w:lineRule="auto"/></w:pPr>'
        if bold_prefix:
            p += f'''<w:r>
              <w:rPr>
                <w:rFonts w:ascii="Inter" w:hAnsi="Inter"/>
                <w:b/>
                <w:sz w:val="22"/>
                <w:color w:val="0D6EFD"/>
              </w:rPr>
              <w:t xml:space="preserve">{xml_escape(bold_prefix)} </w:t>
            </w:r>'''
        
        i_tag = "<w:i/>" if italic else ""
        p += f'''<w:r>
          <w:rPr>
            <w:rFonts w:ascii="Inter" w:hAnsi="Inter"/>
            {i_tag}
            <w:sz w:val="22"/>
            <w:color w:val="{color}"/>
          </w:r>
          <w:t xml:space="preserve">{xml_escape(text)}</w:t>
        </w:r></w:p>'''
        self.body_elements.append(p)

    def add_bullet(self, text, bold_prefix=None):
        p = '<w:p><w:pPr><w:numPr><w:ilvl w:val="0"/><w:numId w:val="1"/></w:numPr><w:spacing w:before="40" w:after="60"/></w:pPr>'
        p += '<w:r><w:rPr><w:rFonts w:ascii="Inter" w:hAnsi="Inter"/><w:b/><w:sz w:val="22"/><w:color w:val="0D6EFD"/></w:rPr><w:t xml:space="preserve">•  </w:t></w:r>'
        if bold_prefix:
            p += f'''<w:r>
              <w:rPr>
                <w:rFonts w:ascii="Inter" w:hAnsi="Inter"/>
                <w:b/>
                <w:sz w:val="22"/>
                <w:color w:val="1E293B"/>
              </w:rPr>
              <w:t xml:space="preserve">{xml_escape(bold_prefix)}: </w:t>
            </w:r>'''
        p += f'''<w:r>
          <w:rPr>
            <w:rFonts w:ascii="Inter" w:hAnsi="Inter"/>
            <w:sz w:val="22"/>
            <w:color w:val="334155"/>
          </w:r>
          <w:t xml:space="preserve">{xml_escape(text)}</w:t>
        </w:r></w:p>'''
        self.body_elements.append(p)

    def add_callout(self, text, title="IMPORTANT NOTICE", fill="EFF6FF", border="3B82F6"):
        box = f'''<w:tbl>
          <w:tblPr>
            <w:tblW w:w="9360" w:type="dxa"/>
            <w:tblBorders>
              <w:top w:val="none"/>
              <w:left w:val="single" w:sz="36" w:space="0" w:color="{border}"/>
              <w:bottom w:val="none"/>
              <w:right w:val="none"/>
            </w:tblBorders>
            <w:tblCellMar>
              <w:top w:w="160" w:type="dxa"/>
              <w:left w:w="240" w:type="dxa"/>
              <w:bottom w:w="160" w:type="dxa"/>
              <w:right w:w="200" w:type="dxa"/>
            </w:tblCellMar>
          </w:tblPr>
          <w:tr>
            <w:tc>
              <w:tcPr>
                <w:tcW w:w="9360" w:type="dxa"/>
                <w:shd w:val="clear" w:color="auto" w:fill="{fill}"/>
              </w:tcPr>
              <w:p>
                <w:pPr><w:spacing w:before="40" w:after="60"/></w:pPr>
                <w:r>
                  <w:rPr>
                    <w:rFonts w:ascii="Plus Jakarta Sans" w:hAnsi="Plus Jakarta Sans"/>
                    <w:b/>
                    <w:sz w:val="22"/>
                    <w:color w:val="{border}"/>
                  </w:rPr>
                  <w:t>{xml_escape(title)}</w:t>
                </w:r>
              </w:p>
              <w:p>
                <w:pPr><w:spacing w:before="0" w:after="40"/></w:pPr>
                <w:r>
                  <w:rPr>
                    <w:rFonts w:ascii="Inter" w:hAnsi="Inter"/>
                    <w:sz w:val="21"/>
                    <w:color w:val="1E293B"/>
                  </w:rPr>
                  <w:t>{xml_escape(text)}</w:t>
                </w:r>
              </w:p>
            </w:tc>
          </w:tr>
        </w:tbl>'''
        self.body_elements.append(box)
        self.body_elements.append('<w:p><w:pPr><w:spacing w:before="80" w:after="80"/></w:pPr></w:p>')

    def add_table(self, headers, rows, col_widths=None):
        total_width = 9360
        if not col_widths:
            w_per_col = total_width // len(headers)
            col_widths = [w_per_col] * len(headers)
        
        tbl = f'''<w:tbl>
          <w:tblPr>
            <w:tblW w:w="{total_width}" w:type="dxa"/>
            <w:tblBorders>
              <w:top w:val="single" w:sz="6" w:space="0" w:color="CBD5E1"/>
              <w:left w:val="single" w:sz="6" w:space="0" w:color="CBD5E1"/>
              <w:bottom w:val="single" w:sz="8" w:space="0" w:color="94A3B8"/>
              <w:right w:val="single" w:sz="6" w:space="0" w:color="CBD5E1"/>
              <w:insideH w:val="single" w:sz="4" w:space="0" w:color="E2E8F0"/>
              <w:insideV w:val="single" w:sz="4" w:space="0" w:color="E2E8F0"/>
            </w:tblBorders>
            <w:tblCellMar>
              <w:top w:w="120" w:type="dxa"/>
              <w:left w:w="160" w:type="dxa"/>
              <w:bottom w:w="120" w:type="dxa"/>
              <w:right w:w="160" w:type="dxa"/>
            </w:tblCellMar>
          </w:tblPr>'''

        # Header Row
        tbl += '<w:tr><w:trPr><w:tblHeader/></w:trPr>'
        for idx, h in enumerate(headers):
            w = col_widths[idx]
            tbl += f'''<w:tc>
              <w:tcPr>
                <w:tcW w:w="{w}" w:type="dxa"/>
                <w:shd w:val="clear" w:color="auto" w:fill="0D6EFD"/>
              </w:tcPr>
              <w:p>
                <w:pPr><w:spacing w:before="60" w:after="60"/></w:pPr>
                <w:r>
                  <w:rPr>
                    <w:rFonts w:ascii="Plus Jakarta Sans" w:hAnsi="Plus Jakarta Sans"/>
                    <w:b/>
                    <w:sz w:val="21"/>
                    <w:color w:val="FFFFFF"/>
                  </w:rPr>
                  <w:t>{xml_escape(h)}</w:t>
                </w:r>
              </w:p>
            </w:tc>'''
        tbl += '</w:tr>'

        # Body Rows
        for r_idx, row in enumerate(rows):
            fill = "F8FAFC" if r_idx % 2 == 1 else "FFFFFF"
            tbl += '<w:tr>'
            for idx, cell in enumerate(row):
                w = col_widths[idx]
                tbl += f'''<w:tc>
                  <w:tcPr>
                    <w:tcW w:w="{w}" w:type="dxa"/>
                    <w:shd w:val="clear" w:color="auto" w:fill="{fill}"/>
                  </w:tcPr>
                  <w:p>
                    <w:pPr><w:spacing w:before="60" w:after="60"/></w:pPr>
                    <w:r>
                      <w:rPr>
                        <w:rFonts w:ascii="Inter" w:hAnsi="Inter"/>
                        <w:sz w:val="20"/>
                        <w:color w:val="334155"/>
                      </w:rPr>
                      <w:t>{xml_escape(str(cell))}</w:t>
                    </w:r>
                  </w:p>
                </w:tc>'''
            tbl += '</w:tr>'

        tbl += '</w:tbl>'
        self.body_elements.append(tbl)
        self.body_elements.append('<w:p><w:pPr><w:spacing w:before="80" w:after="80"/></w:pPr></w:p>')

    def add_code_block(self, code_text):
        lines = code_text.split('\n')
        tbl = '''<w:tbl>
          <w:tblPr>
            <w:tblW w:w="9360" w:type="dxa"/>
            <w:tblBorders>
              <w:top w:val="single" w:sz="4" w:space="0" w:color="CBD5E1"/>
              <w:left w:val="single" w:sz="16" w:space="0" w:color="64748B"/>
              <w:bottom w:val="single" w:sz="4" w:space="0" w:color="CBD5E1"/>
              <w:right w:val="single" w:sz="4" w:space="0" w:color="CBD5E1"/>
            </w:tblBorders>
            <w:tblCellMar>
              <w:top w:w="120" w:type="dxa"/>
              <w:left w:w="180" w:type="dxa"/>
              <w:bottom w:w="120" w:type="dxa"/>
              <w:right w:w="180" w:type="dxa"/>
            </w:tblCellMar>
          </w:tblPr>
          <w:tr>
            <w:tc>
              <w:tcPr>
                <w:tcW w:w="9360" w:type="dxa"/>
                <w:shd w:val="clear" w:color="auto" w:fill="0F172A"/>
              </w:tcPr>'''
        
        for line in lines:
            tbl += f'''<w:p>
              <w:pPr><w:spacing w:before="10" w:after="10"/></w:pPr>
              <w:r>
                <w:rPr>
                  <w:rFonts w:ascii="Consolas" w:hAnsi="Consolas"/>
                  <w:sz w:val="18"/>
                  <w:color w:val="38BDF8"/>
                </w:rPr>
                <w:t xml:space="preserve">{xml_escape(line)}</w:t>
              </w:r>
            </w:p>'''
            
        tbl += '</w:tc></w:tr></w:tbl>'
        self.body_elements.append(tbl)
        self.body_elements.append('<w:p><w:pPr><w:spacing w:before="80" w:after="80"/></w:pPr></w:p>')

    def build_docx(self, output_path):
        doc_xml = f'''<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
            xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <w:body>
    {''.join(self.body_elements)}
    <w:sectPr>
      <w:pgSz w:w="11906" w:h="16838"/>
      <w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="720" w:footer="720" w:gutter="0"/>
    </w:sectPr>
  </w:body>
</w:document>'''

        content_types = '''<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
  <Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>
</Types>'''

        rels = '''<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>'''

        doc_rels = '''<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rIdStyles" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>'''

        styles_xml = '''<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:docDefaults>
    <w:rPrDefault>
      <w:rPr>
        <w:rFonts w:ascii="Inter" w:eastAsia="Inter" w:hAnsi="Inter" w:cs="Inter"/>
        <w:sz w:val="22"/>
        <w:szCs w:val="22"/>
        <w:color w:val="1E293B"/>
      </w:rPr>
    </w:rPrDefault>
    <w:pPrDefault>
      <w:pPr>
        <w:spacing w:before="60" w:after="120" w:line="276" w:lineRule="auto"/>
      </w:pPr>
    </w:pPrDefault>
  </w:docDefaults>
</w:styles>'''

        with zipfile.ZipFile(output_path, 'w', zipfile.ZIP_DEFLATED) as docx_zip:
            docx_zip.writestr('[Content_Types].xml', content_types)
            docx_zip.writestr('_rels/.rels', rels)
            docx_zip.writestr('word/_rels/document.xml.rels', doc_rels)
            docx_zip.writestr('word/document.xml', doc_xml)
            docx_zip.writestr('word/styles.xml', styles_xml)

        print(f"Successfully generated DOCX file at: {output_path}")

def generate_full_documentation():
    builder = DocxBuilder()

    # Document Header & Title
    builder.add_title(
        "GYRS PORTAL - COMPLETE PROJECT SPECIFICATION",
        "Gujarat Youth Rojgar Setu • Multi-Page Architecture, Admin Panel, Candidate Portal, Client-Side Frontend & Technical Skeleton"
    )

    builder.add_callout(
        "This official document provides the complete, exhaustive operational architecture, frontend UI mechanics, candidate authentication workflows (including Mobile OTP), Directorate Admin Panel engine, and codebase skeleton for the Gujarat Youth Rojgar Setu (GYRS) platform.",
        "EXECUTIVE BRIEFING & PURPOSE"
    )

    # -------------------------------------------------------------
    # SECTION 1: HOW THE PROJECT WORKS
    # -------------------------------------------------------------
    builder.add_heading_1("1. How the GYRS Project Works")
    builder.add_paragraph(
        "Gujarat Youth Rojgar Setu (GYRS) is an enterprise-grade state employment platform developed for the Government of Gujarat. It bridges the critical gap between technical graduates, ITI diploma holders, general job seekers, state public departments, and leading private industrial employers across all 33 Gujarat districts."
    )
    
    builder.add_heading_2("1.1 End-to-End Operational Lifecycle")
    builder.add_paragraph("The platform operates across a high-efficiency 5-stage lifecycle:")
    builder.add_bullet("Citizens explore over 48,500+ verified government and corporate vacancies across all 33 Gujarat districts without mandatory up-front login.", "Stage 1 - Public Vacancy Discovery")
    builder.add_bullet("Candidates register with educational qualification, technical skills, and 10-digit mobile number, verified instantaneously via OTP authentication.", "Stage 2 - Mobile OTP Authentication")
    builder.add_bullet("Job cards feature an official Apply Online button routing directly to https://ojas.gujarat.gov.in/, alongside a 1-Click Fast Track button that saves the application directly into the candidate's dossier.", "Stage 3 - Hybrid Application Mechanism")
    builder.add_bullet("Candidates log into their dedicated Candidate Workspace to view a 4-stage visual stepper (Applied ➔ Screening ➔ Shortlisted ➔ Interview Call).", "Stage 4 - Real-Time Application Tracking")
    builder.add_bullet("Directorate officers use the GYRS Portal Admin Panel to publish new vacancies, edit existing positions, review student rosters, and audit candidate applications.", "Stage 5 - State Directorate Supervision")

    builder.add_heading_2("1.2 Multi-Page Architecture (Decoupled vs Legacy Monolith)")
    builder.add_paragraph(
        "Unlike single-page applications that overload the browser and complicate navigation, GYRS is architected with dedicated, distinct HTML pages sharing persistent state via localStorage:"
    )

    table_data = [
        ["Page File", "Target Audience", "Primary Responsibilities"],
        ["index.html", "Public Visitors & Job Seekers", "Naukri.com-style job explorer, 3-segment search, 4-step guide, department showcase."],
        ["student-login.html", "Candidate / Student", "Dedicated authentication gateway: Mobile OTP login (demo: 4826), Password login, Registration."],
        ["admin-login.html", "Directorate Officers", "GYRS Portal Admin authentication gateway with authorized demo credentials."],
        ["student-dashboard.html", "Registered Candidate", "Personal candidate workspace, 4-stage visual application tracker, dossier, Rozgar Melas."],
        ["admin-dashboard.html", "Directorate Admin", "GYRS Portal Admin Panel: Job Feed Engine, Candidate Roster, Application Audit Trail, CSV Export."]
    ]
    builder.add_table(table_data[0], table_data[1:], [2200, 2400, 4760])

    # -------------------------------------------------------------
    # SECTION 2: CLIENT-SIDE FRONTEND VIEW (index.html)
    # -------------------------------------------------------------
    builder.add_heading_1("2. Client-Side Frontend View (index.html)")
    builder.add_paragraph(
        "The public client-side interface has been re-engineered with modern Naukri.com-inspired aesthetics, responsive ergonomics, and 100% English typography (Google Plus Jakarta Sans & Inter)."
    )

    builder.add_heading_2("2.1 Navigation & Accessibility Header")
    builder.add_bullet("Displays Government of Gujarat insignia, Directorate of Employment & Training (DET), 1800-233-5500 toll-free helpline, and a quick link to State Recruitment Exchange.", "Top Government Bar")
    builder.add_bullet("Contains Brand Insignia (GYRS GUJARAT), Home, Explore Jobs, How It Works, Candidate Portal, Direct Recruitment (linking to https://ojas.gujarat.gov.in/), and dynamic Candidate Sign In / OTP button.", "Main Navbar")
    builder.add_bullet("The Admin Directorate login has been intentionally removed from the top navigation and placed in the footer, maintaining strict candidate-centric focus.", "Security Refactor")

    builder.add_heading_2("2.2 State Live Circulars Ticker")
    builder.add_paragraph(
        "A marquee ticker at the top of the hero section streams live employment circulars, GPSC class 1 & 2 recruitment alerts, Mukhyamantri Apprentice Yojana stipend updates, and official direct recruitment links."
    )

    builder.add_heading_2("2.3 Unified 3-Segment Search Bar")
    builder.add_paragraph(
        "Located in the hero section, this search bar allows instant filtering across multiple parameters in a single interaction:"
    )
    builder.add_bullet("Real-time text query against titles, required skills, and departments (e.g., Python, Civil Engineer, Nurse).", "Segment 1 - Skills & Title Input")
    builder.add_bullet("Dropdown filtering between Full-Time, Official Government (Direct), Government Contract, and Apprenticeship.", "Segment 2 - Employment Type")
    builder.add_bullet("Dropdown filtering across all 33 Gujarat districts (Ahmedabad, Gandhinagar GIFT City, Surat, Vadodara, Rajkot, Kutch, etc.).", "Segment 3 - District Location")

    builder.add_heading_2("2.4 Naukri.com-Style 2-Column Job Explorer")
    builder.add_paragraph(
        "The core job explorer features a 2-column layout designed for maximum usability:"
    )
    builder.add_bullet("Includes Location Filter, Technical Skill Dropdown (Python, Web Dev, Typing, Electrical, Cyber Security), Employment Type, and a Trust & Compliance verification seal.", "Left Filter Sidebar (Col-lg-3)")
    builder.add_bullet("Renders dynamic job cards displaying Title, Department, District badge, Match Score (%), Experience, Salary, Description, Skill Badges, and two action buttons:", "Right Job Feed (Col-lg-9)")
    builder.add_bullet("Links directly to https://ojas.gujarat.gov.in/ (opens in new tab) with zero visible 'OJAS' text in visible labels.", "• Apply Online Button")
    builder.add_bullet("Allows logged-in candidates to immediately apply and record the application in their live tracker.", "• 1-Click Fast Track Apply")

    builder.add_heading_2("2.5 Clean Footer Architecture")
    builder.add_paragraph(
        "The footer contains 4 structured columns:"
    )
    builder.add_bullet("Overview of GYRS mission and Directorate of Employment and Training.", "Column 1 - Brand Summary")
    builder.add_bullet("Home, Explore Jobs, How It Works, Top Employers, Candidate Workspace.", "Column 2 - Quick Navigation")
    builder.add_bullet("Candidate Login & OTP (student-login.html), GYRS Portal Admin Login (admin-login.html), State Recruitment Exchange (https://ojas.gujarat.gov.in/), Digital Gujarat Services.", "Column 3 - Administrative Access")
    builder.add_bullet("DET Headquarters address at Dr. Jivraj Mehta Bhavan, Gandhinagar, toll-free number, and support email.", "Column 4 - Directorate Contact")

    # -------------------------------------------------------------
    # SECTION 3: USER / CANDIDATE PANEL (student-dashboard.html & student-login.html)
    # -------------------------------------------------------------
    builder.add_heading_1("3. User / Candidate Panel (Student Portal)")
    builder.add_paragraph(
        "The candidate user panel is built specifically for students and technical job seekers, emphasizing clarity, rapid authentication, and real-time status visibility."
    )

    builder.add_heading_2("3.1 Dedicated Candidate Login & Mobile OTP Authentication")
    builder.add_paragraph(
        "Located at student-login.html, the authentication portal provides 3 accessible tabbed options:"
    )
    builder.add_bullet("Candidates enter their 10-digit mobile number, receive a simulated demo OTP code (4826), view an active countdown timer, and verify with 1-click auto-fill.", "Tab 1: Mobile OTP Login (Recommended)")
    builder.add_bullet("Traditional login using enrollment ID or username with password (default demo: student / student123).", "Tab 2: Password Login")
    builder.add_bullet("New candidates register by providing full name, mobile number, email, qualification, and home district.", "Tab 3: Candidate Registration")

    builder.add_heading_2("3.2 Candidate Executive Hero Card")
    builder.add_paragraph(
        "Upon authentication, candidates enter student-dashboard.html, greeted by a personalized executive dashboard banner:"
    )
    builder.add_bullet("Dynamic initials derived from the student's name, with a green verified badge checkmark.", "Avatar Badge")
    builder.add_bullet("Full name, Enrollment ID (e.g., GYRS-2026-001), State Verified Candidate badge, Degree, District, and Official Email.", "Candidate Meta")
    builder.add_bullet("Interactive progress bar showing 85% readiness with an 'Edit Profile' button opening a live modal.", "Profile Completeness Gauge")

    builder.add_heading_2("3.3 Live KPI Statistics Tiles")
    builder.add_bullet("Total vacancies applied across government and private sectors.", "Applications Submitted")
    builder.add_bullet("Applications verified by state nodal officer scrutiny.", "Screening & Verified")
    builder.add_bullet("Applications qualifying for interview round.", "Shortlisted")
    builder.add_bullet("Formal interview call letters issued with venue details.", "Interview Calls Issued")

    builder.add_heading_2("3.4 The 4-Stage Visual Application Lifecycle Tracker")
    builder.add_paragraph(
        "The signature feature of the Candidate Workspace is the visual pipeline stepper displaying every application's live progress:"
    )
    builder.add_code_block("""[ Stage 1: Applied ] ===> [ Stage 2: Under Review ] ===> [ Stage 3: Shortlisted ] ===> [ Stage 4: Interview Call ]
      (Green Check)             (Green Check)                 (Green Check)               (Active Pulsing Badge)""")
    builder.add_paragraph(
        "Each application card displays the Job Title, Department, District, Application Date, Stage Stepper, and Interview Details (e.g., 'Interview Date: Oct 28, 2026 at DET Gandhinagar') with a call letter download button."
    )

    builder.add_heading_2("3.5 Additional Candidate Workspaces")
    builder.add_bullet("Curates jobs matching the student's registered degree and skills with matching percentage tags.", "Recommended Jobs Feed")
    builder.add_bullet("Summarizes Aadhaar verification, degree certificate, registered phone number, and competency skills.", "Candidate Dossier Tab")
    builder.add_bullet("Lists upcoming state mega job fairs with dates, participating MNCs, and venue ground locations.", "Gujarat Rozgar Melas Tab")

    # -------------------------------------------------------------
    # SECTION 4: GYRS PORTAL ADMIN PANEL (admin-dashboard.html & admin-login.html)
    # -------------------------------------------------------------
    builder.add_heading_1("4. GYRS Portal Admin Panel (State Administration)")
    builder.add_paragraph(
        "The administration portal is exclusively branded as GYRS Portal Admin Panel, providing Directorate officers with centralized control over vacancy feeds, student rosters, and audit trails."
    )

    builder.add_heading_2("4.1 Executive Admin Authentication (admin-login.html)")
    builder.add_bullet("Deep slate/dark theme with red administrative accents and State insignia.", "Theme & Ambiance")
    builder.add_bullet("Pre-loaded with authorized admin credentials (admin / admin123) and 1-click auto-fill helper.", "Authorized Access")
    builder.add_bullet("Prominently states 'Restricted access for authorized GYRS Portal administrators.'", "Security Disclaimer")

    builder.add_heading_2("4.2 Administrative KPI Metrics Strip")
    builder.add_bullet("Real-time count of registered candidate dossiers in the state database.", "Total Students Roster (8 candidates)")
    builder.add_bullet("Count of candidates with completed document verification.", "State Verified Candidates (7 candidates)")
    builder.add_bullet("Active vacancies published and accessible on the public job board.", "Active Job Openings (6 vacancies)")
    builder.add_bullet("Total applications logged through both 1-Click Fast Track and direct channels.", "Applications Audited (15+ applications)")

    builder.add_heading_2("4.3 Tab 1: Job Postings Feed Engine")
    builder.add_paragraph(
        "The job engine enables administrators to manage the complete lifecycle of job postings:"
    )
    builder.add_bullet("Modal form accepting Title, Department, District, Salary, Qualification, Job Type, Skills, AI Match Score, and Application URL (https://ojas.gujarat.gov.in/).", "Create New Job Opening (+ Post New Job)")
    builder.add_bullet("Pre-populates an existing opening in a modal for instant modifications.", "Edit Job Posting")
    builder.add_bullet("Instantly removes an opening from localStorage and updates all candidate recommendations.", "Delete Job Posting")
    builder.add_bullet("Live search bar filtering postings by designation or hiring organization.", "Real-time Search Filter")

    builder.add_heading_2("4.4 Tab 2: Candidate Verification Roster")
    builder.add_paragraph(
        "A high-density administrative table displaying all registered candidates with:"
    )
    builder.add_bullet("Student Name, Enrollment ID, Contact Mobile, Registered Email, District, Degree Qualification, Skills Badges, and Status (Verified, Pending, Shortlisted, Placed).", "Candidate Columns")
    builder.add_bullet("Filter candidates by specific district (Ahmedabad, Gandhinagar, Surat, etc.) or verification status.", "Dropdown Filters")
    builder.add_bullet("Opens full candidate modal displaying profile dossier, qualifications, applied jobs, and verification stamp.", "View Candidate Dossier Modal")

    builder.add_heading_2("4.5 Tab 3: Statewide Applications Audit Trail & CSV Export")
    builder.add_paragraph(
        "Provides an unalterable audit log of every application transaction across Gujarat:"
    )
    builder.add_bullet("Displays Candidate Name, Target Job Title, Hiring Department, District, Submission Timestamp, and Verification Status.", "Audit Log Table")
    builder.add_bullet("Generates and downloads a live CSV spreadsheet containing the complete student roster with 1 click.", "Export Roster CSV Engine")

    # -------------------------------------------------------------
    # SECTION 5: SKELETON & ARCHITECTURE OF THE PROJECT
    # -------------------------------------------------------------
    builder.add_heading_1("5. Skeleton & Technical Architecture")
    builder.add_paragraph(
        "The codebase follows clean separation of concerns, lightweight dependencies, and zero reliance on heavy server-side backends for demonstration."
    )

    builder.add_heading_2("5.1 Complete Project Directory Tree")
    builder.add_code_block("""/Users/onlymec/Downloads/PRAKSH 1 OCT/
├── index.html                   # Public Job Board & Explorer (Client-Side Frontend)
├── student-login.html           # Candidate Gateway (Mobile OTP, Password, Register)
├── admin-login.html             # GYRS Portal Admin Authentication Gateway
├── student-dashboard.html       # Candidate Workspace (4-Stage Pipeline Tracker)
├── admin-dashboard.html         # GYRS Portal Admin Panel (Job Engine & Roster)
├── css/
│   └── styles.css               # Unified Responsive Styling (Naukri UI + Dashboards)
├── js/
│   └── app.js                   # State Management, CRUD Engine, Routing & Event Listeners
└── GYRS_Project_Documentation.docx # Comprehensive System Documentation & Architecture""")

    builder.add_heading_2("5.2 Data Architecture & localStorage Models")
    builder.add_paragraph(
        "All data models are synchronized via browser localStorage across all five pages under distinct namespace keys:"
    )

    builder.add_bullet("Array of Job objects containing: id, title, department, district, salary, qualification, type, skills, matchScore, ojasUrl, description.", "gyrs_jobs_data_v2")
    builder.add_bullet("Array of Student objects containing: id, name, username, password, email, phone, district, qualification, skills, status, appliedJobIds, avatar.", "gyrs_students_roster_v2")
    builder.add_bullet("Current session object containing: role ('student' | 'admin'), user (Student object | Admin credentials).", "gyrs_auth_session_v2")

    builder.add_heading_2("5.3 Technology Stack & Libraries")
    table_stack = [
        ["Component", "Technology", "Purpose"],
        ["Markup", "HTML5 (Semantic)", "Accessible, SEO-compliant multi-page markup."],
        ["Styling", "Bootstrap 5.3.2 + Custom CSS", "Responsive 12-column grid, modals, responsive navigation."],
        ["Typography", "Plus Jakarta Sans & Inter", "Modern, legible English typography matching modern web standards."],
        ["Iconography", "Font Awesome 6.4.2", "Vectors for government insignia, filters, actions, and badges."],
        ["Logic & State", "Vanilla JavaScript (ES6+)", "Zero-dependency event handling, localStorage CRUD, OTP engine."],
        ["Document Engine", "Pure Python (zipfile/OpenXML)", "Native Word .docx document generator without third-party libraries."]
    ]
    builder.add_table(table_stack[0], table_stack[1:], [2000, 3200, 4160])

    builder.add_heading_2("5.4 Key Functional Verification Summary")
    builder.add_callout(
        "1. Multi-Page Architecture: Completely separated into 5 specialized HTML pages.\n"
        "2. Admin Branding: Exclusively branded as 'GYRS Portal Admin Panel'.\n"
        "3. Mobile OTP Login: Interactive 10-digit mobile input with demo OTP 4826 and auto-fill.\n"
        "4. OJAS Clean-up: Zero visible 'OJAS' text; apply buttons cleanly link to https://ojas.gujarat.gov.in/.\n"
        "5. Header/Footer Navigation: Admin login placed in footer; header focused on candidate workflow.\n"
        "6. Typography & Language: 100% English across all views and modals.",
        "QUALITY & COMPLIANCE SIGN-OFF"
    )

    output_file = "/Users/onlymec/Downloads/PRAKSH 1 OCT/GYRS_Project_Documentation.docx"
    builder.build_docx(output_file)

if __name__ == "__main__":
    generate_full_documentation()

