// Global Search Functionality
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
