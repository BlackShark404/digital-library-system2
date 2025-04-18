<?php
include_once $footerPath;

// --- Configuration ---
$pdfFileName = 'To_Kill_A_Mockingbird.pdf'; // Make sure this file is accessible via URL
$pdfFilePath = __DIR__ . '/' . $pdfFileName; // Path on server
$pdfFileUrl = $pdfFileName; // URL accessible by the browser relative to the HTML page
$page_title = "Reading Session - PDF Viewer";

// --- Placeholder for User Data ---
// In a real app, fetch this from DB based on user and PDF identifier
$user_last_page_read = isset($_SESSION['last_page_' . md5($pdfFilePath)]) ? $_SESSION['last_page_' . md5($pdfFilePath)] : 1;
// Fetch PDF metadata (Title/Author) - Can still use pdfparser *once* if needed, or let JS handle it
$pdfTitle = 'PDF Document'; // Default
$pdfAuthor = 'Unknown Author'; // Default
$totalPages = 0; // We will get this from PDF.js

// Try to get basic details quickly without full parsing (optional)
if (file_exists($pdfFilePath) && class_exists('\Smalot\PdfParser\Parser')) {
    try {
        // You could keep pdfparser just for metadata if desired
        require_once 'vendor/autoload.php';
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($pdfFilePath);
        $details = $pdf->getDetails();
        if (!empty($details['Title'])) $pdfTitle = $details['Title'];
        if (!empty($details['Author'])) $pdfAuthor = $details['Author'];
        // Getting total pages here is possible but PDF.js will do it anyway
        // $totalPages = count($pdf->getPages());
    } catch (Exception $e) {
        error_log("Metadata Parsing Error: " . $e->getMessage());
        // Don't stop the page, just use defaults
    }
} else if (!file_exists($pdfFilePath)) {
    $error_message = "Error: PDF file not found at " . htmlspecialchars($pdfFilePath);
    // Set defaults to prevent errors later
    $book = ['id' => 0, 'title' => 'Error Loading PDF', 'author' => '', 'cover' => '', 'total_pages' => 0, 'last_page_read' => 1];
    $in_library = false;
    $reading_stats = ['time_spent' => '0h 0m', 'completion' => '0%', 'last_session' => date('Y-m-d H:i:s')];
}


// --- AJAX Request Handler FOR SAVING PROGRESS ---
if (isset($_POST['save_progress']) && isset($_POST['current_page']) && isset($_POST['pdf_file'])) {
    // Basic validation
    $currentPageSaved = filter_input(INPUT_POST, 'current_page', FILTER_VALIDATE_INT);
    $timeSpentSaved = filter_input(INPUT_POST, 'time_spent_display', FILTER_SANITIZE_STRING);
    $pdfFileSaved = filter_input(INPUT_POST, 'pdf_file', FILTER_SANITIZE_STRING); // Get the PDF filename

    if ($currentPageSaved !== false && $pdfFileSaved) {
        $sessionKey = 'last_page_' . md5(__DIR__ . '/' . $pdfFileSaved); // Recreate key based on posted filename
        $_SESSION[$sessionKey] = $currentPageSaved;
        // In a real app: Save $currentPageSaved, $timeSpentSaved for the user associated with $pdfFileSaved in the database.
        error_log("Progress Save Requested for {$pdfFileSaved}: Page {$currentPageSaved}, Time: {$timeSpentSaved}"); // Log for testing

        // Send a success response back to AJAX instead of redirecting
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Progress saved.']);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid data received for saving progress.']);
        exit;
    }
}

// --- Main Page Logic ---
include_once 'includes/header.php'; // Your standard header

// Define $book structure for the template (using defaults or fetched metadata)
// $totalPages will be updated by JavaScript
if (!isset($book)) { // If not set by error condition above
    $book = [
        'id' => 0, // Use 0 or a hash of the filename if needed
        'title' => $pdfTitle,
        'author' => $pdfAuthor,
        'cover' => 'path/to/default/pdf_icon.png', // Use a generic PDF icon
        'total_pages' => $totalPages, // Will be updated by JS
        'last_page_read' => $user_last_page_read,
        'filepath' => $pdfFilePath // Keep server path if needed elsewhere
    ];
}

// Placeholders - Keep these as they drive UI elements
if (!isset($in_library)) $in_library = true; // Assume viewing means it's "in library" for this context
if (!isset($reading_stats)) $reading_stats = [ // These would ideally be updated based on real tracking
    'time_spent' => '0h 0m',
    'completion' => ($book['total_pages'] > 0) ? round(($book['last_page_read'] / $book['total_pages']) * 100) . '%' : '0%',
    'last_session' => date('Y-m-d H:i:s') // Placeholder: Current time
];

?>

<div class="container-fluid py-4">
    <!-- Display Error Message if PDF file not found -->
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
    <?php else: ?>
        <!-- Success message area (will be populated by JavaScript on save) -->
        <div id="saveStatus" class="alert alert-success alert-dismissible fade show d-none" role="alert">
            Your reading progress has been saved.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>

        <div class="row">
            <!-- Book Information Sidebar (Largely unchanged, but Total Pages might show 'Loading...') -->
            <div class="col-md-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($book['title']); ?></h5>
                        <p class="text-muted">by <?php echo htmlspecialchars($book['author']); ?></p>
                        <p class="text-muted small">Source: <?php echo htmlspecialchars($pdfFileName); ?></p>
                        <hr>
                        <h6 class="mb-3">Reading Progress</h6>
                        <!-- Progress bar and text - will be updated by JS -->
                        <div class="progress mb-3" style="height: 10px;">
                            <div id="progressBar" class="progress-bar bg-success" role="progressbar"
                                style="width: 0%;" aria-valuenow="1" aria-valuemin="1" aria-valuemax="1">
                            </div>
                        </div>
                        <p class="small text-muted">
                            Page <span id="progressCurrentPage"><?php echo $book['last_page_read']; ?></span> of <span id="progressTotalPages">Loading...</span>
                            (<span id="progressPercentage">0</span>% complete)
                        </p>
                        <hr>
                        <!-- Reading Stats (Unchanged logic) -->
                        <h6 class="mb-3">Reading Stats (Placeholder)</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-clock me-2"></i> Time spent: <span id="statsTimeSpent"><?php echo $reading_stats['time_spent']; ?></span></li>
                            <li><i class="bi bi-graph-up me-2"></i> Completion: <span id="statsCompletion"><?php echo $reading_stats['completion']; ?></span></li>
                            <li><i class="bi bi-calendar-check me-2"></i> Last session: <?php echo date('M d, Y', strtotime($reading_stats['last_session'])); ?></li>
                        </ul>
                        <hr>
                        <!-- Actions (Unchanged) -->
                        <div class="d-grid gap-2">
                            <a href="<?php echo htmlspecialchars($pdfFileUrl); ?>" download class="btn btn-sm btn-outline-success">
                                <i class="bi bi-download"></i> Download PDF
                            </a>
                            <?php if ($in_library): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-x-circle"></i> Remove from Library (Placeholder)
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-plus-circle"></i> Add to Library (Placeholder)
                                </button>
                            <?php endif; ?>
                            <button type="button" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-heart"></i> Add to Wishlist (Placeholder)
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-info">
                                <i class="bi bi-share"></i> Share (Placeholder)
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print();">
                                <i class="bi bi-printer"></i> Print View
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reading Area -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <!-- Font size buttons might become Zoom buttons -->
                            <button id="zoomOutBtn" class="btn btn-sm btn-outline-secondary" title="Zoom Out">
                                <i class="bi bi-zoom-out"></i>
                            </button>
                            <button id="zoomInBtn" class="btn btn-sm btn-outline-secondary" title="Zoom In">
                                <i class="bi bi-zoom-in"></i>
                            </button>
                            <div class="btn-group ms-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary active" title="Light mode" id="lightModeBtn">
                                    <i class="bi bi-sun"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" title="Dark mode" id="darkModeBtn">
                                    <i class="bi bi-moon"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <span class="badge bg-primary">Page <span id="currentPage"><?php echo $book['last_page_read']; ?></span> of <span id="totalPagesDisplay">Loading...</span></span>
                            <button id="prevPageBtn" class="btn btn-sm btn-outline-secondary ms-2" disabled>
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button id="nextPageBtn" class="btn btn-sm btn-outline-secondary" disabled>
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <!-- PDF.js Rendering Area -->
                    <div class="card-body" id="pdf-viewer-container" style="max-height: 75vh; overflow: auto; background-color: #eee; text-align: center;">
                        <div id="loadingWrapper" class="text-center p-5">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading PDF...</span>
                            </div>
                            <p class="mt-2">Loading PDF...</p>
                        </div>
                        <canvas id="pdf-canvas"></canvas>
                        <!-- PDF.js Text Layer (Optional but good for selection) -->
                        <div id="text-layer" class="textLayer"></div>
                    </div>
                    <div class="card-footer bg-light">
                        <!-- Use a button to trigger save via JS, not a form submit -->
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <button type="button" class="btn btn-outline-secondary" id="bookmarkBtn">
                                    <i class="bi bi-bookmark"></i> Bookmark Page
                                </button>
                                <button type="button" class="btn btn-outline-secondary ms-2" id="addNoteBtn">
                                    <i class="bi bi-pencil"></i> Add Note on Page
                                </button>
                            </div>
                            <div>
                                <span class="text-muted me-3">Time reading: <span id="timeSpentDisplay">00:00:00</span></span>
                                <!-- Hidden fields needed for JS save function -->
                                <input type="hidden" id="pdfFileInput" value="<?php echo htmlspecialchars($pdfFileName); ?>">
                                <input type="hidden" id="currentPageInput" value="<?php echo $book['last_page_read']; ?>">
                                <input type="hidden" id="timeSpentInputHidden" value="00:00:00">
                                <button type="button" id="saveProgressBtn" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Save Progress
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes Section (Unchanged HTML structure) -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-pencil-square"></i> Notes and Highlights
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-4" id="emptyNotesState">
                            <i class="bi bi-journal-text" style="font-size: 3rem; color: #ccc;"></i>
                            <p class="mt-3">Notes for the current page will appear here.</p>
                            <p class="small text-muted">(Note loading/saving is a placeholder)</p>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="createFirstNoteBtn">
                                <i class="bi bi-plus-circle"></i> Add Note for this Page
                            </button>
                        </div>
                        <div id="notesContainer" class="d-none"></div>
                    </div>
                </div>

            </div> <!-- /col-md-9 -->
        </div> <!-- /row -->
    <?php endif; // End error message check 
    ?>
</div> <!-- /container-fluid -->

<!-- Note Modal (Unchanged HTML structure) -->
<div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
    <!-- ... modal content as before ... -->
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="noteModalLabel">Add Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="noteForm">
                    <div class="mb-3">
                        <label for="highlightedText" class="form-label">Highlighted Text (Optional)</label>
                        <textarea class="form-control" id="highlightedText" rows="3" readonly style="background-color:#f0f0f0;"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="noteText" class="form-label">Your Note</label>
                        <textarea class="form-control" id="noteText" rows="4" required></textarea>
                    </div>
                    <input type="hidden" id="notePage" value="<?php echo $book['last_page_read']; ?>"> <!-- Set initial page -->
                    <p class="small text-muted">Note will be associated with Page <span id="notePageDisplay"><?php echo $book['last_page_read']; ?></span>.</p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveNoteBtn">Save Note (Placeholder)</button>
            </div>
        </div>
    </div>
</div>

<!-- Include PDF.js library -->
<script src="pdfjs/build/pdf.js"></script>
<!-- Optional: PDF.js CSS for text layer -->
<link rel="stylesheet" href="pdfjs/web/viewer.css">
<script src="/node_modules/pdfjs-dist/build/pdf.mjs"></script>
<script src="/node_modules/pdfjs-dist/build/pdf.worker.mjs"></script>

<style>
    /* Style for the container */
    #pdf-viewer-container {
        position: relative;
        /* Needed for absolute positioning of text layer */
    }

    /* Style for the text layer to overlay the canvas */
    #text-layer {
        position: absolute;
        left: 0;
        top: 0;
        right: 0;
        bottom: 0;
        overflow: hidden;
        opacity: 0.2;
        /* Make text layer semi-transparent */
        line-height: 1.0;
        /* PDF.js viewer.css handles text positioning */
    }

    /* Ensure canvas is centered if container is wider */
    #pdf-canvas {
        display: block;
        margin: 0 auto;
        border: 1px solid #ccc;
        /* Optional: visual border */
    }

    /* Dark mode styling */
    body.dark-mode #pdf-viewer-container {
        background-color: #333;
    }

    body.dark-mode .card {
        background-color: #222;
        color: #eee;
    }

    body.dark-mode .card .card-header,
    body.dark-mode .card .card-footer {
        background-color: #333;
        border-color: #444;
    }

    body.dark-mode .text-muted {
        color: #aaa !important;
    }

    /* Add other dark mode styles as needed */
</style>




<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- PDF.js Configuration ---
        const pdfUrl = '<?php echo htmlspecialchars($pdfFileUrl, ENT_QUOTES, 'UTF-8'); ?>';
        const initialPage = <?php echo $book['last_page_read']; ?>;
        const pdfWorkerSrc = 'pdfjs/build/pdf.worker.js'; // Make sure this path is correct
        pdfjsLib.GlobalWorkerOptions.workerSrc = pdfWorkerSrc;

        // --- State Variables ---
        let pdfDoc = null;
        let currentPage = initialPage;
        let totalPages = 0;
        let currentScale = 1.5; // Initial zoom level
        let timeSpent = 0; // Seconds
        let timerInterval;

        // --- DOM Elements ---
        const viewerContainer = document.getElementById('pdf-viewer-container');
        const canvas = document.getElementById('pdf-canvas');
        const ctx = canvas.getContext('2d');
        const textLayerDiv = document.getElementById('text-layer');
        const loadingWrapper = document.getElementById('loadingWrapper');
        const currentPageDisplay = document.getElementById('currentPage');
        const totalPagesDisplay = document.getElementById('totalPagesDisplay');
        const progressTotalPages = document.getElementById('progressTotalPages');
        const currentPageInput = document.getElementById('currentPageInput');
        const prevPageBtn = document.getElementById('prevPageBtn');
        const nextPageBtn = document.getElementById('nextPageBtn');
        const zoomInBtn = document.getElementById('zoomInBtn');
        const zoomOutBtn = document.getElementById('zoomOutBtn');
        const timeSpentDisplay = document.getElementById('timeSpentDisplay');
        const timeSpentInputHidden = document.getElementById('timeSpentInputHidden');
        const lightModeBtn = document.getElementById('lightModeBtn');
        const darkModeBtn = document.getElementById('darkModeBtn');
        const bookmarkBtn = document.getElementById('bookmarkBtn');
        const addNoteBtn = document.getElementById('addNoteBtn');
        const createFirstNoteBtn = document.getElementById('createFirstNoteBtn');
        const noteModalElement = document.getElementById('noteModal');
        const noteModal = noteModalElement ? new bootstrap.Modal(noteModalElement) : null;
        const saveNoteBtn = document.getElementById('saveNoteBtn');
        const highlightedTextarea = document.getElementById('highlightedText');
        const noteTextarea = document.getElementById('noteText');
        const notePageInput = document.getElementById('notePage');
        const notePageDisplay = document.getElementById('notePageDisplay');
        const progressBar = document.getElementById('progressBar');
        const progressCurrentPage = document.getElementById('progressCurrentPage');
        const progressPercentage = document.getElementById('progressPercentage');
        const statsTimeSpent = document.getElementById('statsTimeSpent');
        const statsCompletion = document.getElementById('statsCompletion');
        const saveProgressBtn = document.getElementById('saveProgressBtn');
        const saveStatusDiv = document.getElementById('saveStatus');
        const pdfFileInput = document.getElementById('pdfFileInput');


        // --- Core PDF.js Function ---
        function renderPage(pageNum) {
            if (!pdfDoc) return; // Make sure PDF is loaded

            pageNum = parseInt(pageNum); // Ensure it's a number
            if (pageNum < 1 || pageNum > totalPages) {
                console.error("Invalid page number requested:", pageNum);
                return;
            }

            // Set page rendering state
            currentPage = pageNum;
            updatePageDisplay(); // Update UI elements immediately

            // Disable nav during render
            prevPageBtn.disabled = true;
            nextPageBtn.disabled = true;

            // Show loading indicator for page change (optional)
            // loadingWrapper.style.display = 'block';
            canvas.style.opacity = '0.5';
            if (textLayerDiv) textLayerDiv.innerHTML = ''; // Clear previous text layer


            pdfDoc.getPage(pageNum).then(function(page) {
                console.log('Page loaded');

                const viewport = page.getViewport({
                    scale: currentScale
                });

                // Prepare canvas using PDF page dimensions
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                // Prepare text layer div
                if (textLayerDiv) {
                    textLayerDiv.style.width = canvas.width + 'px';
                    textLayerDiv.style.height = canvas.height + 'px';
                }

                // Render PDF page into canvas context
                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                const renderTask = page.render(renderContext);

                // Render Text Layer (Optional but good for accessibility/selection)
                let textLayerRenderTask = null;
                if (textLayerDiv) {
                    textLayerRenderTask = page.getTextContent().then(function(textContent) {
                        // Pass the text layer HTML element, text content and viewport to the helper function
                        pdfjsLib.renderTextLayer({
                            textContentSource: textContent,
                            container: textLayerDiv,
                            viewport: viewport,
                            textDivs: [] // Required parameter
                        });
                    });
                }


                // Wait for rendering to finish
                Promise.all([renderTask.promise, textLayerRenderTask]).then(function() {
                    console.log('Page rendered');
                    canvas.style.opacity = '1';
                    // loadingWrapper.style.display = 'none';
                    updateNavButtons(); // Re-enable nav buttons based on new page
                }).catch(err => {
                    console.error("Error rendering page:", err);
                    canvas.style.opacity = '1'; // Ensure canvas visible even on error
                    updateNavButtons();
                });
            });
        }

        // --- UI Update Functions ---
        function updatePageDisplay() {
            if (currentPageDisplay) currentPageDisplay.textContent = currentPage;
            if (currentPageInput) currentPageInput.value = currentPage;
            if (notePageInput) notePageInput.value = currentPage; // Update hidden input for note modal
            if (notePageDisplay) notePageDisplay.textContent = currentPage; // Update visible page in note modal

            // Update progress bar
            if (progressBar && totalPages > 0) {
                const percentage = Math.round((currentPage / totalPages) * 100);
                progressBar.style.width = percentage + '%';
                progressBar.setAttribute('aria-valuenow', currentPage);
                if (progressCurrentPage) progressCurrentPage.textContent = currentPage;
                if (progressPercentage) progressPercentage.textContent = percentage;
                if (statsCompletion) statsCompletion.textContent = percentage + '%';
            }
        }

        function updateNavButtons() {
            if (prevPageBtn) prevPageBtn.disabled = (currentPage <= 1);
            if (nextPageBtn) nextPageBtn.disabled = (currentPage >= totalPages);
        }

        function updateZoomButtons() {
            if (zoomOutBtn) zoomOutBtn.disabled = (currentScale <= 0.5); // Set min zoom
            if (zoomInBtn) zoomInBtn.disabled = (currentScale >= 3.0); // Set max zoom
        }

        // --- Timer Functions (Unchanged) ---
        function startTimer() {
            clearInterval(timerInterval);
            timerInterval = setInterval(() => {
                timeSpent++;
                updateTimerDisplay();
            }, 1000);
        }

        function updateTimerDisplay() {
            const hours = Math.floor(timeSpent / 3600);
            const minutes = Math.floor((timeSpent % 3600) / 60);
            const seconds = timeSpent % 60;
            const formattedTime =
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');

            if (timeSpentDisplay) timeSpentDisplay.textContent = formattedTime;
            if (timeSpentInputHidden) timeSpentInputHidden.value = formattedTime;

            const hoursShort = Math.floor(timeSpent / 3600);
            const minutesShort = Math.floor((timeSpent % 3600) / 60);
            if (statsTimeSpent) statsTimeSpent.textContent = `${hoursShort}h ${minutesShort}m`;
        }

        // --- Save Progress Function ---
        function saveProgress() {
            const pageToSave = currentPageInput.value;
            const timeToSave = timeSpentInputHidden.value;
            const fileToSave = pdfFileInput.value;

            const formData = new FormData();
            formData.append('save_progress', '1');
            formData.append('current_page', pageToSave);
            formData.append('time_spent_display', timeToSave);
            formData.append('pdf_file', fileToSave);

            // Disable button during save
            saveProgressBtn.disabled = true;
            saveProgressBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';


            fetch('<?php echo basename(__FILE__); ?>', { // Post back to the same PHP file
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Save response:', data);
                    if (data.success) {
                        saveStatusDiv.textContent = data.message || 'Progress saved successfully.';
                        saveStatusDiv.classList.remove('d-none', 'alert-danger');
                        saveStatusDiv.classList.add('alert-success', 'show');
                    } else {
                        saveStatusDiv.textContent = data.message || 'Failed to save progress.';
                        saveStatusDiv.classList.remove('d-none', 'alert-success');
                        saveStatusDiv.classList.add('alert-danger', 'show');
                    }
                    // Auto-hide the status message after a few seconds
                    setTimeout(() => {
                        saveStatusDiv.classList.remove('show');
                        // Use Bootstrap's method to ensure fade animation completes before hiding
                        var bsAlert = bootstrap.Alert.getOrCreateInstance(saveStatusDiv);
                        if (bsAlert) {
                            bsAlert.close(); // This triggers the fade out
                        }
                        // Ensure it's hidden after animation (Bootstrap might not add d-none automatically on close)
                        setTimeout(() => saveStatusDiv.classList.add('d-none'), 200);
                    }, 4000);
                })
                .catch(error => {
                    console.error('Error saving progress:', error);
                    saveStatusDiv.textContent = 'An error occurred while saving.';
                    saveStatusDiv.classList.remove('d-none', 'alert-success');
                    saveStatusDiv.classList.add('alert-danger', 'show');
                })
                .finally(() => {
                    // Re-enable button and restore text
                    saveProgressBtn.disabled = false;
                    saveProgressBtn.innerHTML = '<i class="bi bi-save"></i> Save Progress';
                });
        }


        // --- Event Listeners ---

        // Page Navigation
        if (prevPageBtn) {
            prevPageBtn.addEventListener('click', () => {
                if (currentPage > 1) {
                    renderPage(currentPage - 1);
                }
            });
        }
        if (nextPageBtn) {
            nextPageBtn.addEventListener('click', () => {
                if (currentPage < totalPages) {
                    renderPage(currentPage + 1);
                }
            });
        }

        // Zoom Controls
        if (zoomInBtn) {
            zoomInBtn.addEventListener('click', () => {
                if (currentScale < 3.0) {
                    currentScale += 0.2;
                    renderPage(currentPage); // Re-render with new scale
                    updateZoomButtons();
                }
            });
        }
        if (zoomOutBtn) {
            zoomOutBtn.addEventListener('click', () => {
                if (currentScale > 0.5) {
                    currentScale -= 0.2;
                    renderPage(currentPage); // Re-render with new scale
                    updateZoomButtons();
                }
            });
        }

        // Reading Mode Controls
        if (lightModeBtn && darkModeBtn) {
            lightModeBtn.addEventListener('click', function() {
                document.body.classList.remove('dark-mode');
                lightModeBtn.classList.add('active');
                darkModeBtn.classList.remove('active');
            });
            darkModeBtn.addEventListener('click', function() {
                document.body.classList.add('dark-mode');
                darkModeBtn.classList.add('active');
                lightModeBtn.classList.remove('active');
            });
        }

        // Save Progress Button
        if (saveProgressBtn) {
            saveProgressBtn.addEventListener('click', saveProgress);
        }

        // Bookmark (Placeholder)
        if (bookmarkBtn) {
            bookmarkBtn.addEventListener('click', function() {
                alert(`Placeholder: Bookmark added for Page ${currentPage} of ${pdfFileInput.value}`);
                // Add AJAX call here to save bookmark
            });
        }

        // Notes Functionality
        function openNoteModal() {
            if (!noteModal) return;
            const selection = window.getSelection();
            const currentHighlightedText = selection.toString().trim();

            // Try to get text only from the text layer if possible
            let textFromLayer = '';
            if (textLayerDiv && textLayerDiv.contains(selection.anchorNode)) {
                textFromLayer = currentHighlightedText;
            } else {
                // If selection is outside text layer, maybe clear it or show message
                textFromLayer = ''; // Don't grab random text from UI
            }


            if (highlightedTextarea) highlightedTextarea.value = textFromLayer;
            if (noteTextarea) noteTextarea.value = '';
            if (notePageInput) notePageInput.value = currentPage;
            if (notePageDisplay) notePageDisplay.textContent = currentPage;

            noteModal.show();
        }

        if (addNoteBtn) addNoteBtn.addEventListener('click', openNoteModal);
        if (createFirstNoteBtn) createFirstNoteBtn.addEventListener('click', openNoteModal);
        if (saveNoteBtn) {
            saveNoteBtn.addEventListener('click', function() {
                const noteData = {
                    pdf: pdfFileInput.value,
                    page: notePageInput ? notePageInput.value : currentPage,
                    highlight: highlightedTextarea ? highlightedTextarea.value : '',
                    text: noteTextarea ? noteTextarea.value : ''
                };
                if (!noteData.text) {
                    alert("Please enter your note.");
                    if (noteTextarea) noteTextarea.focus();
                    return;
                }
                console.log("Saving Note (Placeholder):", noteData);
                alert(`Placeholder: Note saved for Page ${noteData.page}`);
                if (noteModal) noteModal.hide();
                // Add AJAX call here to save note
            });
        }


        // --- Initial Load ---
        loadingWrapper.style.display = 'block';
        canvas.style.opacity = '0';

        pdfjsLib.getDocument(pdfUrl).promise.then(function(pdfDoc_) {
            console.log('PDF loaded');
            pdfDoc = pdfDoc_;
            totalPages = pdfDoc.numPages;

            // Update total pages display everywhere
            if (totalPagesDisplay) totalPagesDisplay.textContent = totalPages;
            if (progressTotalPages) progressTotalPages.textContent = totalPages;
            if (progressBar) progressBar.setAttribute('aria-valuemax', totalPages);

            loadingWrapper.style.display = 'none';
            canvas.style.opacity = '1';

            // Validate initial page number
            if (currentPage < 1 || currentPage > totalPages) {
                currentPage = 1;
            }

            // Render the first page
            renderPage(currentPage);
            updateZoomButtons(); // Set initial state for zoom

            // Start timer if document has pages
            if (totalPages > 0) {
                startTimer();
            } else {
                // Handle empty PDF case
                viewerContainer.innerHTML = '<p class="text-danger text-center p-5">This PDF document appears to be empty or corrupted.</p>';
                // Disable all controls
                prevPageBtn.disabled = true;
                nextPageBtn.disabled = true;
                zoomInBtn.disabled = true;
                zoomOutBtn.disabled = true;
                bookmarkBtn.disabled = true;
                addNoteBtn.disabled = true;
                saveProgressBtn.disabled = true;
            }

        }).catch(function(reason) {
            // PDF loading error
            console.error('Error loading PDF: ' + reason);
            loadingWrapper.style.display = 'none';
            viewerContainer.innerHTML = `<p class="text-danger text-center p-5">Error loading PDF: ${reason.message || reason}. Please check the file URL and permissions.</p>`;
        });

        // --- Cleanup ---
        window.addEventListener('beforeunload', function() {
            clearInterval(timerInterval);
            // Optional: Trigger auto-save here if needed
            // saveProgress(); // Be careful with synchronous requests here
        });
    });
</script>

<?php
require_once $footerPath; // Your standard footer
?>