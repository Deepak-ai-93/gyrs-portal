/**
 * GYRS (Gujarat Youth Rojgar Setu)
 * Naukri.com-Style Job Board, Transparent 4-Step Application Tracker,
 * English Language Architecture & Cross-Screen Responsive Controller
 */

document.addEventListener("DOMContentLoaded", () => {
  // ==========================================================================
  // 1. DATASETS & STORAGE CONSTANTS (V2 PURE ENGLISH PERSISTENCE)
  // ==========================================================================
  const STORAGE_KEYS = {
    STUDENTS: "gyrs_students_roster_v2",
    SESSION: "gyrs_auth_session_v2",
    JOBS: "gyrs_jobs_data_v3"
  };

  const adminCredentials = {
    username: "admin",
    password: "admin123",
    fullName: "Super Administrator",
    role: "admin",
    email: "admin.gyrs@gujarat.gov.in"
  };

  const initialStudents = [
    {
      id: "GYRS-2026-001",
      name: "Jagan Trivedi",
      username: "student",
      password: "student123",
      email: "jagan.trivedi@gyrs.gov.in",
      phone: "9876543210",
      district: "Ahmedabad",
      qualification: "B.Tech / B.E. (Computer / IT)",
      skills: ["Python", "Web Development", "Data Analysis", "SQL"],
      status: "Verified",
      registeredDate: "15 Jan 2026",
      cgpa: "8.7",
      appliedJobIds: [1, 2, 6]
    },
    {
      id: "GYRS-2026-002",
      name: "Priya Patel",
      username: "priya",
      password: "student123",
      email: "priya.patel@gujarat.ac.in",
      phone: "9825012345",
      district: "Gandhinagar",
      qualification: "MCA / Software Engineering",
      skills: ["Python", "Cloud", "Cyber Security", "SQL"],
      status: "Placed",
      registeredDate: "10 Feb 2026",
      cgpa: "9.1",
      appliedJobIds: [1, 6]
    },
    {
      id: "GYRS-2026-003",
      name: "Rahul Solanki",
      username: "rahul",
      password: "student123",
      email: "rahul.solanki@mail.com",
      phone: "9712345678",
      district: "Vadodara",
      qualification: "ITI / Diploma (Mechanical/Electrical)",
      skills: ["AutoCAD", "Machinery", "Maintenance"],
      status: "Verified",
      registeredDate: "18 Feb 2026",
      cgpa: "7.9",
      appliedJobIds: [5]
    },
    {
      id: "GYRS-2026-004",
      name: "Anjali Mehta",
      username: "anjali",
      password: "student123",
      email: "anjali.mehta@gmail.com",
      phone: "9909011223",
      district: "Surat",
      qualification: "ITI / Diploma (Mechanical/Electrical)",
      skills: ["Automation", "Textile Tech", "Machinery"],
      status: "Shortlisted",
      registeredDate: "01 Mar 2026",
      cgpa: "8.4",
      appliedJobIds: [4]
    },
    {
      id: "GYRS-2026-005",
      name: "Bhavik Shah",
      username: "bhavik",
      password: "student123",
      email: "bhavik.shah@gyrs.gov.in",
      phone: "9426098765",
      district: "Rajkot",
      qualification: "B.Tech / B.E. (Computer / IT)",
      skills: ["Networking", "Cyber Security", "Python"],
      status: "Verified",
      registeredDate: "12 Mar 2026",
      cgpa: "8.9",
      appliedJobIds: [6]
    },
    {
      id: "GYRS-2026-006",
      name: "Nisha Desai",
      username: "nisha",
      password: "student123",
      email: "nisha.desai@outlook.com",
      phone: "9638054321",
      district: "Kutch",
      qualification: "ITI / Diploma (Mechanical/Electrical)",
      skills: ["Electrical", "Solar Tech", "Maintenance"],
      status: "Pending",
      registeredDate: "25 Mar 2026",
      cgpa: "8.1",
      appliedJobIds: [3]
    },
    {
      id: "GYRS-2026-007",
      name: "Hardik Prajapati",
      username: "hardik",
      password: "student123",
      email: "hardik.prajapati@gmail.com",
      phone: "9898012399",
      district: "Ahmedabad",
      qualification: "12th Pass / Graduate",
      skills: ["Typing", "MS Office", "Data Entry"],
      status: "Verified",
      registeredDate: "02 Apr 2026",
      cgpa: "7.5",
      appliedJobIds: [2]
    },
    {
      id: "GYRS-2026-008",
      name: "Kavita Vaghela",
      username: "kavita",
      password: "student123",
      email: "kavita.vaghela@gujarat.in",
      phone: "9724087654",
      district: "Gandhinagar",
      qualification: "B.Com / Accounting",
      skills: ["Tally", "MS Office", "GST Compliance"],
      status: "Under Review",
      registeredDate: "10 Apr 2026",
      cgpa: "8.0",
      appliedJobIds: []
    }
  ];

  const initialJobsData = [
    {
      id: 1,
      title: "Junior Software Engineer",
      department: "Gujarat Informatics Ltd (GIL)",
      location: "Gandhinagar",
      district: "Gandhinagar",
      salary: "₹35,000 - ₹45,000 / month",
      qualification: "B.Tech / B.E. (Computer / IT)",
      skills: ["Python", "Web Development", "Database"],
      matchScore: 96,
      type: "Full-Time",
      ojasUrl: "https://ojas.gujarat.gov.in/",
      applyUrl: "https://ojas.gujarat.gov.in/",
      officialWebsite: "https://gil.gujarat.gov.in/",
      notificationPdf: "https://ojas.gujarat.gov.in/pdf/gil-junior-software-engineer-2026.pdf",
      advtNo: "GIL/2026/04",
      totalPosts: 48,
      postedDate: "02 Oct 2026",
      lastDate: "02 Nov 2026",
      ageLimit: "21 - 35 Years (relaxation as per Govt rules)",
      fees: "General: ₹500 | SC/ST/SEBC/EWS: ₹250 | PwD: Nil",
      vacancyBreakup: "UR: 20 | SEBC: 12 | SC: 6 | ST: 7 | EWS: 3",
      eligibility: ["B.E./B.Tech in Computer / IT / MCA with minimum 55%", "Knowledge of Python, Web Development and SQL / Database", "Gujarati / Hindi language proficiency desirable"],
      responsibilities: ["Development of state e-governance web modules", "API integration and database management", "Testing, documentation and support at GIFT City centre"],
      selectionProcess: ["Online Application Scrutiny", "Written / Technical Test", "Personal Interview", "Document Verification"],
      howToApply: ["Read the official notification PDF carefully before applying.", "Click Apply Now below to open the OJAS application portal.", "Register with One-Time Registration, fill the form and upload photo / signature.", "Pay the fee online and note the confirmation number for tracking in GYRS dashboard."],
      description: "Recruitment for state e-governance software initiatives and AI web platform development at GIFT City, Gandhinagar."
    },
    {
      id: 2,
      title: "Data Entry & Systems Operator",
      department: "Ahmedabad Municipal Corp (AMC)",
      location: "Ahmedabad",
      district: "Ahmedabad",
      salary: "₹22,000 - ₹28,000 / month",
      qualification: "12th Pass / Graduate",
      skills: ["Gujarati Typing", "MS Office", "Data Entry"],
      matchScore: 91,
      type: "Govt Contract",
      ojasUrl: "https://ojas.gujarat.gov.in/",
      applyUrl: "https://ojas.gujarat.gov.in/",
      officialWebsite: "https://ahmedabadcity.gov.in/",
      notificationPdf: "https://ojas.gujarat.gov.in/pdf/amc-data-entry-operator-2026.pdf",
      advtNo: "AMC/EST/2026/11",
      totalPosts: 120,
      postedDate: "28 Sep 2026",
      lastDate: "27 Oct 2026",
      ageLimit: "18 - 34 Years",
      fees: "All Categories: ₹200 (online only)",
      vacancyBreakup: "UR: 50 | SEBC: 30 | SC: 15 | ST: 18 | EWS: 7",
      eligibility: ["12th Pass / Graduate from recognized board / university", "Gujarati + English typing speed 25 wpm", "CCC / computer certificate desirable"],
      responsibilities: ["Digital record digitization at civic centres", "Citizen service desk data operations", "Document verification and MIS entry"],
      selectionProcess: ["Application Scrutiny", "Typing / Skill Test", "Merit List", "Document Verification"],
      howToApply: ["Download and read the AMC notification PDF.", "Click Apply Now to go to OJAS / AMC portal.", "Fill personal, education details and upload documents.", "Submit before last date and save acknowledgement."],
      description: "Digital record digitization, citizen service desk operations, and document management for AMC civic centers."
    },
    {
      id: 3,
      title: "Solar Panel Maintenance Engineer",
      department: "GEDA - Renewable Energy Dept",
      location: "Kutch",
      district: "Kutch",
      salary: "₹30,000 - ₹40,000 / month",
      qualification: "ITI / Diploma (Mechanical/Electrical)",
      skills: ["Electrical", "Solar Tech", "Maintenance"],
      matchScore: 88,
      type: "Full-Time",
      ojasUrl: "https://ojas.gujarat.gov.in/",
      applyUrl: "https://ojas.gujarat.gov.in/",
      officialWebsite: "https://geda.gujarat.gov.in/",
      notificationPdf: "https://ojas.gujarat.gov.in/pdf/geda-solar-engineer-2026.pdf",
      advtNo: "GEDA/HR/2026/07",
      totalPosts: 65,
      postedDate: "05 Oct 2026",
      lastDate: "04 Nov 2026",
      ageLimit: "19 - 36 Years",
      fees: "General: ₹400 | Reserved: ₹200",
      vacancyBreakup: "UR: 28 | SEBC: 16 | SC: 8 | ST: 9 | EWS: 4",
      eligibility: ["ITI / Diploma in Electrical / Mechanical", "Knowledge of PV systems and safety norms", "Willingness for Khavda site posting"],
      responsibilities: ["PV array maintenance and grid commissioning", "Substation supervision and fault reporting", "Daily generation logs and safety compliance"],
      selectionProcess: ["Shortlisting", "Trade Test", "Interview", "Medical + Document Verification"],
      howToApply: ["Read GEDA notification PDF for site conditions.", "Apply Now via OJAS link below.", "Attach ITI / Diploma certificates and experience if any.", "Track status in GYRS Candidate Dashboard."],
      description: "Grid commissioning, photovoltaic array maintenance, and substation supervision at Khavda Renewable Energy Park."
    },
    {
      id: 4,
      title: "Textile Automation Technician",
      department: "Gujarat Industrial Dev Corp (GIDC)",
      location: "Surat",
      district: "Surat",
      salary: "₹25,000 - ₹32,000 / month",
      qualification: "ITI / Diploma (Mechanical/Electrical)",
      skills: ["Automation", "Textile Tech", "Machinery"],
      matchScore: 85,
      type: "Full-Time",
      ojasUrl: "https://ojas.gujarat.gov.in/",
      applyUrl: "https://ojas.gujarat.gov.in/",
      officialWebsite: "https://gidc.gujarat.gov.in/",
      notificationPdf: "https://ojas.gujarat.gov.in/pdf/gidc-textile-technician-2026.pdf",
      advtNo: "GIDC/TECH/2026/09",
      totalPosts: 80,
      postedDate: "01 Oct 2026",
      lastDate: "30 Oct 2026",
      ageLimit: "18 - 33 Years",
      fees: "General: ₹300 | SC/ST: ₹150",
      vacancyBreakup: "UR: 34 | SEBC: 20 | SC: 10 | ST: 11 | EWS: 5",
      eligibility: ["ITI / Diploma in Mechanical / Electrical / Textile", "PLC / automation basics preferred", "Shift duty willingness at Surat GIDC"],
      responsibilities: ["Automated loom operations and PLC upkeep", "Preventive maintenance scheduling", "Spare management and breakdown support"],
      selectionProcess: ["Scrutiny", "Practical Trade Test", "Interview", "Document Verification"],
      howToApply: ["Verify eligibility in notification PDF.", "Click Apply Now for OJAS form.", "Upload trade certificates.", "Save application number for interview call letter."],
      description: "Automated loom machinery operations, programmable logic maintenance, and industrial equipment servicing at Surat GIDC."
    },
    {
      id: 5,
      title: "Apprentice Mechanical Supervisor",
      department: "Vadodara ITI Apprenticeship",
      location: "Vadodara",
      district: "Vadodara",
      salary: "₹18,000 - ₹22,000 / month",
      qualification: "ITI / Diploma (Mechanical/Electrical)",
      skills: ["Machinery", "AutoCAD", "Maintenance"],
      matchScore: 82,
      type: "Apprenticeship",
      ojasUrl: "https://ojas.gujarat.gov.in/",
      applyUrl: "https://ojas.gujarat.gov.in/",
      officialWebsite: "https://apprenticeshipindia.gov.in/",
      notificationPdf: "https://ojas.gujarat.gov.in/pdf/vadodara-apprentice-2026.pdf",
      advtNo: "DETVAD/APP/2026/03",
      totalPosts: 150,
      postedDate: "25 Sep 2026",
      lastDate: "25 Oct 2026",
      ageLimit: "18 - 28 Years",
      fees: "No Application Fee (Stipend under Mukhyamantri Apprentice Yojana)",
      vacancyBreakup: "Mechanical: 90 | Electrical: 60 (district-wise merit)",
      eligibility: ["ITI pass in relevant trade / Diploma fresher", "Gujarat domicile", "Aadhaar + bank details for DBT stipend"],
      responsibilities: ["1-year on-job training in manufacturing belt", "AutoCAD drawings and shop-floor assistance", "Monthly logbook and assessment"],
      selectionProcess: ["Merit (ITI marks)", "Document Verification", "Medical Fitness"],
      howToApply: ["Read apprentice notification PDF.", "Apply Now on OJAS / Apprenticeship portal.", "Select Vadodara cluster and trade.", "Stipend credited monthly via DBT after joining."],
      description: "1-year industrial apprenticeship program with monthly stipend under Mukhyamantri Apprentice Yojana in Vadodara manufacturing belt."
    },
    {
      id: 6,
      title: "Cyber Security Analyst",
      department: "Gujarat Police Cyber Cell",
      location: "Rajkot",
      district: "Rajkot",
      salary: "₹40,000 - ₹55,000 / month",
      qualification: "B.Tech / B.E. (Computer / IT)",
      skills: ["Networking", "Cyber Security", "Python"],
      matchScore: 94,
      type: "Govt Direct",
      ojasUrl: "https://ojas.gujarat.gov.in/",
      applyUrl: "https://ojas.gujarat.gov.in/",
      officialWebsite: "https://police.gujarat.gov.in/",
      notificationPdf: "https://ojas.gujarat.gov.in/pdf/cyber-cell-analyst-2026.pdf",
      advtNo: "GP/CYBER/2026/02",
      totalPosts: 36,
      postedDate: "08 Oct 2026",
      lastDate: "07 Nov 2026",
      ageLimit: "21 - 37 Years + physical standards as per rules",
      fees: "General: ₹600 | SC/ST/SEBC/EWS: ₹300",
      vacancyBreakup: "UR: 15 | SEBC: 9 | SC: 4 | ST: 5 | EWS: 3",
      eligibility: ["B.E./B.Tech (CS/IT) / MCA with 55%", "Networking + security fundamentals, Python scripting", "Clean record; police verification required"],
      responsibilities: ["Digital forensics and threat monitoring", "State network protection and incident response", "Awareness and reporting support"],
      selectionProcess: ["Written Exam", "Technical / Practical Test", "Interview", "Physical + Document Verification"],
      howToApply: ["Read cyber cell notification PDF in detail.", "Click Apply Now to open OJAS form.", "Fill exam centre preference (Rajkot / Ahmedabad).", "Download hall ticket when announced and track here."],
      description: "Digital forensic investigations, state data network protection, and cyber threat monitoring for Rajkot Cyber Crime Division."
    }
  ];

  // Helper Storage functions
  function getStudents() {
    const data = localStorage.getItem(STORAGE_KEYS.STUDENTS);
    if (!data) {
      localStorage.setItem(STORAGE_KEYS.STUDENTS, JSON.stringify(initialStudents));
      return initialStudents;
    }
    try {
      return JSON.parse(data);
    } catch {
      return initialStudents;
    }
  }

  function saveStudents(list) {
    localStorage.setItem(STORAGE_KEYS.STUDENTS, JSON.stringify(list));
  }

  function getJobs() {
    const data = localStorage.getItem(STORAGE_KEYS.JOBS);
    if (!data) {
      localStorage.setItem(STORAGE_KEYS.JOBS, JSON.stringify(initialJobsData));
      return initialJobsData;
    }
    try {
      return JSON.parse(data);
    } catch {
      return initialJobsData;
    }
  }

  function saveJobs(list) {
    localStorage.setItem(STORAGE_KEYS.JOBS, JSON.stringify(list));
  }

  function getSession() {
    const data = localStorage.getItem(STORAGE_KEYS.SESSION);
    if (!data) return null;
    try {
      return JSON.parse(data);
    } catch {
      return null;
    }
  }

  function setSession(sessionObj) {
    if (!sessionObj) {
      localStorage.removeItem(STORAGE_KEYS.SESSION);
    } else {
      localStorage.setItem(STORAGE_KEYS.SESSION, JSON.stringify(sessionObj));
    }
  }

  // ==========================================================================
  // 2. STATE MANAGER
  // ==========================================================================
  const appState = {
    students: getStudents(),
    jobs: getJobs(),
    session: getSession(),
    currentPortal: "public"
  };

  function getCurrentStudent() {
    if (appState.session && appState.session.role === "student") {
      return appState.session.user;
    }
    return appState.students[0];
  }

  // ==========================================================================
  // 3. DOM ELEMENT REFERENCES
  // ==========================================================================
  // Navigation & Switcher
  const btnSwitchPublic = document.getElementById("btnSwitchPublic");
  const btnSwitchStudent = document.getElementById("btnSwitchStudent");
  const btnSwitchAdmin = document.getElementById("btnSwitchAdmin");

  const navLinkPublic = document.getElementById("navLinkPublic");
  const navLinkStudent = document.getElementById("navLinkStudent");
  const navLinkAdmin = document.getElementById("navLinkAdmin");

  // Portal View Containers
  const viewPublic = document.getElementById("viewPublic");
  const viewStudent = document.getElementById("viewStudent");
  const viewAdmin = document.getElementById("viewAdmin");

  // Auth & Badges
  const loginBtn = document.getElementById("loginBtn");
  const logoutBtn = document.getElementById("logoutBtn");
  const quickLoginModalBtn = document.getElementById("quickLoginModalBtn");
  const quickUserBadge = document.getElementById("quickUserBadge");
  const quickUserRoleText = document.getElementById("quickUserRoleText");
  const userProfileBadge = document.getElementById("userProfileBadge");
  const userFullNameDisplay = document.getElementById("userFullNameDisplay");

  // Modals Bootstrap Instances
  const authModalEl = document.getElementById("authModal");
  const authModal = authModalEl ? new bootstrap.Modal(authModalEl) : null;

  const addJobModalEl = document.getElementById("addJobModal");
  const addJobModal = addJobModalEl ? new bootstrap.Modal(addJobModalEl) : null;

  const editJobModalEl = document.getElementById("editJobModal");
  const editJobModal = editJobModalEl ? new bootstrap.Modal(editJobModalEl) : null;

  const editStudentModalEl = document.getElementById("editStudentModal");
  const editStudentModal = editStudentModalEl ? new bootstrap.Modal(editStudentModalEl) : null;

  const viewStudentModalEl = document.getElementById("viewStudentModal");
  const viewStudentModal = viewStudentModalEl ? new bootstrap.Modal(viewStudentModalEl) : null;

  const editProfileModalEl = document.getElementById("editProfileModal");
  const editProfileModal = editProfileModalEl ? new bootstrap.Modal(editProfileModalEl) : null;

  const jobDetailsModalEl = document.getElementById("jobDetailsModal");
  const jobDetailsModal = jobDetailsModalEl ? new bootstrap.Modal(jobDetailsModalEl) : null;

  // Toast System
  function showToast(message, type = "success", icon = "check-circle") {
    const container = document.getElementById("toastContainer");
    if (!container) return;

    const toast = document.createElement("div");
    toast.className = `gyrs-toast toast-${type}`;
    toast.innerHTML = `
      <div class="d-flex align-items-center gap-2">
        <i class="fas fa-${icon} ${type === 'success' ? 'text-success' : type === 'danger' ? 'text-danger' : 'text-primary'} fa-lg"></i>
        <div class="small fw-semibold">${message}</div>
      </div>
      <button type="button" class="btn-close btn-sm" aria-label="Close"></button>
    `;

    toast.querySelector(".btn-close").addEventListener("click", () => {
      toast.remove();
    });

    container.appendChild(toast);

    setTimeout(() => {
      if (toast.parentNode) {
        toast.style.opacity = "0";
        toast.style.transform = "translateX(100%)";
        toast.style.transition = "all 0.3s ease";
        setTimeout(() => toast.remove(), 300);
      }
    }, 4000);
  }

  // ==========================================================================
  // 4. PORTAL SWITCHING ENGINE
  // ==========================================================================
  function switchPortalView(portalName) {
    appState.currentPortal = portalName;

    if (portalName === "student") {
      if (!window.location.pathname.includes("student-dashboard")) {
        window.location.href = "student-dashboard.html";
        return;
      }
      renderStudentDashboard();
      window.scrollTo({ top: 0, behavior: "smooth" });
    } else if (portalName === "admin") {
      if (!window.location.pathname.includes("admin-dashboard")) {
        window.location.href = "admin-dashboard.html";
        return;
      }
      renderAdminDashboard();
      window.scrollTo({ top: 0, behavior: "smooth" });
    } else {
      if (!window.location.pathname.includes("index") && window.location.pathname !== "/" && !window.location.pathname.endsWith("/")) {
        window.location.href = "index.html";
        return;
      }
      viewPublic?.classList.remove("d-none");
      if (typeof renderPublicJobs === "function") renderPublicJobs();
    }
  }

  window.switchPortalView = switchPortalView;

  // ==========================================================================
  // 5. SESSION & UI STATE MANAGEMENT
  // ==========================================================================
  function updateSessionUI() {
    const session = appState.session;

    if (session) {
      if (session.role === "admin") {
        if (quickUserRoleText) quickUserRoleText.textContent = "GYRS Portal Admin";
        if (userFullNameDisplay) userFullNameDisplay.textContent = "GYRS Administrator";
        if (userProfileBadge) {
          userProfileBadge.classList.remove("d-none");
          userProfileBadge.innerHTML = `<a href="admin-dashboard.html" class="badge bg-danger-subtle text-danger border border-danger px-3 py-2 rounded-pill small text-decoration-none"><i class="fas fa-shield-alt me-1"></i> GYRS Portal Admin</a>`;
        }
        if (loginBtn) loginBtn.classList.add("d-none");
        if (logoutBtn) logoutBtn.classList.remove("d-none");
        if (quickLoginModalBtn) quickLoginModalBtn.classList.add("d-none");
      } else if (session.role === "student") {
        const studentName = session.user?.name || "Student";
        if (quickUserRoleText) quickUserRoleText.textContent = `Candidate: ${studentName.split(' ')[0]}`;
        if (userFullNameDisplay) userFullNameDisplay.textContent = studentName.split(' ')[0];
        if (userProfileBadge) {
          userProfileBadge.classList.remove("d-none");
          userProfileBadge.innerHTML = `<a href="student-dashboard.html" class="badge bg-primary-subtle text-primary border border-primary px-3 py-2 rounded-pill small text-decoration-none"><i class="fas fa-user-graduate me-1"></i> ${studentName.split(' ')[0]}</a>`;
        }
        if (loginBtn) loginBtn.classList.add("d-none");
        if (logoutBtn) logoutBtn.classList.remove("d-none");
        if (quickLoginModalBtn) quickLoginModalBtn.classList.add("d-none");
      }
    } else {
      if (quickUserRoleText) quickUserRoleText.textContent = "Guest Visitor";
      if (userProfileBadge) userProfileBadge.classList.add("d-none");
      if (loginBtn) {
        loginBtn.classList.remove("d-none");
        loginBtn.innerHTML = `<i class="fas fa-sign-in-alt me-1"></i> Candidate Sign In / OTP`;
      }
      if (logoutBtn) logoutBtn.classList.add("d-none");
      if (quickLoginModalBtn) quickLoginModalBtn.classList.remove("d-none");
    }
  }

  function handleLogout() {
    appState.session = null;
    setSession(null);
    showToast("You have successfully signed out.", "info", "sign-out-alt");
    const path = window.location.pathname;
    if (path.includes("student-dashboard") || path.includes("student-login")) {
      window.location.href = "student-login.html";
    } else if (path.includes("admin-dashboard") || path.includes("admin-login")) {
      window.location.href = "admin-login.html";
    } else {
      updateSessionUI();
      if (typeof switchPortalView === "function") switchPortalView("public");
    }
  }

  // ==========================================================================
  // 6. AUTHENTICATION LOGIC (MOBILE OTP + STUDENT CREDENTIALS + ADMIN)
  // ==========================================================================
  function initAuthSystem() {
    // 6A. MOBILE OTP FLOW FOR CANDIDATES
    let currentOtp = "4826";
    let otpPhone = "9876543210";
    let countdownTimer = null;

    const btnSendOtp = document.getElementById("btnSendOtp");
    const otpMobileInput = document.getElementById("otpMobileInput");
    const otpVerificationSection = document.getElementById("otpVerificationSection");
    const displayOtpPhone = document.getElementById("displayOtpPhone");
    const demoOtpPill = document.getElementById("demoOtpPill");
    const otpCodeInput = document.getElementById("otpCodeInput");
    const btnFillDemoOtp = document.getElementById("btnFillDemoOtp");
    const btnChangeMobile = document.getElementById("btnChangeMobile");
    const otpLoginForm = document.getElementById("otpLoginForm");

    if (btnSendOtp) {
      btnSendOtp.addEventListener("click", () => {
        const phone = (otpMobileInput?.value || "").trim();
        if (!phone || phone.length < 10) {
          alert("Please enter a valid 10-digit mobile number.");
          return;
        }
        otpPhone = phone;
        currentOtp = "4826";

        if (displayOtpPhone) displayOtpPhone.textContent = phone;
        if (demoOtpPill) demoOtpPill.textContent = `Demo OTP: ${currentOtp}`;
        if (otpVerificationSection) otpVerificationSection.classList.remove("d-none");

        if (otpCodeInput) {
          otpCodeInput.value = "";
          otpCodeInput.focus();
        }

        let secondsLeft = 30;
        const timerEl = document.getElementById("otpTimerText");
        if (timerEl) timerEl.innerHTML = `Resend code in <strong class="text-dark">${secondsLeft}s</strong>`;
        clearInterval(countdownTimer);
        countdownTimer = setInterval(() => {
          secondsLeft--;
          if (secondsLeft <= 0) {
            clearInterval(countdownTimer);
            if (timerEl) timerEl.innerHTML = `<a href="#" onclick="document.getElementById('btnSendOtp')?.click(); return false;" class="text-primary fw-bold">Resend OTP</a>`;
          } else {
            if (timerEl) timerEl.innerHTML = `Resend code in <strong class="text-dark">${secondsLeft}s</strong>`;
          }
        }, 1000);

        showToast(`One-Time Password sent to +91 ${phone}! Demo code: ${currentOtp}`, "success", "mobile-alt");
      });
    }

    if (btnFillDemoOtp) {
      btnFillDemoOtp.addEventListener("click", () => {
        if (otpCodeInput) otpCodeInput.value = currentOtp;
      });
    }

    if (btnChangeMobile) {
      btnChangeMobile.addEventListener("click", () => {
        if (otpVerificationSection) otpVerificationSection.classList.add("d-none");
        if (otpMobileInput) otpMobileInput.focus();
      });
    }

    if (otpLoginForm) {
      otpLoginForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const enteredCode = (otpCodeInput?.value || "").trim();
        if (enteredCode !== currentOtp && enteredCode !== "4826") {
          alert(`Invalid OTP code! Please enter the demo code: ${currentOtp}`);
          return;
        }

        appState.students = getStudents();
        let matched = appState.students.find(s => s.phone === otpPhone || s.phone.endsWith(otpPhone.slice(-10)));

        if (!matched) {
          const nextNum = appState.students.length + 1;
          const newId = `GYRS-2026-${String(nextNum).padStart(3, "0")}`;
          matched = {
            id: newId,
            name: `Candidate ${otpPhone.slice(-4)}`,
            username: `user_${otpPhone.slice(-4)}`,
            password: "student123",
            email: `candidate.${otpPhone.slice(-4)}@gyrs.gov.in`,
            phone: otpPhone,
            district: "Gandhinagar",
            qualification: "B.Tech / B.E. (Computer / IT)",
            skills: ["Computer Fundamentals", "Data Management"],
            status: "Verified",
            registeredDate: new Date().toLocaleDateString("en-GB", { day: '2-digit', month: 'short', year: 'numeric' }),
            cgpa: "8.5",
            appliedJobIds: [1]
          };
          appState.students.push(matched);
          saveStudents(appState.students);
        }

        appState.session = { role: "student", user: matched };
        setSession(appState.session);
        showToast(`Mobile verified! Welcome, ${matched.name}. Redirecting...`, "success", "check-circle");
        setTimeout(() => {
          window.location.href = "student-dashboard.html";
        }, 600);
      });
    }

    // 6B. Student Username/Password Login Form Handler
    const studentLoginForm = document.getElementById("studentLoginForm");
    studentLoginForm?.addEventListener("submit", (e) => {
      e.preventDefault();
      const usernameInput = document.getElementById("studentUsernameInput").value.trim().toLowerCase();
      const passwordInput = document.getElementById("studentPasswordInput").value.trim();

      appState.students = getStudents();
      const matchedStudent = appState.students.find(s => 
        (s.username.toLowerCase() === usernameInput || s.email.toLowerCase() === usernameInput || s.phone === usernameInput) &&
        s.password === passwordInput
      );

      if (matchedStudent) {
        appState.session = { role: "student", user: matchedStudent };
        setSession(appState.session);
        updateSessionUI();
        authModal?.hide();
        showToast(`Welcome back, ${matchedStudent.name}! Redirecting...`, "success", "check-circle");
        if (window.location.pathname.includes("student-login")) {
          setTimeout(() => { window.location.href = "student-dashboard.html"; }, 600);
        } else if (typeof switchPortalView === "function") {
          switchPortalView("student");
        }
      } else {
        alert("Invalid candidate username or password!\nPlease use demo credentials: Username: student | Password: student123");
      }
    });

    // 6C. Admin Login Form Handler
    const adminLoginForm = document.getElementById("adminLoginForm");
    adminLoginForm?.addEventListener("submit", (e) => {
      e.preventDefault();
      const adminUsername = document.getElementById("adminUsernameInput").value.trim();
      const adminPassword = document.getElementById("adminPasswordInput").value.trim();

      if (adminUsername === adminCredentials.username && adminPassword === adminCredentials.password) {
        appState.session = { role: "admin", user: adminCredentials };
        setSession(appState.session);
        updateSessionUI();
        authModal?.hide();
        showToast("Signed in as Administrator. Welcome to Control Center.", "success", "shield-alt");
        if (window.location.pathname.includes("admin-login")) {
          setTimeout(() => { window.location.href = "admin-dashboard.html"; }, 600);
        } else if (typeof switchPortalView === "function") {
          switchPortalView("admin");
        }
      } else {
        alert("Invalid admin credentials!\nOfficial Demo Credentials: Username: admin | Password: admin123");
      }
    });

    // 6D. Student Registration Form Handler
    const studentRegisterForm = document.getElementById("studentRegisterForm");
    studentRegisterForm?.addEventListener("submit", (e) => {
      e.preventDefault();
      const fullName = document.getElementById("regFullName").value.trim();
      const username = document.getElementById("regUsername").value.trim().toLowerCase();
      const password = document.getElementById("regPassword").value.trim();
      const email = document.getElementById("regEmail")?.value.trim() || `${username}@gyrs.gov.in`;
      const mobile = document.getElementById("regMobile")?.value.trim() || "9876543210";
      const district = document.getElementById("regDistrict").value;
      const qual = document.getElementById("regQual").value;
      const skillsRaw = document.getElementById("regSkills").value.trim();
      const skills = skillsRaw ? skillsRaw.split(",").map(s => s.trim()).filter(Boolean) : ["General"];

      appState.students = getStudents();
      if (appState.students.some(s => s.username.toLowerCase() === username)) {
        alert("This username is already taken. Please choose another username.");
        return;
      }

      const nextNum = appState.students.length + 1;
      const newId = `GYRS-2026-${String(nextNum).padStart(3, "0")}`;

      const newStudent = {
        id: newId,
        name: fullName,
        username: username,
        password: password,
        email: email,
        phone: mobile,
        district: district,
        qualification: qual,
        skills: skills,
        status: "Verified",
        registeredDate: new Date().toLocaleDateString("en-GB", { day: '2-digit', month: 'short', year: 'numeric' }),
        cgpa: "8.5",
        appliedJobIds: [1]
      };

      appState.students.push(newStudent);
      saveStudents(appState.students);

      appState.session = { role: "student", user: newStudent };
      setSession(appState.session);
      updateSessionUI();
      authModal?.hide();
      showToast(`Registration successful! Welcome, ${fullName}. Redirecting...`, "success", "user-check");
      if (window.location.pathname.includes("student-login")) {
        setTimeout(() => { window.location.href = "student-dashboard.html"; }, 600);
      } else if (typeof switchPortalView === "function") {
        switchPortalView("student");
      }
    });

    // Demo Fill Buttons
    const fillStudent = () => {
      const u = document.getElementById("studentUsernameInput");
      const p = document.getElementById("studentPasswordInput");
      if (u) u.value = "student";
      if (p) p.value = "student123";
      showToast("Demo student credentials loaded (student / student123).", "info", "bolt");
    };
    document.getElementById("btnFillStudentDemo")?.addEventListener("click", fillStudent);
    document.getElementById("fillDemoStudentBtn")?.addEventListener("click", fillStudent);

    const fillAdmin = () => {
      const u = document.getElementById("adminUsernameInput");
      const p = document.getElementById("adminPasswordInput");
      if (u) u.value = "admin";
      if (p) p.value = "admin123";
      showToast("Demo admin credentials loaded (admin / admin123).", "info", "bolt");
    };
    document.getElementById("btnFillAdminDemo")?.addEventListener("click", fillAdmin);
    document.getElementById("fillDemoAdminBtn")?.addEventListener("click", fillAdmin);

    // Navigation buttons
    loginBtn?.addEventListener("click", () => {
      window.location.href = "student-login.html";
    });
    quickLoginModalBtn?.addEventListener("click", () => {
      window.location.href = "student-login.html";
    });

    logoutBtn?.addEventListener("click", handleLogout);
    document.getElementById("studentLogoutBtn")?.addEventListener("click", handleLogout);
    document.getElementById("adminLogoutBtn")?.addEventListener("click", handleLogout);

    // Tab parameter check for student-login.html?tab=register
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get("tab");
    if (tabParam === "register") {
      const regTab = document.getElementById("tabRegister");
      if (regTab) bootstrap.Tab.getOrCreateInstance(regTab).show();
    } else if (tabParam === "password") {
      const passTab = document.getElementById("tabPasswordLogin");
      if (passTab) bootstrap.Tab.getOrCreateInstance(passTab).show();
    }
  }

  function openLoginModalWithTab(tabRole = "student") {
    if (tabRole === "admin") {
      window.location.href = "admin-login.html";
    } else {
      window.location.href = "student-login.html";
    }
  }

  window.openLoginModalWithTab = openLoginModalWithTab;

  // ==========================================================================
  // 7. ADMIN PANEL: JOB POSTINGS & FEED ENGINE
  // ==========================================================================
  function renderAdminJobsTable() {
    const tableBody = document.getElementById("adminJobsTableBody");
    if (!tableBody) return;

    appState.jobs = getJobs();
    const jobs = appState.jobs;

    const totalJobsCount = document.getElementById("adminStatActiveJobs");
    if (totalJobsCount) totalJobsCount.textContent = jobs.length;

    tableBody.innerHTML = "";

    if (jobs.length === 0) {
      tableBody.innerHTML = `
        <tr>
          <td colspan="7" class="text-center py-4 text-muted">
            <i class="fas fa-briefcase fa-2x mb-2 d-block text-secondary"></i>
            No active job postings found. Click "+ Post New Job Opening" to add a new posting.
          </td>
        </tr>
      `;
      return;
    }

    jobs.forEach(job => {
      const skillsBadges = job.skills.slice(0, 3).map(sk => 
        `<span class="badge bg-light text-dark border me-1 small" style="font-size: 0.7rem;">${sk}</span>`
      ).join('');

      let typeBadgeClass = "bg-primary-subtle text-primary border border-primary";
      if (job.type === "Govt Direct") typeBadgeClass = "bg-success-subtle text-success border border-success";
      else if (job.type === "Apprenticeship") typeBadgeClass = "bg-warning-subtle text-warning border border-warning";

      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td><span class="badge bg-secondary-subtle text-dark border font-monospace">#${job.id}</span></td>
        <td>
          <div class="fw-bold text-dark">${job.title}</div>
          <div class="small text-muted font-monospace"><i class="fas fa-star text-warning me-1"></i>${job.matchScore || 90}% AI Match</div>
        </td>
        <td>
          <div class="small fw-semibold text-primary"><i class="fas fa-building me-1"></i>${job.department}</div>
        </td>
        <td>
          <span class="badge bg-light text-dark border">${job.district}</span>
        </td>
        <td>
          <div class="small fw-bold text-success">${job.salary}</div>
        </td>
        <td>
          <span class="badge ${typeBadgeClass}">${job.type}</span>
        </td>
        <td class="text-center">
          <div class="d-flex justify-content-center gap-1">
            <button class="btn btn-sm btn-outline-success" onclick="window.shareJobWhatsApp(${job.id})" title="Send WhatsApp job alert (template)">
              <i class="fab fa-whatsapp"></i>
            </button>
            <button class="btn btn-sm btn-outline-info" onclick="window.shareJobEmail(${job.id})" title="Send Email job alert (template)">
              <i class="fas fa-envelope"></i>
            </button>
            <button class="btn btn-sm btn-outline-primary" onclick="window.editJobPosting(${job.id})" title="Edit Job Posting">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger" onclick="window.deleteJobPosting(${job.id})" title="Delete Job Posting">
              <i class="fas fa-trash-alt"></i>
            </button>
          </div>
        </td>
      `;
      tableBody.appendChild(tr);
    });
  }

  // ---- Admin job-form helpers (PDF upload + field parsing) ----
  function readFileAsDataURL(file) {
    return new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onload = () => resolve(reader.result);
      reader.onerror = () => reject(reader.error);
      reader.readAsDataURL(file);
    });
  }

  function formatDateInput(value, fallback) {
    if (!value) return fallback;
    try {
      const d = new Date(value + "T00:00:00");
      if (isNaN(d)) return value;
      return d.toLocaleDateString("en-GB", { day: "2-digit", month: "short", year: "numeric" });
    } catch { return value; }
  }

  function splitLines(id) {
    const el = document.getElementById(id);
    if (!el) return [];
    return el.value.split("\n").map(s => s.trim()).filter(Boolean);
  }

  function splitComma(id) {
    const el = document.getElementById(id);
    if (!el) return [];
    return el.value.split(",").map(s => s.trim()).filter(Boolean);
  }

  function val(id, fallback = "") {
    const el = document.getElementById(id);
    return el ? el.value.trim() : fallback;
  }

  // Shows selected PDF name/size under the file input
  document.getElementById("addJobNotifFile")?.addEventListener("change", (e) => {
    const f = e.target.files[0];
    const hint = document.getElementById("addJobNotifFileHint");
    if (hint) hint.textContent = f ? `Selected: ${f.name} (${(f.size / 1024).toFixed(0)} KB)` : "PDF up to ~3MB. Uploaded file is used first.";
  });

  // Admin Actions: Add New Job Form Submit
  const addJobForm = document.getElementById("addJobForm");
  addJobForm?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const title = document.getElementById("addJobTitle").value.trim();
    const dept = document.getElementById("addJobDept").value.trim();
    const district = document.getElementById("addJobDistrict").value;
    const salary = document.getElementById("addJobSalary").value.trim();
    const qual = val("addJobQual", "B.Tech / B.E. (Computer / IT)");
    const type = document.getElementById("addJobType").value;
    const skillsRaw = document.getElementById("addJobSkills").value.trim();
    const skills = skillsRaw ? skillsRaw.split(",").map(s => s.trim()).filter(Boolean) : ["General"];
    const score = parseInt(val("addJobScore", "90")) || 90;
    const ojasUrl = val("addJobOjasUrl") || "https://ojas.gujarat.gov.in/";
    const desc = document.getElementById("addJobDesc").value.trim();

    const advtNo = val("addJobAdvt") || `GYRS/2026/${Date.now().toString().slice(-4)}`;
    const totalPosts = val("addJobPosts", "As per notification");
    const postedDate = formatDateInput(val("addJobPosted"), new Date().toLocaleDateString("en-GB", { day: '2-digit', month: 'short', year: 'numeric' }));
    const lastDate = formatDateInput(val("addJobLastDate"), "Refer notification");
    const ageLimit = val("addJobAge", "As per Govt rules");
    const fees = val("addJobFees", "Refer notification");
    const breakup = val("addJobBreakup", "As per roster");
    const eligibility = splitLines("addJobEligibility");
    const responsibilities = splitLines("addJobResp");
    const selection = splitComma("addJobSelection");
    const howTo = splitLines("addJobHowTo");
    const website = val("addJobWebsite") || "https://ojas.gujarat.gov.in/";
    let notifUrl = val("addJobNotifUrl") || ojasUrl;

    // PDF file upload takes priority over URL
    const fileInput = document.getElementById("addJobNotifFile");
    const file = fileInput?.files?.[0];
    if (file) {
      if (file.size > 3 * 1024 * 1024) {
        showToast("PDF is larger than 3MB — using the URL link instead (browser storage limit).", "info", "info-circle");
      } else {
        try {
          notifUrl = await readFileAsDataURL(file);
          showToast("Notification PDF attached to this posting.", "success", "file-pdf");
        } catch {
          showToast("Could not read PDF file — using the URL link instead.", "info", "info-circle");
        }
      }
    }

    appState.jobs = getJobs();
    const nextId = appState.jobs.length > 0 ? Math.max(...appState.jobs.map(j => j.id)) + 1 : 1;

    const newJob = {
      id: nextId,
      title: title,
      department: dept,
      location: district,
      district: district,
      salary: salary,
      qualification: qual,
      skills: skills,
      matchScore: score,
      type: type,
      ojasUrl: ojasUrl,
      applyUrl: ojasUrl,
      officialWebsite: website,
      notificationPdf: notifUrl,
      advtNo: advtNo,
      totalPosts: totalPosts,
      postedDate: postedDate,
      lastDate: lastDate,
      ageLimit: ageLimit,
      fees: fees,
      vacancyBreakup: breakup,
      eligibility: eligibility.length ? eligibility : [qual, `Working knowledge of ${skills.slice(0, 2).join(", ")}`],
      responsibilities: responsibilities.length ? responsibilities : [desc],
      selectionProcess: selection.length ? selection : ["Application Scrutiny", "Written / Skill Test", "Interview", "Document Verification"],
      howToApply: howTo.length ? howTo : ["Read the notification PDF carefully.", "Click Apply Now to open the official portal.", "Fill the form and upload documents.", "Track status in GYRS dashboard."],
      description: desc
    };

    appState.jobs.unshift(newJob);
    try {
      saveJobs(appState.jobs);
    } catch {
      appState.jobs.shift();
      showToast("Browser storage full (PDF too large). Try a smaller PDF or use a URL link.", "danger", "exclamation-triangle");
      return;
    }

    addJobModal?.hide();
    addJobForm.reset();
    const hint = document.getElementById("addJobNotifFileHint");
    if (hint) hint.textContent = "PDF up to ~3MB. Uploaded file is used first.";
    showToast(`New job posting #${nextId} published successfully!`, "success", "check-circle");

    // Re-render
    renderAdminJobsTable();
    renderStudentDashboard();
  });

  document.getElementById("openAddJobModalBtn")?.addEventListener("click", () => {
    addJobModal?.show();
  });

  // Admin Actions: Edit Job Posting
  window.editJobPosting = function(jobId) {
    appState.jobs = getJobs();
    const raw = appState.jobs.find(j => j.id === jobId);
    if (!raw) return;
    const job = withJobDefaults(raw);

    document.getElementById("editJobId").value = job.id;
    document.getElementById("editJobTitle").value = job.title;
    document.getElementById("editJobDept").value = job.department;
    document.getElementById("editJobDistrict").value = job.district;
    document.getElementById("editJobSalary").value = job.salary;
    document.getElementById("editJobQual").value = job.qualification;
    document.getElementById("editJobType").value = job.type;
    document.getElementById("editJobSkills").value = job.skills.join(", ");
    document.getElementById("editJobScore").value = job.matchScore || 90;
    document.getElementById("editJobPosts").value = job.totalPosts;
    document.getElementById("editJobAdvt").value = job.advtNo;
    document.getElementById("editJobPosted").value = job.postedDate;
    document.getElementById("editJobLastDate").value = job.lastDate;
    document.getElementById("editJobAge").value = job.ageLimit;
    document.getElementById("editJobFees").value = job.fees;
    document.getElementById("editJobBreakup").value = job.vacancyBreakup;
    document.getElementById("editJobEligibility").value = (job.eligibility || []).join("\n");
    document.getElementById("editJobResp").value = (job.responsibilities || []).join("\n");
    document.getElementById("editJobSelection").value = (job.selectionProcess || []).join(", ");
    document.getElementById("editJobHowTo").value = (job.howToApply || []).join("\n");
    document.getElementById("editJobNotifUrl").value = (job.notificationPdf || "").startsWith("data:") ? "" : (job.notificationPdf || "");
    document.getElementById("editJobWebsite").value = job.officialWebsite || "";
    document.getElementById("editJobOjasUrl").value = job.applyUrl || job.ojasUrl;
    document.getElementById("editJobDesc").value = job.description;
    const cur = document.getElementById("editJobNotifCurrent");
    if (cur) cur.textContent = "Current: " + ((job.notificationPdf || "").startsWith("data:") ? "uploaded PDF file attached" : (job.notificationPdf || "—"));
    const ef = document.getElementById("editJobNotifFile");
    if (ef) ef.value = "";

    editJobModal?.show();
  };

  const editJobForm = document.getElementById("editJobForm");
  editJobForm?.addEventListener("submit", async (e) => {
    e.preventDefault();
    const jobId = parseInt(document.getElementById("editJobId").value);
    appState.jobs = getJobs();
    const index = appState.jobs.findIndex(j => j.id === jobId);
    if (index === -1) return;

    const skillsRaw = document.getElementById("editJobSkills").value.trim();
    const skills = skillsRaw ? skillsRaw.split(",").map(s => s.trim()).filter(Boolean) : [];
    const prev = withJobDefaults(appState.jobs[index]);

    // PDF: uploaded file replaces, else URL field, else keep existing
    let notifPdf = prev.notificationPdf;
    const efile = document.getElementById("editJobNotifFile")?.files?.[0];
    const eurl = val("editJobNotifUrl");
    if (efile) {
      if (efile.size > 3 * 1024 * 1024) {
        showToast("PDF larger than 3MB — keeping existing PDF (browser storage limit).", "info", "info-circle");
      } else {
        try { notifPdf = await readFileAsDataURL(efile); } catch { /* keep existing */ }
      }
    } else if (eurl) {
      notifPdf = eurl;
    }

    const elig = splitLines("editJobEligibility");
    const resp = splitLines("editJobResp");
    const sel = splitComma("editJobSelection");
    const how = splitLines("editJobHowTo");

    appState.jobs[index] = {
      ...appState.jobs[index],
      title: document.getElementById("editJobTitle").value.trim(),
      department: document.getElementById("editJobDept").value.trim(),
      district: document.getElementById("editJobDistrict").value,
      location: document.getElementById("editJobDistrict").value,
      salary: document.getElementById("editJobSalary").value.trim(),
      qualification: document.getElementById("editJobQual").value,
      type: document.getElementById("editJobType").value,
      skills: skills,
      matchScore: parseInt(document.getElementById("editJobScore").value) || 90,
      totalPosts: val("editJobPosts", prev.totalPosts),
      advtNo: val("editJobAdvt", prev.advtNo),
      postedDate: val("editJobPosted", prev.postedDate),
      lastDate: val("editJobLastDate", prev.lastDate),
      ageLimit: val("editJobAge", prev.ageLimit),
      fees: val("editJobFees", prev.fees),
      vacancyBreakup: val("editJobBreakup", prev.vacancyBreakup),
      eligibility: elig.length ? elig : prev.eligibility,
      responsibilities: resp.length ? resp : prev.responsibilities,
      selectionProcess: sel.length ? sel : prev.selectionProcess,
      howToApply: how.length ? how : prev.howToApply,
      notificationPdf: notifPdf,
      officialWebsite: val("editJobWebsite") || prev.officialWebsite,
      applyUrl: val("editJobOjasUrl") || prev.applyUrl,
      ojasUrl: val("editJobOjasUrl") || prev.ojasUrl,
      description: document.getElementById("editJobDesc").value.trim()
    };

    try {
      saveJobs(appState.jobs);
    } catch {
      showToast("Browser storage full (PDF too large). Try a smaller PDF or use a URL link.", "danger", "exclamation-triangle");
      return;
    }
    editJobModal?.hide();
    showToast(`Job posting #${jobId} updated successfully!`, "success", "check-circle");

    renderAdminJobsTable();
    renderStudentDashboard();
  });

  // Admin Actions: Delete Job Posting
  window.deleteJobPosting = function(jobId) {
    if (!confirm(`Are you sure you want to remove job posting #${jobId}?`)) return;

    appState.jobs = getJobs();
    appState.jobs = appState.jobs.filter(j => j.id !== jobId);
    saveJobs(appState.jobs);
    showToast(`Job posting #${jobId} removed successfully.`, "info", "trash-alt");

    renderAdminJobsTable();
    renderStudentDashboard();
  };

  // Job Search in Admin Panel
  document.getElementById("adminJobSearch")?.addEventListener("input", filterAdminJobs);
  document.getElementById("adminJobDeptFilter")?.addEventListener("change", filterAdminJobs);

  function filterAdminJobs() {
    const searchTxt = (document.getElementById("adminJobSearch")?.value || "").toLowerCase().trim();
    const deptFilter = document.getElementById("adminJobDeptFilter")?.value || "ALL";

    const rows = document.querySelectorAll("#adminJobsTableBody tr");
    rows.forEach(tr => {
      const text = tr.innerText.toLowerCase();
      const matchSearch = (searchTxt === "" || text.includes(searchTxt));
      const matchDept = (deptFilter === "ALL" || text.includes(deptFilter.toLowerCase()));
      tr.style.display = (matchSearch && matchDept) ? "" : "none";
    });
  }

  // ==========================================================================
  // 8. ADMIN PANEL: STUDENT CANDIDATE ROSTER MANAGEMENT
  // ==========================================================================
  function renderAdminDashboard() {
    appState.students = getStudents();
    appState.jobs = getJobs();

    renderAdminJobsTable();
    renderAdminStudentTable();
    renderAdminRecentApplications();

    // Update Admin KPI Counters
    const statTotalStudents = document.getElementById("adminStatTotalStudents");
    if (statTotalStudents) statTotalStudents.textContent = appState.students.length;

    const statVerified = document.getElementById("adminStatVerifiedStudents");
    if (statVerified) {
      statVerified.textContent = appState.students.filter(s => s.status === "Verified").length;
    }

    const statJobs = document.getElementById("adminStatActiveJobs");
    if (statJobs) statJobs.textContent = appState.jobs.length;

    // Update Tab Badges
    const tabJobsCount = document.getElementById("adminTabJobsCount");
    if (tabJobsCount) tabJobsCount.textContent = appState.jobs.length;

    const tabStudentsCount = document.getElementById("adminTabStudentsCount");
    if (tabStudentsCount) tabStudentsCount.textContent = appState.students.length;
  }

  function renderAdminStudentTable() {
    const tableBody = document.getElementById("adminStudentTableBody");
    if (!tableBody) return;

    appState.students = getStudents();
    const students = appState.students;

    const searchTxt = (document.getElementById("adminStudentSearch")?.value || "").toLowerCase().trim();
    const districtFilter = document.getElementById("adminFilterDistrict")?.value || "ALL";
    const statusFilter = document.getElementById("adminFilterStatus")?.value || "ALL";

    const filtered = students.filter(student => {
      const matchDistrict = (districtFilter === "ALL" || student.district === districtFilter);
      const matchStatus = (statusFilter === "ALL" || student.status === statusFilter);
      const matchQuery = (searchTxt === "" ||
        student.name.toLowerCase().includes(searchTxt) ||
        student.id.toLowerCase().includes(searchTxt) ||
        student.email.toLowerCase().includes(searchTxt) ||
        student.phone.includes(searchTxt) ||
        student.qualification.toLowerCase().includes(searchTxt) ||
        student.skills.some(sk => sk.toLowerCase().includes(searchTxt)));

      return matchDistrict && matchStatus && matchQuery;
    });

    tableBody.innerHTML = "";

    if (filtered.length === 0) {
      tableBody.innerHTML = `
        <tr>
          <td colspan="6" class="text-center py-4 text-muted">
            <i class="fas fa-search-minus fa-2x mb-2 d-block text-secondary"></i>
            No matching student records found.
          </td>
        </tr>
      `;
      return;
    }

    filtered.forEach(student => {
      let statusBadgeClass = "bg-secondary-subtle text-secondary";
      if (student.status === "Verified") statusBadgeClass = "bg-success-subtle text-success border border-success";
      else if (student.status === "Placed") statusBadgeClass = "bg-purple-subtle text-purple border border-purple";
      else if (student.status === "Shortlisted") statusBadgeClass = "bg-primary-subtle text-primary border border-primary";
      else if (student.status === "Under Review") statusBadgeClass = "bg-info-subtle text-info border border-info";

      const skillsBadges = student.skills.slice(0, 3).map(sk => 
        `<span class="badge bg-light text-dark border me-1 small" style="font-size: 0.7rem;">${sk}</span>`
      ).join('');

      const row = document.createElement("tr");
      row.innerHTML = `
        <td>
          <span class="badge bg-dark-subtle text-dark border fw-bold font-monospace">${student.id}</span>
        </td>
        <td>
          <div class="fw-bold text-dark">${student.name}</div>
          <div class="small text-muted font-monospace">@${student.username}</div>
        </td>
        <td>
          <div class="small fw-semibold mb-1">${student.qualification}</div>
          <div>${skillsBadges}</div>
        </td>
        <td>
          <span class="badge bg-primary-subtle text-primary border border-primary">${student.district}</span>
        </td>
        <td>
          <span class="badge ${statusBadgeClass}">
            <i class="fas fa-circle fa-2xs me-1"></i>${student.status}
          </span>
        </td>
        <td class="text-center">
          <div class="d-flex justify-content-center gap-1">
            <button class="btn btn-sm btn-outline-info" onclick="window.viewStudentDossier('${student.id}')" title="View Candidate Dossier">
              <i class="fas fa-eye"></i>
            </button>
            <button class="btn btn-sm btn-outline-primary" onclick="window.editStudentRecord('${student.id}')" title="Edit Student Record">
              <i class="fas fa-edit"></i>
            </button>
            <div class="dropdown d-inline-block">
              <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown" title="Change Status">
                <i class="fas fa-exchange-alt"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow-sm small">
                <li><h6 class="dropdown-header">Change Status</h6></li>
                <li><a class="dropdown-item" href="#" onclick="window.quickChangeStatus('${student.id}', 'Verified'); return false;"><i class="fas fa-check text-success me-2"></i> Verified</a></li>
                <li><a class="dropdown-item" href="#" onclick="window.quickChangeStatus('${student.id}', 'Shortlisted'); return false;"><i class="fas fa-bolt text-primary me-2"></i> Shortlisted</a></li>
                <li><a class="dropdown-item" href="#" onclick="window.quickChangeStatus('${student.id}', 'Placed'); return false;"><i class="fas fa-trophy text-purple me-2"></i> Placed</a></li>
                <li><a class="dropdown-item" href="#" onclick="window.quickChangeStatus('${student.id}', 'Under Review'); return false;"><i class="fas fa-eye text-info me-2"></i> Under Review</a></li>
                <li><a class="dropdown-item" href="#" onclick="window.quickChangeStatus('${student.id}', 'Pending'); return false;"><i class="fas fa-clock text-warning me-2"></i> Pending</a></li>
              </ul>
            </div>
            <button class="btn btn-sm btn-outline-danger" onclick="window.deleteStudentRecord('${student.id}')" title="Delete Student Record">
              <i class="fas fa-trash-alt"></i>
            </button>
          </div>
        </td>
      `;
      tableBody.appendChild(row);
    });
  }

  function renderAdminRecentApplications() {
    const tbody = document.getElementById("adminRecentApplicationsTableBody");
    if (!tbody) return;

    tbody.innerHTML = "";

    const applications = [
      { appId: "APP-9021", student: "Jagan Trivedi", job: "Junior Software Engineer", date: "12 Oct 2026", match: "96%", stage: "Shortlisted" },
      { appId: "APP-9022", student: "Priya Patel", job: "Cyber Security Analyst", date: "10 Oct 2026", match: "94%", stage: "Placed" },
      { appId: "APP-9023", student: "Rahul Solanki", job: "Apprentice Mechanical Supervisor", date: "09 Oct 2026", match: "82%", stage: "Verified" },
      { appId: "APP-9024", student: "Anjali Mehta", job: "Textile Automation Technician", date: "08 Oct 2026", match: "85%", stage: "Shortlisted" },
      { appId: "APP-9025", student: "Nisha Desai", job: "Solar Panel Maintenance Engineer", date: "07 Oct 2026", match: "88%", stage: "Under Review" }
    ];

    applications.forEach(app => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td class="font-monospace fw-bold small text-primary">${app.appId}</td>
        <td class="fw-semibold">${app.student}</td>
        <td>${app.job}</td>
        <td class="small text-muted">${app.date}</td>
        <td><span class="badge bg-success-subtle text-success border border-success">${app.match}</span></td>
        <td><span class="badge bg-${app.stage === 'Placed' ? 'success' : app.stage === 'Shortlisted' ? 'primary' : 'warning'} px-2 py-1">${app.stage}</span></td>
      `;
      tbody.appendChild(tr);
    });
  }

  // Admin Actions: Edit Student
  window.editStudentRecord = function(studentId) {
    appState.students = getStudents();
    const student = appState.students.find(s => s.id === studentId);
    if (!student) return;

    document.getElementById("editStudentId").value = student.id;
    document.getElementById("editStudentName").value = student.name;
    document.getElementById("editStudentUsername").value = student.username;
    document.getElementById("editStudentPassword").value = student.password;
    document.getElementById("editStudentEmail").value = student.email;
    document.getElementById("editStudentPhone").value = student.phone;
    document.getElementById("editStudentDistrict").value = student.district;
    document.getElementById("editStudentQual").value = student.qualification;
    document.getElementById("editStudentStatus").value = student.status;
    document.getElementById("editStudentSkills").value = student.skills.join(", ");

    editStudentModal?.show();
  };

  const editStudentForm = document.getElementById("editStudentForm");
  editStudentForm?.addEventListener("submit", (e) => {
    e.preventDefault();
    const studentId = document.getElementById("editStudentId").value;
    appState.students = getStudents();
    const index = appState.students.findIndex(s => s.id === studentId);
    if (index === -1) return;

    const skillsRaw = document.getElementById("editStudentSkills").value.trim();
    const skills = skillsRaw ? skillsRaw.split(",").map(s => s.trim()).filter(Boolean) : [];

    appState.students[index] = {
      ...appState.students[index],
      name: document.getElementById("editStudentName").value.trim(),
      username: document.getElementById("editStudentUsername").value.trim().toLowerCase(),
      password: document.getElementById("editStudentPassword").value.trim(),
      email: document.getElementById("editStudentEmail").value.trim(),
      phone: document.getElementById("editStudentPhone").value.trim(),
      district: document.getElementById("editStudentDistrict").value,
      qualification: document.getElementById("editStudentQual").value,
      status: document.getElementById("editStudentStatus").value,
      skills: skills
    };

    saveStudents(appState.students);
    editStudentModal?.hide();
    showToast(`Candidate ${studentId} updated successfully!`, "success", "check-circle");

    renderAdminDashboard();
  });

  // Admin Actions: Quick Change Status
  window.quickChangeStatus = function(studentId, newStatus) {
    appState.students = getStudents();
    const index = appState.students.findIndex(s => s.id === studentId);
    if (index === -1) return;

    appState.students[index].status = newStatus;
    saveStudents(appState.students);
    showToast(`Status for ${studentId} updated to: ${newStatus}`, "info", "exchange-alt");

    renderAdminDashboard();
  };

  // Admin Actions: Delete Student
  window.deleteStudentRecord = function(studentId) {
    if (!confirm(`Are you sure you want to delete candidate ${studentId}?`)) return;

    appState.students = getStudents();
    appState.students = appState.students.filter(s => s.id !== studentId);
    saveStudents(appState.students);
    showToast(`Candidate record ${studentId} deleted.`, "info", "trash-alt");

    renderAdminDashboard();
  };

  // View Student Dossier Modal
  window.viewStudentDossier = function(studentId) {
    appState.students = getStudents();
    const student = appState.students.find(s => s.id === studentId);
    if (!student) return;

    const container = document.getElementById("viewStudentDossierBody");
    if (!container) return;

    const initials = student.name.split(" ").map(p => p[0]).slice(0, 2).join("").toUpperCase();
    const skillsPills = student.skills.map(s => `<span class="badge bg-primary-subtle text-primary border border-primary px-2 py-1 me-1">${s}</span>`).join("");

    container.innerHTML = `
      <div class="p-3 bg-light rounded-4 border mb-3">
        <div class="d-flex align-items-center gap-3">
          <div class="student-avatar mb-0" style="width: 65px; height: 65px; font-size: 1.6rem;">${initials}</div>
          <div>
            <h5 class="fw-bold text-dark mb-0">${student.name}</h5>
            <div class="small text-muted font-monospace">@${student.username} | ${student.email}</div>
            <div class="d-flex gap-2 mt-1">
              <span class="badge bg-dark">${student.id}</span>
              <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> ${student.status}</span>
              <span class="badge bg-primary">${student.district}</span>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <div class="border rounded-3 p-3 h-100 bg-white">
            <h6 class="fw-bold text-primary mb-2"><i class="fas fa-graduation-cap me-1"></i> Academic Credentials</h6>
            <div class="small mb-1"><strong>Degree / Qualification:</strong> ${student.qualification}</div>
            <div class="small mb-1"><strong>Academic CGPA:</strong> ${student.cgpa || '8.5'} / 10.0</div>
            <div class="small"><strong>Registration Date:</strong> ${student.registeredDate}</div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="border rounded-3 p-3 h-100 bg-white">
            <h6 class="fw-bold text-success mb-2"><i class="fas fa-phone me-1"></i> Contact Information</h6>
            <div class="small mb-1"><strong>Mobile:</strong> +91 ${student.phone}</div>
            <div class="small mb-1"><strong>Email:</strong> ${student.email}</div>
            <div class="small"><strong>State:</strong> Gujarat, India</div>
          </div>
        </div>
      </div>

      <div class="border rounded-3 p-3 mb-3 bg-white">
        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-tools me-1 text-warning"></i> Verified Skills Dossier</h6>
        <div>${skillsPills}</div>
      </div>

      <div class="p-3 bg-warning-subtle border border-warning rounded-3 d-flex align-items-center justify-content-between">
        <div>
          <div class="fw-bold text-dark small"><i class="fas fa-certificate text-warning me-1"></i> DET Gujarat State Verification Stamp</div>
          <div class="small text-muted">Candidate authenticated by Directorate of Employment & Training, Government of Gujarat.</div>
        </div>
        <div class="badge bg-success px-3 py-2 fs-7">VERIFIED</div>
      </div>
    `;

    viewStudentModal?.show();
  };

  // Admin Actions: Export CSV
  function exportStudentsToCSV() {
    appState.students = getStudents();
    const list = appState.students;

    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "ID,Full Name,Username,Email,Phone,District,Qualification,Skills,Status,Registered Date\r\n";

    list.forEach(s => {
      const skillsStr = s.skills.join("; ");
      const row = `"${s.id}","${s.name}","${s.username}","${s.email}","${s.phone}","${s.district}","${s.qualification}","${skillsStr}","${s.status}","${s.registeredDate}"`;
      csvContent += row + "\r\n";
    });

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `GYRS_Student_Roster_${new Date().toISOString().slice(0, 10)}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    showToast("Student roster exported to CSV successfully!", "success", "file-download");
  }

  document.getElementById("exportCsvBtn")?.addEventListener("click", exportStudentsToCSV);

  // Student Roster Filters
  document.getElementById("adminStudentSearch")?.addEventListener("input", renderAdminStudentTable);
  document.getElementById("adminFilterDistrict")?.addEventListener("change", renderAdminStudentTable);
  document.getElementById("adminFilterStatus")?.addEventListener("change", renderAdminStudentTable);

  // ==========================================================================
  // 9. STUDENT PANEL: CANDIDATE DASHBOARD
  // ==========================================================================
  function renderStudentDashboard() {
    const student = getCurrentStudent();
    if (!student) return;

    // Avatar initials
    const initials = (student.name || "Candidate")
      .split(" ")
      .filter(Boolean)
      .map(n => n[0])
      .slice(0, 2)
      .join("")
      .toUpperCase() || "ST";

    const avatarInitialsEl = document.getElementById("studentAvatarInitials");
    if (avatarInitialsEl) avatarInitialsEl.textContent = initials;

    const dossierAvatarEl = document.getElementById("studentDossierAvatar");
    if (dossierAvatarEl) dossierAvatarEl.textContent = initials;

    // Header & Hero info
    const welcomeNameEl = document.getElementById("studentWelcomeName");
    if (welcomeNameEl) welcomeNameEl.textContent = student.name;

    const enrollmentIdEl = document.getElementById("studentEnrollmentId");
    if (enrollmentIdEl) enrollmentIdEl.textContent = student.id;

    const heroDegreeEl = document.getElementById("studentHeroDegree");
    if (heroDegreeEl) heroDegreeEl.textContent = student.qualification;

    const heroDistrictEl = document.getElementById("studentHeroDistrict");
    if (heroDistrictEl) heroDistrictEl.textContent = `${student.district}, Gujarat`;

    const heroEmailEl = document.getElementById("studentHeroEmail");
    if (heroEmailEl) heroEmailEl.textContent = student.email;

    const statusBadgeEl = document.getElementById("studentVerificationStatusBadge");
    if (statusBadgeEl) {
      statusBadgeEl.innerHTML = `<i class="fas fa-check-circle me-1"></i> State Verified Candidate (${student.status})`;
    }

    // Profile card & Dossier fields
    const dossierName = document.getElementById("studentDossierName");
    if (dossierName) dossierName.textContent = student.name;

    const dossierRoll = document.getElementById("studentDossierRoll");
    if (dossierRoll) dossierRoll.textContent = student.id;

    const dossierDegree = document.getElementById("studentDossierDegree");
    if (dossierDegree) dossierDegree.textContent = student.qualification;

    const dossierDistrict = document.getElementById("studentDossierDistrict");
    if (dossierDistrict) dossierDistrict.textContent = `${student.district}, Gujarat`;

    const dossierEmail = document.getElementById("studentDossierEmail");
    if (dossierEmail) dossierEmail.textContent = student.email;

    const dossierPhone = document.getElementById("studentDossierPhone");
    if (dossierPhone) dossierPhone.textContent = `+91 ${student.phone}`;

    // Skills badges
    const dossierSkills = document.getElementById("studentDossierSkills");
    if (dossierSkills) {
      dossierSkills.innerHTML = student.skills.map(s => 
        `<span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2 fw-semibold me-1 mb-1">${s}</span>`
      ).join("");
    }

    // KPI Counters
    const appliedCount = student.appliedJobIds ? student.appliedJobIds.length : 0;
    const appliedCountEl = document.getElementById("studentStatAppliedCount");
    if (appliedCountEl) appliedCountEl.textContent = appliedCount;

    const tabAppsCountEl = document.getElementById("studentTabAppsCount");
    if (tabAppsCountEl) tabAppsCountEl.textContent = appliedCount;

    const skillsCountEl = document.getElementById("studentStatSkillsCount");
    if (skillsCountEl) skillsCountEl.textContent = student.skills.length;

    // Render Student Applications List (Pipeline Stepper View)
    renderStudentApplicationsList(student);

    // Render Recommended Jobs
    renderStudentRecommendedJobs(student);

    // Render AI Skill Upgrade analyzer (only on dashboard page)
    if (document.getElementById("skillAnalysisResult")) analyzeStudentSkills(false);
  }

  // ==========================================================================
  // 9B. AI SKILL UPGRADE: resume analyzer + job unlocker (100% client-side)
  // ==========================================================================
  const SKILL_COURSES = {
    "Python": "Python for Everybody (NPTEL / Swayam) + Kaushalya Skill University IT module",
    "Web Development": "Full-Stack Web Dev (HTML/CSS/JS) — GKS certified course",
    "Database": "SQL & Database Management — Swayam NPTEL",
    "SQL": "SQL & Database Management — Swayam NPTEL",
    "Cyber Security": "Cyber Security Fundamentals — Gujarat Police Cyber Cell MoU course",
    "Networking": "Computer Networks (NPTEL) + CCNA basics",
    "Cloud": "Cloud Foundations (AWS Educate / NPTEL)",
    "Data Analysis": "Data Analysis with Python & Excel — GKS course",
    "Data Entry": "CCC + Gujarati/English typing practice (DET centres)",
    "Gujarati Typing": "CCC + Gujarati typing speed course (DET centres)",
    "MS Office": "CCC / MS Office certification",
    "Tally": "Tally + GST Compliance (ITI computer module)",
    "GST Compliance": "GST Compliance short course",
    "Electrical": "Electrician trade top-up (ITI advanced module)",
    "Solar Tech": "GEDA Solar PV installer training",
    "Maintenance": "Industrial maintenance & safety (ITI module)",
    "Machinery": "Machine operations practice (ITI workshop)",
    "AutoCAD": "AutoCAD 2D/3D drafting (Kaushalya Skill University)",
    "Automation": "PLC & Industrial Automation basics",
    "Textile Tech": "Textile processing module (Surat ITI cluster)",
    "Typing": "Typing speed + CCC course",
    "Computer Fundamentals": "CCC — Course on Computer Concepts",
    "Data Management": "Data management & MIS (Swayam)"
  };

  function getSkillVocabulary() {
    const set = new Set(Object.keys(SKILL_COURSES));
    getJobs().forEach(j => (j.skills || []).forEach(s => set.add(s)));
    getStudents().forEach(s => (s.skills || []).forEach(sk => set.add(sk)));
    return Array.from(set);
  }

  function detectSkillsFromText(text) {
    const lower = " " + (text || "").toLowerCase().replace(/[^a-z0-9+#. ]/g, " ") + " ";
    return getSkillVocabulary().filter(skill => {
      const k = skill.toLowerCase();
      return lower.includes(" " + k + " ") || lower.includes(" " + k + ",") || lower.includes("(" + k);
    });
  }

  function jobCoverage(job, skillSet) {
    const req = job.skills || [];
    if (!req.length) return { pct: 100, missing: [] };
    const have = req.filter(s => skillSet.has(s.toLowerCase()));
    const missing = req.filter(s => !skillSet.has(s.toLowerCase()));
    return { pct: Math.round((have.length / req.length) * 100), missing };
  }

  function analyzeStudentSkills(fromResume) {
    const resultBox = document.getElementById("skillAnalysisResult");
    const unlockBox = document.getElementById("skillUnlockList");
    const almostBox = document.getElementById("almostUnlockedJobs");
    if (!resultBox || !unlockBox || !almostBox) return;

    try {
    const student = getCurrentStudent();
    if (!student) {
      resultBox.innerHTML = `<div class="text-center py-4 text-muted small">Please sign in to see your skill report.</div>`;
      return;
    }

    const resumeText = document.getElementById("resumeTextInput")?.value || "";
    const resumeSkills = detectSkillsFromText(resumeText);
    const profileSkills = student.skills || [];
    const combined = new Set([...profileSkills.map(s => s.toLowerCase()), ...resumeSkills.map(s => s.toLowerCase())]);
    const displayName = (arr) => arr.length ? arr.join(", ") : "—";

    const jobs = getJobs();
    const rows = jobs.map(job => {
      const cov = jobCoverage(job, combined);
      return { job, ...cov };
    }).sort((a, b) => b.pct - a.pct);

    const avg = rows.length ? Math.round(rows.reduce((s, r) => s + r.pct, 0) / rows.length) : 0;
    const ready = rows.filter(r => r.pct === 100).length;

    resultBox.innerHTML = `
      <div class="row g-2 mb-3 text-center">
        <div class="col-4"><div class="border rounded-3 p-2 bg-light"><div class="fw-bold text-primary fs-5">${avg}%</div><div class="small text-muted fw-semibold">Avg Match</div></div></div>
        <div class="col-4"><div class="border rounded-3 p-2 bg-light"><div class="fw-bold text-success fs-5">${ready}/${jobs.length}</div><div class="small text-muted fw-semibold">Jobs Ready</div></div></div>
        <div class="col-4"><div class="border rounded-3 p-2 bg-light"><div class="fw-bold text-dark fs-5">${combined.size}</div><div class="small text-muted fw-semibold">Skills Found</div></div></div>
      </div>
      <div class="small mb-1"><strong>Profile skills (${profileSkills.length}):</strong> <span class="text-muted">${displayName(profileSkills)}</span></div>
      <div class="small mb-2"><strong>Resume-detected${fromResume ? "" : " (paste resume for deeper scan)"} (${resumeSkills.length}):</strong> <span class="text-muted">${displayName(resumeSkills)}</span></div>
      <div class="small fw-bold mb-1">Your match per job:</div>
      ${rows.map(r => `
        <div class="d-flex align-items-center gap-2 mb-1">
          <div class="small flex-grow-1 text-truncate"><strong>${r.job.title}</strong> <span class="text-muted">• ${r.job.district}</span></div>
          <div class="progress flex-shrink-0" style="width:110px;height:8px;"><div class="progress-bar ${r.pct === 100 ? 'bg-success' : r.pct >= 60 ? 'bg-primary' : 'bg-warning'}" style="width:${r.pct}%"></div></div>
          <span class="badge ${r.pct === 100 ? 'bg-success' : 'bg-light text-dark border'} small" style="min-width:46px;">${r.pct}%</span>
        </div>`).join("")}
      ${fromResume ? `<div class="alert alert-success small py-2 mt-2 mb-0"><i class="fas fa-check-circle me-1"></i> Resume analyzed. Missing skills below unlock locked jobs.</div>` : ""}
    `;

    // Which single new skill unlocks the most additional jobs?
    const missingCount = {};
    rows.forEach(r => r.missing.forEach(m => { missingCount[m] = (missingCount[m] || 0) + 1; }));
    const ranked = Object.entries(missingCount).sort((a, b) => b[1] - a[1]).slice(0, 6);

    const badge = document.getElementById("studentTabSkillsCount");
    if (badge) badge.textContent = ranked.length ? `${ranked.length} skills` : "";

    unlockBox.innerHTML = ranked.length ? ranked.map(([skill, n]) => `
      <div class="col-md-6">
        <div class="border rounded-3 p-2 bg-white h-100 d-flex justify-content-between align-items-center gap-2">
          <div>
            <div class="fw-bold small text-dark"><i class="fas fa-arrow-up text-success me-1"></i>${skill} <span class="badge bg-warning-subtle text-dark border ms-1">unlocks ${n} job${n > 1 ? "s" : ""}</span></div>
            <div class="text-muted" style="font-size:0.75rem;"><i class="fas fa-graduation-cap me-1"></i>${SKILL_COURSES[skill] || "Relevant state skill course (DET / ITI / Swayam)"}</div>
          </div>
          <button class="btn btn-sm btn-outline-success fw-bold flex-shrink-0" onclick="window.addSkillToProfile('${skill.replace(/'/g, "\\'")}')">+ Add</button>
        </div>
      </div>`).join("") :
      `<div class="col-12 text-center text-success small py-3"><i class="fas fa-trophy fa-2x mb-2 d-block text-warning"></i><strong>All jobs unlocked!</strong> Your skills cover every live vacancy.</div>`;

    const almost = rows.filter(r => r.pct < 100 && r.missing.length <= 2).slice(0, 4);
    almostBox.innerHTML = almost.length ? almost.map(r => `
      <div class="col-md-6">
        <div class="naukri-job-card h-100">
          <span class="badge bg-warning-subtle text-dark border mb-2">Missing: ${r.missing.join(", ")}</span>
          <h6 class="naukri-job-title mb-1">${r.job.title}</h6>
          <div class="small text-muted mb-2"><i class="fas fa-building me-1"></i> ${r.job.department} • ${r.job.district}</div>
          <button class="btn btn-sm btn-primary fw-bold w-100" onclick="window.openJobDetails(${r.job.id})"><i class="fas fa-eye me-1"></i> View & Apply</button>
        </div>
      </div>`).join("") :
      `<div class="col-12 text-center text-muted small py-3">No near-miss jobs — every remaining job needs 3+ new skills. Start with the top unlock skill above.</div>`;
    } catch (err) {
      console.error("Skill analysis failed:", err);
      resultBox.innerHTML = `<div class="alert alert-warning small mb-0"><i class="fas fa-exclamation-triangle me-1"></i> Could not analyze right now. Please refresh the page and try again.</div>`;
      unlockBox.innerHTML = "";
      almostBox.innerHTML = "";
    }
  }

  window.addSkillToProfile = function(skill) {
    const student = getCurrentStudent();
    if (!student) return;
    if (!student.skills.includes(skill)) {
      student.skills.push(skill);
      saveStudents(appState.students);
      if (appState.session && appState.session.role === "student") {
        appState.session.user = student;
        setSession(appState.session);
      }
      renderStudentDashboard();
      showToast(`"${skill}" added to your profile! Re-analyzed matches.`, "success", "plus-circle");
    } else {
      showToast(`"${skill}" is already in your profile.`, "info", "info-circle");
    }
  };

  document.getElementById("analyzeResumeBtn")?.addEventListener("click", () => analyzeStudentSkills(true));
  document.getElementById("resumeFileInput")?.addEventListener("change", async (e) => {
    const f = e.target.files[0];
    if (!f) return;
    const text = await f.text();
    const box = document.getElementById("resumeTextInput");
    if (box) box.value = text.slice(0, 20000);
    showToast("Resume file loaded — click Analyze My Skills.", "info", "file-alt");
  });
  // Re-run analysis every time the Skill tab is opened (fixes blank/stale view)
  document.getElementById("tabStudentSkills-tab")?.addEventListener("shown.bs.tab", () => analyzeStudentSkills(false));

  // ==========================================================================
  // 9C. CANDIDATE NOTIFICATION TEMPLATES (WhatsApp + Email share)
  // ==========================================================================
  function siteBaseUrl() {
    return window.location.origin + window.location.pathname.replace(/[^/]*$/, "");
  }

  function buildJobShareMessages(job) {
    const jd = withJobDefaults(job);
    const detailsUrl = siteBaseUrl() + `job-details.html?id=${job.id}`;
    const whatsapp =
`New Govt Job Alert — GYRS Gujarat
${jd.title}
${jd.department}, ${jd.district}
Posts: ${jd.totalPosts} | Salary: ${jd.salary}
Last Date: ${jd.lastDate}
Notification: ${jd.notificationPdf}
Details & Apply: ${detailsUrl}`;
    const emailSubject = `New Vacancy: ${jd.title} (${jd.department}) — Apply by ${jd.lastDate}`;
    const emailBody =
`Dear Candidate,

A new verified vacancy is live on GYRS Gujarat Youth Rojgar Setu:

Job: ${jd.title}
Department: ${jd.department}
Location: ${jd.district}, Gujarat
Advt No: ${jd.advtNo} | Posts: ${jd.totalPosts}
Salary: ${jd.salary}
Qualification: ${jd.qualification}
Last Date: ${jd.lastDate}

Notification PDF: ${jd.notificationPdf}
View full details & Apply Now: ${detailsUrl}

— GYRS Portal`;
    return { whatsapp, emailSubject, emailBody, detailsUrl };
  }

  window.shareJobWhatsApp = function(jobId) {
    const job = getJobs().find(j => j.id === Number(jobId));
    if (!job) return;
    const { whatsapp } = buildJobShareMessages(job);
    window.open("https://wa.me/?text=" + encodeURIComponent(whatsapp), "_blank");
  };

  window.shareJobEmail = function(jobId) {
    const job = getJobs().find(j => j.id === Number(jobId));
    if (!job) return;
    const { emailSubject, emailBody } = buildJobShareMessages(job);
    window.location.href = "mailto:?subject=" + encodeURIComponent(emailSubject) + "&body=" + encodeURIComponent(emailBody);
  };

  window.copyJobAlert = async function(jobId) {
    const job = getJobs().find(j => j.id === Number(jobId));
    if (!job) return;
    try {
      await navigator.clipboard.writeText(buildJobShareMessages(job).whatsapp);
      showToast("Job alert message copied — paste it in WhatsApp / Email.", "success", "copy");
    } catch {
      showToast("Copy blocked by browser — use WhatsApp / Email buttons.", "info", "info-circle");
    }
  };

  // Live client preview of notification templates (notification-preview.html)
  function renderNotificationPreview() {
    const select = document.getElementById("previewJobSelect");
    if (!select) return;
    const jobs = getJobs();
    const params = new URLSearchParams(window.location.search);
    let currentId = Number(params.get("id")) || (jobs[0] && jobs[0].id);

    select.innerHTML = jobs.map(j => `<option value="${j.id}">${j.title} — ${j.department} (${j.district})</option>`).join("");
    select.value = currentId;

    function paint() {
      const job = jobs.find(j => j.id === Number(select.value)) || jobs[0];
      if (!job) return;
      const { whatsapp, emailSubject, emailBody } = buildJobShareMessages(job);
      const bubble = document.getElementById("waPreviewBubble");
      if (bubble) bubble.textContent = whatsapp;
      const time = document.getElementById("waPreviewTime");
      if (time) time.textContent = new Date().toLocaleTimeString("en-IN", { hour: "2-digit", minute: "2-digit" });
      const subj = document.getElementById("mailPreviewSubject");
      if (subj) subj.textContent = emailSubject;
      const body = document.getElementById("mailPreviewBody");
      if (body) body.textContent = emailBody;
      document.getElementById("waPreviewSendBtn")?.setAttribute("onclick", `window.shareJobWhatsApp(${job.id})`);
      document.getElementById("mailPreviewSendBtn")?.setAttribute("onclick", `window.shareJobEmail(${job.id})`);
      document.getElementById("copyPreviewBtn")?.setAttribute("onclick", `window.copyJobAlert(${job.id})`);
    }

    select.addEventListener("change", paint);
    paint();
  }

  function renderStudentApplicationsList(student) {
    const container = document.getElementById("studentApplicationsTrackerList");
    if (!container) return;

    container.innerHTML = "";
    const appliedIds = student.appliedJobIds || [];
    const allJobs = getJobs();

    if (appliedIds.length === 0) {
      container.innerHTML = `
        <div class="text-center py-5 bg-white rounded-4 border p-4">
          <div class="rounded-circle bg-primary-subtle text-primary mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; font-size: 1.8rem;">
            <i class="fas fa-paper-plane"></i>
          </div>
          <h5 class="fw-bold text-dark mb-1">No Active Applications in Pipeline</h5>
          <p class="text-muted small mb-3" style="max-width: 480px; margin: 0 auto;">
            Your candidate profile is 96% complete and verified. Explore recommended opportunities below — open a job, read its details and press Apply Now.
          </p>
          <button class="btn btn-primary fw-bold px-4" onclick="document.getElementById('tabStudentMatches-tab')?.click()">
            <i class="fas fa-sparkles me-1"></i> View Recommended Matches
          </button>
        </div>
      `;
      return;
    }

    appliedIds.forEach((jobId, index) => {
      const job = allJobs.find(j => j.id === jobId);
      if (!job) return;

      const appId = `APP-${student.id.replace('GYRS-2026-', '')}-0${index + 1}`;
      
      // Determine stage and UI metadata
      let statusBadge = "";
      let stepperHtml = "";
      let calloutBoxHtml = "";

      if (index === 0) {
        // Stage 3: Interview Scheduled
        statusBadge = `<span class="status-pill status-verified"><i class="fas fa-calendar-check me-1"></i>Interview Scheduled</span>`;
        stepperHtml = `
          <div class="application-stepper">
            <div class="stepper-stage completed">
              <div class="stage-icon"><i class="fas fa-check"></i></div>
              <div class="stage-title">Application Submitted<br><small class="text-muted fw-normal">12 Oct 2026</small></div>
            </div>
            <div class="stepper-stage completed">
              <div class="stage-icon"><i class="fas fa-check"></i></div>
              <div class="stage-title">Document Screening<br><small class="text-success fw-bold">Verified</small></div>
            </div>
            <div class="stepper-stage active">
              <div class="stage-icon"><i class="fas fa-calendar-alt"></i></div>
              <div class="stage-title text-primary fw-bold">Interview Call<br><small class="text-primary fw-bold">Oct 28, 2026</small></div>
            </div>
            <div class="stepper-stage">
              <div class="stage-icon">4</div>
              <div class="stage-title">Final Offer<br><small class="text-muted fw-normal">Pending</small></div>
            </div>
          </div>
        `;
        calloutBoxHtml = `
          <div class="interview-callout-box">
            <div class="d-flex align-items-center gap-3">
              <div class="rounded-circle bg-warning text-dark p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                <i class="fas fa-calendar-check fa-lg"></i>
              </div>
              <div>
                <div class="fw-bold text-dark small">Official Interview Call Letter Issued</div>
                <div class="small text-muted">Reporting Time: <strong class="text-dark">10:30 AM, Oct 28, 2026</strong> • Venue: Directorate of Employment & Training (DET), Sector 10, Gandhinagar</div>
              </div>
            </div>
            <div class="d-flex gap-2">
              <a href="job-details.html?id=${job.id}" class="btn btn-sm btn-primary fw-bold px-3">
                <i class="fas fa-file-download me-1"></i> Download Call Letter
              </a>
              <button class="btn btn-sm btn-outline-secondary fw-semibold" onclick="window.openJobDetailsModal(${job.id})">
                <i class="fas fa-info-circle me-1"></i> Job Details
              </button>
            </div>
          </div>
        `;
      } else if (index === 1) {
        // Stage 2: Shortlisted
        statusBadge = `<span class="status-pill status-shortlisted"><i class="fas fa-bolt me-1"></i>Shortlisted</span>`;
        stepperHtml = `
          <div class="application-stepper">
            <div class="stepper-stage completed">
              <div class="stage-icon"><i class="fas fa-check"></i></div>
              <div class="stage-title">Application Submitted<br><small class="text-muted fw-normal">10 Oct 2026</small></div>
            </div>
            <div class="stepper-stage completed">
              <div class="stage-icon"><i class="fas fa-check"></i></div>
              <div class="stage-title">Document Screening<br><small class="text-success fw-bold">Passed</small></div>
            </div>
            <div class="stepper-stage active">
              <div class="stage-icon"><i class="fas fa-user-clock"></i></div>
              <div class="stage-title text-primary fw-bold">Departmental Review<br><small class="text-primary fw-bold">In Progress</small></div>
            </div>
            <div class="stepper-stage">
              <div class="stage-icon">4</div>
              <div class="stage-title">Final Offer<br><small class="text-muted fw-normal">Upcoming</small></div>
            </div>
          </div>
        `;
        calloutBoxHtml = `
          <div class="interview-callout-box" style="background: #eff6ff; border-color: #bfdbfe;">
            <div class="d-flex align-items-center gap-3">
              <div class="rounded-circle bg-primary text-white p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                <i class="fas fa-user-check fa-lg"></i>
              </div>
              <div>
                <div class="fw-bold text-dark small">Candidate Dossier Shortlisted</div>
                <div class="small text-muted">Your verified profile score qualifies for direct screening. Interview committee is reviewing applications.</div>
              </div>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-primary fw-semibold" onclick="window.openJobDetailsModal(${job.id})">
                <i class="fas fa-info-circle me-1"></i> Review Requirements
              </button>
            </div>
          </div>
        `;
      } else {
        // Stage 1/2: Under Review
        statusBadge = `<span class="status-pill status-review"><i class="fas fa-clock me-1"></i>Under Review</span>`;
        stepperHtml = `
          <div class="application-stepper">
            <div class="stepper-stage completed">
              <div class="stage-icon"><i class="fas fa-check"></i></div>
              <div class="stage-title">Application Submitted<br><small class="text-muted fw-normal">08 Oct 2026</small></div>
            </div>
            <div class="stepper-stage active">
              <div class="stage-icon"><i class="fas fa-hourglass-half"></i></div>
              <div class="stage-title text-primary fw-bold">Document Screening<br><small class="text-primary fw-bold">Scrutiny</small></div>
            </div>
            <div class="stepper-stage">
              <div class="stage-icon">3</div>
              <div class="stage-title">Interview Round<br><small class="text-muted fw-normal">Pending</small></div>
            </div>
            <div class="stepper-stage">
              <div class="stage-icon">4</div>
              <div class="stage-title">Final Offer<br><small class="text-muted fw-normal">Pending</small></div>
            </div>
          </div>
        `;
        calloutBoxHtml = `
          <div class="interview-callout-box" style="background: #f8fafc; border-color: #e2e8f0;">
            <div class="d-flex align-items-center gap-3">
              <div class="rounded-circle bg-secondary text-white p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                <i class="fas fa-hourglass-half fa-lg"></i>
              </div>
              <div>
                <div class="fw-bold text-dark small">Application Received & Authenticated</div>
                <div class="small text-muted">Candidate credentials transmitted to ${job.department}. Verification scrutiny in progress.</div>
              </div>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-secondary fw-semibold" onclick="window.openJobDetailsModal(${job.id})">
                <i class="fas fa-info-circle me-1"></i> Job Details
              </button>
            </div>
          </div>
        `;
      }

      const itemCard = document.createElement("div");
      itemCard.className = "application-tracker-item";
      itemCard.innerHTML = `
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
          <div>
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
              <span class="badge bg-dark-subtle text-dark border font-monospace fw-bold">${appId}</span>
              ${statusBadge}
              <span class="badge bg-light text-muted border">${job.district}</span>
            </div>
            <h5 class="fw-bold text-dark mb-1">${job.title}</h5>
            <div class="small text-muted d-flex align-items-center gap-3 flex-wrap">
              <span><i class="fas fa-building text-primary me-1"></i> ${job.department}</span>
              <span><i class="fas fa-rupee-sign text-success me-1"></i> <strong class="text-success">${job.salary}</strong></span>
              <span><i class="fas fa-briefcase text-secondary me-1"></i> ${job.type}</span>
            </div>
          </div>
          <div>
            <button class="btn btn-sm btn-outline-secondary" onclick="window.openJobDetailsModal(${job.id})">
              <i class="fas fa-eye me-1"></i> View Vacancy
            </button>
          </div>
        </div>

        ${stepperHtml}

        ${calloutBoxHtml}
      `;
      container.appendChild(itemCard);
    });
  }

  function renderStudentRecommendedJobs(student) {
    const grid = document.getElementById("studentRecommendedJobsGrid");
    if (!grid) return;

    grid.innerHTML = "";
    const appliedIds = student.appliedJobIds || [];
    const allJobs = getJobs();

    allJobs.forEach(job => {
      const isApplied = appliedIds.includes(job.id);
      const jd = withJobDefaults(job);

      const col = document.createElement("div");
      col.className = "col-md-6";
      col.innerHTML = `
        <div class="naukri-job-card h-100" data-job-details="${job.id}" style="cursor:pointer;" title="Click to view full details, notification & apply">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="badge bg-primary-subtle text-primary border border-primary small">${job.district}</span>
            <span class="company-rating-badge"><i class="fas fa-star text-warning"></i> ${job.matchScore || 90}% Match</span>
          </div>
          <h6 class="naukri-job-title mb-1">${job.title}</h6>
          <div class="small text-muted mb-2"><i class="fas fa-building me-1"></i> ${job.department}</div>
          <div class="small text-success fw-bold mb-1"><i class="fas fa-rupee-sign me-1"></i> ${job.salary}</div>
          <div class="small text-danger fw-semibold mb-2"><i class="fas fa-calendar-times me-1"></i>Last Date: ${jd.lastDate}</div>
          <div class="d-flex flex-wrap gap-1 mb-3">
            ${job.skills.slice(0, 3).map(sk => `<span class="badge bg-light text-dark border small" style="font-size: 0.72rem;">${sk}</span>`).join('')}
          </div>
          <div class="mt-auto d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary fw-semibold px-2" data-job-details="${job.id}" title="View full details, notification PDF and Apply Now">
              <i class="fas fa-eye"></i> Details
            </button>
            ${isApplied ? 
              `<button class="btn btn-sm btn-success flex-grow-1 fw-bold disabled" disabled><i class="fas fa-check-circle me-1"></i> Applied</button>` :
              `<button class="btn btn-sm btn-primary flex-grow-1 fw-bold" data-job-details="${job.id}"><i class="fas fa-eye me-1"></i> View & Apply</button>`
            }
          </div>
        </div>
      `;
      grid.appendChild(col);
    });
  }

  window.applyStudentJob = function(jobId) {
    const student = getCurrentStudent();
    if (!student) {
      showToast("Please sign in as a candidate to apply for this job.", "info", "sign-in-alt");
      setTimeout(() => {
        window.location.href = "student-login.html";
      }, 700);
      return;
    }

    if (!student.appliedJobIds) student.appliedJobIds = [];
    if (!student.appliedJobIds.includes(jobId)) {
      student.appliedJobIds.push(jobId);
      saveStudents(appState.students);
      if (typeof renderStudentDashboard === "function" && document.getElementById("studentApplicationsTrackerList")) {
        renderStudentDashboard();
      }
      showToast("Application tracked successfully! Opening the official Apply portal...", "success", "paper-plane");
    } else {
      showToast("You have already applied for this position.", "info", "info-circle");
    }
  };

  // View Vacancy Details Modal Handler — Full detail -> Notification PDF -> Apply Now flow
  // Design/layout classes unchanged, only content structure extended.
  function withJobDefaults(job) {
    return {
      advtNo: job.advtNo || `GYRS/2026/${String(job.id).padStart(2, "0")}`,
      totalPosts: job.totalPosts || "As per norms",
      postedDate: job.postedDate || "Oct 2026",
      lastDate: job.lastDate || "Refer notification",
      ageLimit: job.ageLimit || "18 - 35 Years (as per Govt rules)",
      fees: job.fees || "Refer official notification",
      vacancyBreakup: job.vacancyBreakup || "Category-wise as per roster",
      eligibility: job.eligibility || [job.qualification, ...(job.skills || []).map(s => `Working knowledge of ${s}`)],
      responsibilities: job.responsibilities || [job.description],
      selectionProcess: job.selectionProcess || ["Application Scrutiny", "Written / Skill Test", "Interview", "Document Verification"],
      howToApply: job.howToApply || ["Read the notification PDF carefully.", "Click Apply Now to open the official portal.", "Fill the form, upload documents and pay fee.", "Track status in GYRS Candidate Dashboard."],
      notificationPdf: job.notificationPdf || job.ojasUrl || "https://ojas.gujarat.gov.in/",
      officialWebsite: job.officialWebsite || "https://ojas.gujarat.gov.in/",
      applyUrl: job.applyUrl || job.ojasUrl || "https://ojas.gujarat.gov.in/",
      ...job
    };
  }

  // Job details now live on a dedicated responsive page (job-details.html?id=...).
  // Keep the old name so every existing card/button keeps working.
  window.openJobDetailsModal = function(jobId) {
    window.location.href = `job-details.html?id=${jobId}`;
  };
  window.openJobDetails = window.openJobDetailsModal;

  // Renders the dedicated job-details page from ?id=
  function renderJobDetailsPage() {
    const container = document.getElementById("jobDetailsContent");
    if (!container) return;

    const params = new URLSearchParams(window.location.search);
    const jobId = Number(params.get("id"));
    const jobs = getJobs();
    const raw = jobs.find(j => j.id === jobId);
    const linksBox = document.getElementById("jobDetailsLinks");
    const similarBox = document.getElementById("similarJobsList");
    const crumb = document.getElementById("breadcrumbJobTitle");

    if (!raw) {
      container.innerHTML = `
        <div class="text-center py-5 bg-white rounded-4 border p-4">
          <i class="fas fa-search-minus fa-3x text-muted mb-3"></i>
          <h5 class="fw-bold">Job Not Found</h5>
          <p class="text-muted small">This vacancy may have been removed or the link is invalid.</p>
          <a href="index.html#jobExplorer" class="btn btn-primary btn-sm fw-bold px-3 mt-2">
            <i class="fas fa-arrow-left me-1"></i> Back to All Jobs
          </a>
        </div>`;
      if (linksBox) linksBox.innerHTML = "";
      return;
    }

    const job = withJobDefaults(raw);
    if (crumb) crumb.textContent = job.title;
    document.title = `${job.title} | GYRS Gujarat`;

    const student = getCurrentStudent();
    const isApplied = student && student.appliedJobIds && student.appliedJobIds.includes(job.id);

    const skillsHtml = job.skills.map(s =>
      `<span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2 fw-semibold me-1 mb-1">${s}</span>`
    ).join("");
    const listHtml = (arr) => (arr || []).map(t => `<li class="small text-muted mb-1">${t}</li>`).join("");
    const stepsHtml = (arr) => (arr || []).map((t, i) => `
      <div class="d-flex gap-2 mb-2">
        <span class="badge bg-primary rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:24px;height:24px;">${i + 1}</span>
        <div class="small text-muted">${t}</div>
      </div>`).join("");

    container.innerHTML = `
      <div class="naukri-job-card mb-3">
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
          <span class="badge bg-primary-subtle text-primary border border-primary">${job.district}</span>
          <span class="badge bg-secondary-subtle text-secondary border">${job.type}</span>
          <span class="company-rating-badge"><i class="fas fa-star text-warning"></i> ${job.matchScore || 90}% Match</span>
        </div>
        <h2 class="fw-bold text-dark mb-1" style="font-size:1.5rem;">${job.title}</h2>
        <div class="text-muted small mb-1"><i class="fas fa-building text-primary me-1"></i> ${job.department}</div>
        <div class="small text-muted"><i class="fas fa-hashtag me-1"></i>Advt No: <strong class="text-dark">${job.advtNo}</strong> &nbsp;|&nbsp; <i class="fas fa-users me-1"></i>Total Posts: <strong class="text-dark">${job.totalPosts}</strong></div>
        <div class="fs-5 fw-bold text-success mt-2"><i class="fas fa-rupee-sign me-1"></i>${job.salary} <span class="small text-muted fw-normal">/ month</span></div>
        <div class="row g-2 mt-3">
          <div class="col-6 col-md-3"><div class="border rounded-3 p-2 bg-light text-center"><div class="small text-muted fw-semibold">Posted On</div><div class="small fw-bold text-dark">${job.postedDate}</div></div></div>
          <div class="col-6 col-md-3"><div class="border rounded-3 p-2 bg-light text-center border-danger"><div class="small text-muted fw-semibold">Last Date</div><div class="small fw-bold text-danger">${job.lastDate}</div></div></div>
          <div class="col-6 col-md-3"><div class="border rounded-3 p-2 bg-light text-center"><div class="small text-muted fw-semibold">Location</div><div class="small fw-bold text-dark">${job.district}, Gujarat</div></div></div>
          <div class="col-6 col-md-3"><div class="border rounded-3 p-2 bg-light text-center"><div class="small text-muted fw-semibold">Job Type</div><div class="small fw-bold text-dark">${job.type}</div></div></div>
        </div>
      </div>

      <div class="naukri-job-card mb-3">
        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-align-left text-primary me-2"></i>Job Description</h6>
        <p class="text-muted small mb-0" style="line-height: 1.7;">${job.description}</p>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <div class="naukri-job-card h-100">
            <h6 class="fw-bold text-dark small mb-2"><i class="fas fa-graduation-cap text-primary me-1"></i> Eligibility & Qualification</h6>
            <ul class="ps-3 mb-2">${listHtml(job.eligibility)}</ul>
            <div class="small"><strong>Qualification:</strong> <span class="text-muted">${job.qualification}</span></div>
            <div class="small mt-1"><strong>Age Limit:</strong> <span class="text-muted">${job.ageLimit}</span></div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="naukri-job-card h-100">
            <h6 class="fw-bold text-dark small mb-2"><i class="fas fa-users text-success me-1"></i> Vacancy & Fee Details</h6>
            <div class="small mb-1"><strong>Total Posts:</strong> ${job.totalPosts}</div>
            <div class="small mb-1"><strong>Breakup:</strong> <span class="text-muted">${job.vacancyBreakup}</span></div>
            <div class="small"><strong>Application Fee:</strong> <span class="text-muted">${job.fees}</span></div>
          </div>
        </div>
      </div>

      <div class="naukri-job-card mb-3">
        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-tasks text-warning me-2"></i> Roles & Responsibilities</h6>
        <ul class="ps-3 mb-3">${listHtml(job.responsibilities)}</ul>
        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-filter text-info me-2"></i> Selection Process</h6>
        <div class="d-flex flex-wrap gap-1 mb-3">${job.selectionProcess.map(s => `<span class="badge bg-light text-dark border small">${s}</span>`).join('<span class="text-muted small px-1">→</span>')}</div>
        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-tools text-primary me-2"></i>Required Skills</h6>
        <div class="d-flex flex-wrap">${skillsHtml}</div>
      </div>

      <div class="naukri-job-card mb-3" style="border-color:#fca5a5;">
        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-file-pdf text-danger me-2"></i> Official Notification & Documents</h6>
        <p class="small text-muted mb-2">Read the official advertisement PDF before applying — vacancy, reservation, syllabus and instructions.</p>
        <div class="d-flex gap-2 flex-wrap">
          <a href="${job.notificationPdf}" target="_blank" class="btn btn-sm btn-danger fw-bold px-3"><i class="fas fa-file-pdf me-1"></i> View / Download Notification PDF</a>
        </div>
      </div>

      <div class="naukri-job-card mb-3">
        <h6 class="fw-bold text-dark mb-2"><i class="fas fa-list-ol text-success me-2"></i> How to Apply (Step-by-Step)</h6>
        ${stepsHtml(job.howToApply)}
        <div class="d-grid gap-2 mt-3">
          ${isApplied ?
            `<a href="${job.applyUrl}" target="_blank" class="btn btn-success fw-bold"><i class="fas fa-check-circle me-1"></i> Already Tracked — Open Apply Portal</a>` :
            `<a href="${job.applyUrl}" target="_blank" onclick="window.applyStudentJob(${job.id})" class="btn btn-primary fw-bold btn-lg"><i class="fas fa-paper-plane me-1"></i> Apply Now</a>
             <div class="small text-muted text-center">You will be redirected to the official application portal. Your application will also be tracked in the Candidate Dashboard.</div>`
          }
        </div>
      </div>
    `;

    if (linksBox) {
      linksBox.innerHTML = `
        <a href="${job.notificationPdf}" target="_blank" class="btn btn-danger btn-sm fw-bold"><i class="fas fa-file-pdf me-1"></i> Notification PDF</a>
        ${isApplied ?
          `<a href="${job.applyUrl}" target="_blank" class="btn btn-success btn-sm fw-bold"><i class="fas fa-check-circle me-1"></i> Open Apply Portal</a>` :
          `<a href="${job.applyUrl}" target="_blank" onclick="window.applyStudentJob(${job.id})" class="btn btn-primary btn-sm fw-bold"><i class="fas fa-paper-plane me-1"></i> Apply Now</a>`
        }
        <div class="small fw-bold text-muted mt-1"><i class="fas fa-share-alt me-1"></i>Share this job alert</div>
        <button class="btn btn-success btn-sm fw-bold" onclick="window.shareJobWhatsApp(${job.id})"><i class="fab fa-whatsapp me-1"></i> WhatsApp Alert</button>
        <button class="btn btn-outline-info btn-sm fw-semibold" onclick="window.shareJobEmail(${job.id})"><i class="fas fa-envelope me-1"></i> Email Alert</button>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.copyJobAlert(${job.id})"><i class="fas fa-copy me-1"></i> Copy Message</button>
        <a href="index.html#jobExplorer" class="btn btn-outline-primary btn-sm fw-semibold"><i class="fas fa-arrow-left me-1"></i> All Jobs</a>
      `;
    }

    if (similarBox) {
      const similar = jobs.filter(j => j.id !== job.id && (j.district === job.district || j.type === job.type)).slice(0, 4);
      const list = (similar.length ? similar : jobs.filter(j => j.id !== job.id).slice(0, 4));
      similarBox.innerHTML = list.map(j => `
        <a href="job-details.html?id=${j.id}" class="category-card" style="padding:0.85rem;">
          <div class="category-icon-box" style="width:38px;height:38px;font-size:1rem;"><i class="fas fa-briefcase"></i></div>
          <div>
            <div class="fw-bold text-dark small">${j.title}</div>
            <div class="text-muted" style="font-size:0.75rem;">${j.department} • ${j.district}</div>
          </div>
        </a>`).join("");
    }
  }

  // Student Edit Profile Modal Handlers
  function openEditProfileModal() {
    const student = getCurrentStudent();
    if (!student) return;

    document.getElementById("selfProfileName").value = student.name;
    document.getElementById("selfProfileDistrict").value = student.district;
    document.getElementById("selfProfileQual").value = student.qualification;
    document.getElementById("selfProfilePhone").value = student.phone;
    document.getElementById("selfProfileSkills").value = student.skills.join(", ");

    editProfileModal?.show();
  }

  document.getElementById("studentEditProfileBtn")?.addEventListener("click", openEditProfileModal);

  const editProfileForm = document.getElementById("editProfileForm");
  editProfileForm?.addEventListener("submit", (e) => {
    e.preventDefault();
    const student = getCurrentStudent();
    if (!student) return;

    const skillsRaw = document.getElementById("selfProfileSkills").value.trim();
    const skills = skillsRaw ? skillsRaw.split(",").map(s => s.trim()).filter(Boolean) : [];

    student.name = document.getElementById("selfProfileName").value.trim();
    student.district = document.getElementById("selfProfileDistrict").value;
    student.qualification = document.getElementById("selfProfileQual").value;
    student.phone = document.getElementById("selfProfilePhone").value.trim();
    student.skills = skills;

    saveStudents(appState.students);
    if (appState.session && appState.session.role === "student") {
      appState.session.user = student;
      setSession(appState.session);
    }

    editProfileModal?.hide();
    updateSessionUI();
    renderStudentDashboard();
    showToast("Profile details updated successfully!", "success", "check-circle");
  });

  // ==========================================================================
  // 10. PUBLIC VIEW: NAUKRI.COM JOB FILTER ENGINE & INTERACTION
  // ==========================================================================
  function initJobFilterEngine() {
    const filterDistrict = document.getElementById("filterDistrict");
    const filterSkill = document.getElementById("filterSkill");
    const filterSearch = document.getElementById("filterSearch");
    const filterType = document.getElementById("filterType");
    const resetFiltersBtn = document.getElementById("resetFiltersBtn");
    const filteredJobsGrid = document.getElementById("filteredJobsGrid");
    const filteredJobCount = document.getElementById("filteredJobCount");
    const naukriSearchBtn = document.getElementById("naukriSearchBtn");

    function renderJobs() {
      if (!filteredJobsGrid) return;

      const jobs = getJobs();
      const selDistrict = filterDistrict?.value || "ALL";
      const selSkill = filterSkill?.value || "ALL";
      const selType = filterType?.value || "ALL";
      const searchTxt = (filterSearch?.value || "").toLowerCase().trim();

      const filtered = jobs.filter(job => {
        const matchDistrict = (selDistrict === "ALL" || job.district === selDistrict);
        const matchSkill = (selSkill === "ALL" || job.skills.some(s => s.toLowerCase().includes(selSkill.toLowerCase())));
        const matchType = (selType === "ALL" || job.type === selType);
        const matchQuery = (searchTxt === "" || 
          job.title.toLowerCase().includes(searchTxt) || 
          job.department.toLowerCase().includes(searchTxt) ||
          job.description.toLowerCase().includes(searchTxt) ||
          job.skills.some(s => s.toLowerCase().includes(searchTxt)));

        return matchDistrict && matchSkill && matchType && matchQuery;
      });

      if (filteredJobCount) filteredJobCount.textContent = filtered.length;
      filteredJobsGrid.innerHTML = "";

      if (filtered.length === 0) {
        filteredJobsGrid.innerHTML = `
          <div class="col-12 text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h5 class="fw-bold">No Matching Opportunities Found</h5>
            <p class="text-muted small">Please modify your search keywords, location filter, or skill selection.</p>
            <button class="btn btn-outline-primary btn-sm fw-bold px-3 mt-2" onclick="document.getElementById('resetFiltersBtn')?.click()">
              <i class="fas fa-undo me-1"></i> Reset All Filters
            </button>
          </div>
        `;
        return;
      }

      filtered.forEach(job => {
        const jd = withJobDefaults(job);
        const skillsBadges = job.skills.map(s => `<span class="naukri-skill-pill">${s}</span>`).join('');
        
        const cardHtml = `
          <div class="col-md-6">
            <div class="naukri-job-card" data-job-details="${job.id}" style="cursor:pointer;" title="Click to view full details, notification & apply">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="badge bg-primary-subtle text-primary border border-primary fw-bold">${job.district}</span>
                <span class="company-rating-badge"><i class="fas fa-star text-warning"></i> ${job.matchScore || 90}% Match</span>
              </div>
              <h5 class="naukri-job-title" data-job-details="${job.id}" style="cursor:pointer;">${job.title}</h5>
              <div class="naukri-job-company">
                <i class="fas fa-building text-primary me-1"></i> ${job.department}
              </div>
              
              <div class="naukri-meta-row">
                <span class="naukri-meta-item"><i class="fas fa-briefcase"></i> 0-2 Yrs • ${job.type}</span>
                <span class="naukri-meta-item salary-item"><i class="fas fa-rupee-sign"></i> ${job.salary}</span>
                <span class="naukri-meta-item"><i class="fas fa-graduation-cap"></i> ${job.qualification}</span>
              </div>

              <p class="naukri-job-snippet">${job.description}</p>

              <div class="small text-danger fw-semibold mb-2"><i class="fas fa-calendar-times me-1"></i>Last Date: ${jd.lastDate} &nbsp;|&nbsp; <i class="fas fa-users me-1"></i>${jd.totalPosts} Posts &nbsp;|&nbsp; <span class="text-primary">Click for full details</span></div>
              
              <div class="naukri-skills-row">
                ${skillsBadges}
              </div>

              <div class="naukri-job-footer">
                <div class="naukri-post-time">
                  <i class="fas fa-check-circle text-success me-1"></i> State Verified • Active
                </div>
                <div class="d-flex gap-2">
                  <button class="naukri-apply-btn" data-job-details="${job.id}">
                    <i class="fas fa-eye me-1"></i> View Full Details
                  </button>
                </div>
              </div>
            </div>
          </div>
        `;
        filteredJobsGrid.innerHTML += cardHtml;
      });
    }

    // Delegated click fallback so ANY click on card/title/button opens details
    // (works even if inline onclick is blocked/cached).
    document.addEventListener("click", (e) => {
      const t = e.target.closest("[data-job-details]");
      if (t && typeof window.openJobDetailsModal === "function") {
        const id = parseInt(t.getAttribute("data-job-details"), 10);
        if (!isNaN(id)) window.openJobDetailsModal(id);
      }
    });

    filterDistrict?.addEventListener("change", renderJobs);
    filterSkill?.addEventListener("change", renderJobs);
    filterType?.addEventListener("change", renderJobs);
    filterSearch?.addEventListener("input", renderJobs);

    naukriSearchBtn?.addEventListener("click", () => {
      renderJobs();
      const target = document.getElementById("jobExplorer");
      if (target) target.scrollIntoView({ behavior: "smooth" });
    });

    // Trending Search Chips Handler
    document.querySelectorAll(".naukri-chip[data-search-chip]").forEach(chip => {
      chip.addEventListener("click", () => {
        const chipVal = chip.getAttribute("data-search-chip");
        if (filterSearch) filterSearch.value = chipVal;
        renderJobs();
        const target = document.getElementById("jobExplorer");
        if (target) target.scrollIntoView({ behavior: "smooth" });
      });
    });

    resetFiltersBtn?.addEventListener("click", () => {
      if (filterDistrict) filterDistrict.value = "ALL";
      if (filterSkill) filterSkill.value = "ALL";
      if (filterType) filterType.value = "ALL";
      if (filterSearch) filterSearch.value = "";
      const sidebarDist = document.getElementById("sidebarFilterDistrict");
      if (sidebarDist) sidebarDist.value = "ALL";
      const sidebarType = document.getElementById("sidebarFilterType");
      if (sidebarType) sidebarType.value = "ALL";
      renderJobs();
    });

    renderJobs();
  }

  // ==========================================================================
  // 11. INITIALIZATION ON DOM READY
  // ==========================================================================
  initAuthSystem();
  initJobFilterEngine();

  // Navigation & Switcher Listeners
  btnSwitchPublic?.addEventListener("click", () => switchPortalView("public"));
  btnSwitchStudent?.addEventListener("click", () => switchPortalView("student"));
  btnSwitchAdmin?.addEventListener("click", () => switchPortalView("admin"));

  navLinkPublic?.addEventListener("click", (e) => {
    if (!window.location.pathname.includes("index") && window.location.pathname !== "/" && !window.location.pathname.endsWith("/")) {
      window.location.href = "index.html";
    }
  });

  navLinkStudent?.addEventListener("click", (e) => {
    if (!window.location.pathname.includes("student-dashboard")) {
      e.preventDefault();
      if (appState.session && appState.session.role === "student") {
        window.location.href = "student-dashboard.html";
      } else {
        window.location.href = "student-login.html";
      }
    }
  });

  navLinkAdmin?.addEventListener("click", (e) => {
    e.preventDefault();
    if (appState.session && appState.session.role === "admin") {
      window.location.href = "admin-dashboard.html";
    } else {
      window.location.href = "admin-login.html";
    }
  });

  // Page Routing & Initialization
  const currentPath = window.location.pathname;

  if (currentPath.includes("student-dashboard")) {
    if (!appState.session || appState.session.role !== "student") {
      const defaultStudent = appState.students[0];
      appState.session = { role: "student", user: defaultStudent };
      setSession(appState.session);
    }
    updateSessionUI();
    renderStudentDashboard();
  } else if (currentPath.includes("admin-dashboard")) {
    if (!appState.session || appState.session.role !== "admin") {
      appState.session = { role: "admin", user: adminCredentials };
      setSession(appState.session);
    }
    updateSessionUI();
    renderAdminDashboard();
  } else if (currentPath.includes("student-login") || currentPath.includes("admin-login")) {
    updateSessionUI();
  } else if (currentPath.includes("job-details")) {
    updateSessionUI();
    renderJobDetailsPage();
  } else if (currentPath.includes("notification-preview")) {
    updateSessionUI();
    renderNotificationPreview();
  } else {
    // index.html / Public Portal
    updateSessionUI();
    if (typeof renderPublicJobs === "function") {
      renderPublicJobs();
    }

    const urlParams = new URLSearchParams(window.location.search);
    const requestedView = urlParams.get("view");
    if (requestedView === "admin") {
      window.location.href = "admin-login.html";
    } else if (requestedView === "student") {
      window.location.href = "student-login.html";
    }
  }
  
});
