/**
 * In-Exam Issue Reporter Widget
 * Include this script in your exam interface to enable quick issue reporting
 * 
 * Usage:
 * 1. Include this script in your exam page
 * 2. Add the HTML button: <button onclick="openIssueReporter()">Report Issue</button>
 * 3. Set the exam_id variable: var currentExamId = <?php echo $exam_id; ?>;
 */

var currentExamId = null; // Set this in your exam page

function openIssueReporter() {
    const modal = document.createElement('div');
    modal.id = 'issueReporterModal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.7);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
    `;

    modal.innerHTML = `
        <div style="background: white; padding: 30px; border-radius: 10px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto;">
            <h3 style="color: #667eea; margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle"></i> Report Technical Issue
            </h3>
            
            <div id="issueReportMessage"></div>
            
            <form id="quickIssueForm">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Issue Type *</label>
                    <select id="quick_issue_type" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="">-- Select Issue Type --</option>
                        <option value="connectivity">❌ Connectivity Problems</option>
                        <option value="system_error">❌ System Error/Freezing</option>
                        <option value="submission_failure">❌ Cannot Submit Answers</option>
                        <option value="display_issue">❌ Questions Not Showing</option>
                        <option value="timer_error">❌ Timer Problem</option>
                        <option value="other">🔧 Other Issue</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold;">Describe the Issue *</label>
                    <textarea id="quick_issue_description" required rows="4" 
                              style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;"
                              placeholder="Please describe what happened..."></textarea>
                </div>
                
                <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 0.85em;">
                    <strong>System Info (Auto-captured):</strong><br>
                    Browser: <span id="quick_browser_display">Detecting...</span><br>
                    OS: <span id="quick_os_display">Detecting...</span><br>
                    Screen: <span id="quick_screen_display">Detecting...</span>
                </div>
                
                <input type="hidden" id="quick_browser_info">
                <input type="hidden" id="quick_os_info">
                <input type="hidden" id="quick_screen_resolution">
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" style="flex: 1; padding: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">
                        Submit Report
                    </button>
                    <button type="button" onclick="closeIssueReporter()" style="flex: 1; padding: 10px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        Cancel
                    </button>
                </div>
            </form>
            
            <div style="margin-top: 15px; padding: 10px; background: #fff3cd; border-radius: 5px; font-size: 0.85em;">
                <strong>Note:</strong> Your exam timer will continue running. Our team will be notified immediately.
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    // Capture system info
    captureQuickSystemInfo();

    // Handle form submission
    document.getElementById('quickIssueForm').addEventListener('submit', function (e) {
        e.preventDefault();
        submitQuickIssue();
    });
}

function closeIssueReporter() {
    const modal = document.getElementById('issueReporterModal');
    if (modal) {
        modal.remove();
    }
}

function captureQuickSystemInfo() {
    document.getElementById('quick_browser_info').value = navigator.userAgent;
    document.getElementById('quick_os_info').value = navigator.platform;
    document.getElementById('quick_screen_resolution').value = screen.width + 'x' + screen.height;

    document.getElementById('quick_browser_display').textContent = navigator.userAgent.substring(0, 50) + '...';
    document.getElementById('quick_os_display').textContent = navigator.platform;
    document.getElementById('quick_screen_display').textContent = screen.width + 'x' + screen.height;
}

function submitQuickIssue() {
    const formData = new FormData();
    formData.append('exam_id', currentExamId);
    formData.append('issue_type', document.getElementById('quick_issue_type').value);
    formData.append('issue_description', document.getElementById('quick_issue_description').value);
    formData.append('browser_info', document.getElementById('quick_browser_info').value);
    formData.append('os_info', document.getElementById('quick_os_info').value);
    formData.append('screen_resolution', document.getElementById('quick_screen_resolution').value);

    fetch('QuickReportIssue.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            const messageDiv = document.getElementById('issueReportMessage');
            if (data.success) {
                messageDiv.innerHTML = `
                <div style="padding: 15px; background: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 15px;">
                    <strong>✓ Success!</strong> ${data.message}
                </div>
            `;
                document.getElementById('quickIssueForm').style.display = 'none';
                setTimeout(closeIssueReporter, 3000);
            } else {
                messageDiv.innerHTML = `
                <div style="padding: 15px; background: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 15px;">
                    <strong>✗ Error:</strong> ${data.message}
                </div>
            `;
            }
        })
        .catch(error => {
            document.getElementById('issueReportMessage').innerHTML = `
            <div style="padding: 15px; background: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 15px;">
                <strong>✗ Error:</strong> Network error. Please try again.
            </div>
        `;
        });
}

// Add floating report button to exam page
function addFloatingReportButton() {
    const button = document.createElement('button');
    button.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Report Issue';
    button.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 12px 20px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
        z-index: 9999;
        transition: all 0.3s;
    `;
    button.onmouseover = function () {
        this.style.transform = 'scale(1.05)';
        this.style.boxShadow = '0 6px 20px rgba(220, 53, 69, 0.6)';
    };
    button.onmouseout = function () {
        this.style.transform = 'scale(1)';
        this.style.boxShadow = '0 4px 15px rgba(220, 53, 69, 0.4)';
    };
    button.onclick = openIssueReporter;
    document.body.appendChild(button);
}

// Auto-add button when page loads (optional - comment out if you want manual button placement)
// window.addEventListener('load', addFloatingReportButton);
