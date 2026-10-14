# GYRS — Candidate Notification Templates (WhatsApp + Email)

> These are the exact messages a candidate receives when admin taps
> **WhatsApp / Email** on any job row (Admin Dashboard)
> or **WhatsApp Alert / Email Alert** on a job details page.
> Placeholders auto-fill from that job's data.

---

## 1. WhatsApp Job Alert Template

**Structure:**

```whatsapp
New Govt Job Alert — GYRS Gujarat
{JOB_TITLE}
{DEPARTMENT}, {DISTRICT}
Posts: {TOTAL_POSTS} | Salary: {SALARY}
Last Date: {LAST_DATE}
Notification: {NOTIFICATION_PDF_LINK}
Details & Apply: {JOB_DETAILS_PAGE_LINK}
```

**Filled example (Job #1):**

```whatsapp
New Govt Job Alert — GYRS Gujarat
Junior Software Engineer
Gujarat Informatics Ltd (GIL), Gandhinagar
Posts: 48 | Salary: ₹35,000 - ₹45,000 / month
Last Date: 02 Nov 2026
Notification: https://ojas.gujarat.gov.in/pdf/gil-junior-software-engineer-2026.pdf
Details & Apply: https://gyrs-portal.vercel.app/job-details.html?id=1
```

Opens via `wa.me` share — admin picks the candidate/group chat and sends.

---

## 2. Email Job Alert Template

**Subject structure:**

```
New Vacancy: {JOB_TITLE} ({DEPARTMENT}) — Apply by {LAST_DATE}
```

**Body structure:**

```
Dear Candidate,

A new verified vacancy is live on GYRS Gujarat Youth Rojgar Setu:

Job: {JOB_TITLE}
Department: {DEPARTMENT}
Location: {DISTRICT}, Gujarat
Advt No: {ADVT_NO} | Posts: {TOTAL_POSTS}
Salary: {SALARY}
Qualification: {QUALIFICATION}
Last Date: {LAST_DATE}

Notification PDF: {NOTIFICATION_PDF_LINK}
View full details & Apply Now: {JOB_DETAILS_PAGE_LINK}

— GYRS Portal
```

**Filled example (Job #1):**

Subject:

```
New Vacancy: Junior Software Engineer (Gujarat Informatics Ltd (GIL)) — Apply by 02 Nov 2026
```

Body:

```
Dear Candidate,

A new verified vacancy is live on GYRS Gujarat Youth Rojgar Setu:

Job: Junior Software Engineer
Department: Gujarat Informatics Ltd (GIL)
Location: Gandhinagar, Gujarat
Advt No: GIL/2026/04 | Posts: 48
Salary: ₹35,000 - ₹45,000 / month
Qualification: B.Tech / B.E. (Computer / IT)
Last Date: 02 Nov 2026

Notification PDF: https://ojas.gujarat.gov.in/pdf/gil-junior-software-engineer-2026.pdf
View full details & Apply Now: https://gyrs-portal.vercel.app/job-details.html?id=1

— GYRS Portal
```

Opens the default mail app with subject + body pre-filled (mailto).

---

## 3. Where templates trigger from

| Location | Buttons |
|---|---|
| Admin Dashboard → Job Postings Engine (each row) | WhatsApp (green) + Email (blue) icons |
| Job Details page → Important Links sidebar | WhatsApp Alert + Email Alert + Copy Message |
