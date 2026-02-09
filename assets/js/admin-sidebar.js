// Real-time clock
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
    const timeElement = document.getElementById('currentTime');
    if (timeElement) {
        timeElement.textContent = timeString;
    }
}
updateTime();
setInterval(updateTime, 1000);

// Toggle sidebar minimize/maximize
function toggleSidebarMinimize() {
    const sidebar = document.getElementById('adminSidebar');
    const toggleIcon = document.getElementById('toggleIcon');

    if (!sidebar || !toggleIcon) return;

    sidebar.classList.toggle('minimized');

    if (sidebar.classList.contains('minimized')) {
        toggleIcon.textContent = '▶';
        localStorage.setItem('sidebarMinimized', 'true');
    } else {
        toggleIcon.textContent = '◀';
        localStorage.setItem('sidebarMinimized', 'false');
    }
}

// Toggle sidebar for mobile
function toggleSidebar() {
    const sidebar = document.querySelector('.admin-sidebar');
    if (sidebar) {
        // On mobile, toggle the 'open' class
        if (window.innerWidth <= 1024) {
            sidebar.classList.toggle('open');
        } else {
            // On desktop, use the minimize function
            toggleSidebarMinimize();
        }
    }
}

// Tab switching function for pages with tabs
function switchTab(index) {
    const tabs = document.querySelectorAll('.tab-btn');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach((tab, i) => {
        if (i === index) {
            tab.classList.add('active');
            contents[i].classList.add('active');
        } else {
            tab.classList.remove('active');
            contents[i].classList.remove('active');
        }
    });
}

// Restore sidebar state from localStorage
window.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('adminSidebar');
    const toggleIcon = document.getElementById('toggleIcon');

    if (sidebar && toggleIcon) {
        const isMinimized = localStorage.getItem('sidebarMinimized') === 'true';

        if (isMinimized) {
            sidebar.classList.add('minimized');
            toggleIcon.textContent = '▶';
        }
    }

    // Initialize search functionality
    const searchInput = document.querySelector('.search-input');

    if (searchInput) {
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                const searchTerm = this.value.trim();
                if (searchTerm) {
                    performSearch(searchTerm);
                }
            }
        });
    }
});

// Search functionality
function performSearch(searchTerm) {
    const term = searchTerm.toLowerCase();

    const searchMap = {
        'student': 'Student.php',
        'students': 'Student.php',
        'instructor': 'Instructor.php',
        'instructors': 'Instructor.php',
        'teacher': 'Instructor.php',
        'teachers': 'Instructor.php',
        'course': 'Course.php',
        'courses': 'Course.php',
        'department': 'Department.php',
        'departments': 'Department.php',
        'college': 'Faculty.php',
        'faculty': 'Faculty.php',
        'exam': 'ECommittee.php',
        'committee': 'ECommittee.php',
        'exam committee': 'ECommittee.php',
        'dashboard': 'index.php',
        'home': 'index.php',
        'profile': 'Profile.php',
        'settings': 'SystemSettings.php',
        'system settings': 'SystemSettings.php'
    };

    if (searchMap[term]) {
        window.location.href = searchMap[term];
    } else {
        for (let key in searchMap) {
            if (key.includes(term) || term.includes(key)) {
                window.location.href = searchMap[key];
                return;
            }
        }

        alert('No results found for "' + searchTerm + '". Try: students, instructors, courses, departments, etc.');
    }
}

// Close sidebar when clicking outside on mobile
document.addEventListener('click', function (event) {
    const sidebar = document.querySelector('.admin-sidebar');
    const menuBtn = document.querySelector('.mobile-menu-btn');

    if (!sidebar || !menuBtn) return;

    if (window.innerWidth <= 1024) {
        if (!sidebar.contains(event.target) && !menuBtn.contains(event.target)) {
            sidebar.classList.remove('open');
        }
    }
});

// Toggle profile dropdown
function toggleProfileDropdown(event) {
    if (event) {
        event.stopPropagation();
    }
    const profile = event ? event.currentTarget : document.querySelector('.header-profile');
    if (profile) {
        profile.classList.toggle('active');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function (event) {
    const profile = document.querySelector('.header-profile');
    if (profile && !profile.contains(event.target)) {
        profile.classList.remove('active');
    }
});


// ============================================
// GLOBAL SEARCH FUNCTIONALITY
// ============================================

(function () {
    const searchInput = document.getElementById('globalSearchInput');
    const searchResults = document.getElementById('searchResults');

    if (!searchInput || !searchResults) return;

    let searchTimeout;
    let currentRole = document.body.classList.contains('admin-layout') ?
        (window.location.pathname.includes('/Admin/') ? 'Admin' :
            window.location.pathname.includes('/Instructor/') ? 'Instructor' : 'DepartmentHead') : 'Admin';

    // Debounced search function
    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            searchResults.style.display = 'none';
            searchResults.innerHTML = '';
            return;
        }

        searchTimeout = setTimeout(() => {
            performSearch(query);
        }, 300);
    });

    // Perform search
    function performSearch(query) {
        const searchUrl = `GlobalSearch.php?q=${encodeURIComponent(query)}`;

        fetch(searchUrl)
            .then(response => response.json())
            .then(data => {
                displayResults(data);
            })
            .catch(error => {
                console.error('Search error:', error);
                searchResults.innerHTML = '<div class="search-error">Search failed. Please try again.</div>';
                searchResults.style.display = 'block';
            });
    }

    // Display search results
    function displayResults(results) {
        if (results.length === 0) {
            searchResults.innerHTML = '<div class="search-no-results">No results found</div>';
            searchResults.style.display = 'block';
            return;
        }

        let html = '';
        results.forEach(result => {
            html += formatResult(result);
        });

        searchResults.innerHTML = html;
        searchResults.style.display = 'block';
    }

    // Format individual result
    function formatResult(result) {
        const icons = {
            student: '👨‍🎓',
            instructor: '👨‍🏫',
            course: '📚',
            exam: '📝',
            question: '❓',
            department: '🏢'
        };

        const icon = icons[result.type] || '📄';
        let title = '';
        let subtitle = '';
        let link = '#';

        switch (result.type) {
            case 'student':
                title = result.full_name;
                subtitle = result.student_code + ' • ' + result.email;
                link = currentRole === 'Admin' ? `Student.php` : `#`;
                break;
            case 'instructor':
                title = result.full_name;
                subtitle = result.instructor_code + ' • ' + result.email;
                link = currentRole === 'Admin' ? `Instructor.php` : `#`;
                break;
            case 'course':
                title = result.course_name;
                subtitle = result.course_code;
                link = currentRole === 'Admin' ? `Course.php` : `#`;
                break;
            case 'exam':
                title = result.exam_name;
                subtitle = result.course_code;
                link = currentRole === 'Instructor' ? `ViewExam.php?id=${result.exam_id}` :
                    currentRole === 'DepartmentHead' ? `ViewExamDetails.php?id=${result.exam_id}` : `#`;
                break;
            case 'question':
                title = result.question_text;
                subtitle = result.course_code;
                link = currentRole === 'Instructor' ? `ManageQuestions.php` : `#`;
                break;
            case 'department':
                title = result.department_name;
                subtitle = result.department_code;
                link = currentRole === 'Admin' ? `Department.php` : `#`;
                break;
        }

        return `
            <a href="${link}" class="search-result-item" data-type="${result.type}">
                <span class="result-icon">${icon}</span>
                <div class="result-content">
                    <div class="result-title">${escapeHtml(title)}</div>
                    <div class="result-subtitle">${escapeHtml(subtitle)}</div>
                </div>
                <span class="result-type">${result.type}</span>
            </a>
        `;
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Close search results when clicking outside
    document.addEventListener('click', function (e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    // Show results when input is focused and has value
    searchInput.addEventListener('focus', function () {
        if (this.value.trim().length >= 2 && searchResults.innerHTML) {
            searchResults.style.display = 'block';
        }
    });
})();
