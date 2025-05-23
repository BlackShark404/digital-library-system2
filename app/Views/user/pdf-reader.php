<?php
include $headerPath;
?>

<div class="pdf-reader-container">
    <!-- Fixed Top Navigation Bar -->
    <div class="pdf-reader-navbar">
        <div class="navbar-left">
            <a href="/user/reading-sessions" class="back-btn">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h5 class="book-title"><?= $session['b_title'] ?></h5>
            <span class="book-author"><?= $session['b_author'] ?></span>
        </div>
        
        <div class="navbar-center d-none d-md-flex">
            <button id="prev" class="nav-btn">
                <i class="bi bi-chevron-left"></i>
            </button>
            <div class="page-display">
                <span id="page_num">0</span> / <span id="page_count">0</span>
            </div>
            <button id="next" class="nav-btn">
                <i class="bi bi-chevron-right"></i>
            </button>
        </div>
        
        <div class="navbar-right">
            <?php if ($hasExpired && !$isPurchased): ?>
                <div class="session-expired">
                    <i class="bi bi-exclamation-triangle-fill"></i> Session expired
                </div>
                <a href="/purchase/checkout?book_id=<?= $session['b_id'] ?>" class="purchase-btn">
                    <i class="bi bi-cart-plus"></i> Purchase
                </a>
            <?php else: ?>
                <div class="session-badge <?= $isPurchased ? 'purchased' : '' ?>">
                    <?php if ($isPurchased): ?>
                        <i class="bi bi-check-circle-fill"></i> Purchased
                    <?php else: ?>
                        <i class="bi bi-clock"></i> Expires: <?= date('M d, Y', strtotime($session['rs_expires_at'])) ?>
                    <?php endif; ?>
                </div>
                <div class="controls-group">
                    <button id="zoomOut" class="control-btn" title="Zoom Out">
                        <i class="bi bi-zoom-out"></i>
                    </button>
                    <button id="zoomIn" class="control-btn" title="Zoom In">
                        <i class="bi bi-zoom-in"></i>
                    </button>
                    <button id="bookmark" class="control-btn" title="Bookmark">
                        <i class="bi bi-bookmark"></i>
                    </button>
                    <button id="fullscreen" class="control-btn" title="Fullscreen">
                        <i class="bi bi-arrows-fullscreen"></i>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Floating Mobile Controls -->
    <div class="mobile-controls d-md-none">
        <button id="prev-mobile" class="mobile-nav-btn">
            <i class="bi bi-chevron-left"></i>
        </button>
        <div class="mobile-page-display">
            <span id="page_num_mobile">0</span> / <span id="page_count_mobile">0</span>
        </div>
        <button id="next-mobile" class="mobile-nav-btn">
            <i class="bi bi-chevron-right"></i>
        </button>
    </div>

    <!-- PDF Viewer Container -->
    <div id="viewerContainer">
        <!-- Error Message -->
        <div id="errorMessage">
            <div class="error-content">
                <i class="bi bi-exclamation-triangle"></i>
                <span id="errorText">Error loading PDF</span>
            </div>
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner">
            <div class="spinner"></div>
            <p>Loading your book...</p>
        </div>

        <!-- PDF Canvas -->
        <canvas id="pdfCanvas"></canvas>
    </div>
    
    <!-- Progress Bar -->
    <div class="reading-progress-bar">
        <div id="progressIndicator"></div>
    </div>
    
    <!-- Bookmark Modal -->
    <div class="modal fade" id="bookmarkModal" tabindex="-1" aria-labelledby="bookmarkModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookmarkModalLabel">Bookmarks</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex mb-3">
                        <input type="text" id="bookmarkName" class="form-control me-2" placeholder="New bookmark name">
                        <button id="addBookmark" class="btn btn-primary">Add</button>
                    </div>
                    
                    <div id="bookmarksList" class="list-group">
                        <!-- Bookmarks will be added here -->
                        <div class="text-center text-muted p-3" id="noBookmarksMsg">
                            No bookmarks yet
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PDF.js library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    // Set up PDF.js
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

    document.addEventListener('DOMContentLoaded', function() {
        const sessionId = <?= $session['rs_id'] ?>;
        const bookId = <?= $session['b_id'] ?>;
        const pdfUrl = '/assets/books/<?= $session['b_file_path'] ?>';
        const startPage = <?= isset($session['current_page']) ? max(1, intval($session['current_page'])) : 1 ?>;
        const totalBookPages = <?= isset($session['b_pages']) ? intval($session['b_pages']) : 0 ?>;
        
        // UI Elements
        const canvas = document.getElementById('pdfCanvas');
        const ctx = canvas.getContext('2d');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');
        const progressIndicator = document.getElementById('progressIndicator');
        const bookmarkBtn = document.getElementById('bookmark');
        
        // Navigation elements
        const prevBtn = document.getElementById('prev');
        const nextBtn = document.getElementById('next');
        const prevBtnMobile = document.getElementById('prev-mobile');
        const nextBtnMobile = document.getElementById('next-mobile');
        const pageNumDisplay = document.getElementById('page_num');
        const pageCountDisplay = document.getElementById('page_count');
        const pageNumMobileDisplay = document.getElementById('page_num_mobile');
        const pageCountMobileDisplay = document.getElementById('page_count_mobile');
        
        // Zoom controls
        const zoomInBtn = document.getElementById('zoomIn');
        const zoomOutBtn = document.getElementById('zoomOut');
        const fullscreenBtn = document.getElementById('fullscreen');
        
        // State variables
        let pdfDoc = null;
        let pageNum = startPage;
        let pageRendering = false;
        let pageNumPending = null;
        let scale = 1.0;
        let bookmarks = [];
        
        // Initial element states
        errorMessage.style.display = 'none';
        
        // Load bookmarks from localStorage
        loadBookmarks();
        
        /**
         * Load the PDF document
         */
        pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
            pdfDoc = pdf;
            
            // Update page count display
            const numPages = pdf.numPages;
            pageCountDisplay.textContent = numPages;
            pageCountMobileDisplay.textContent = numPages;
            
            // Check if the requested page is valid
            if (pageNum > numPages) {
                pageNum = 1;
            }
            
            // Render the first page
            renderPage(pageNum);
            
            // Hide loading spinner
            loadingSpinner.style.display = 'none';
            
            // Update progress bar
            updateProgressBar();
        }).catch(function(error) {
            console.error('Error loading PDF:', error);
            loadingSpinner.style.display = 'none';
            errorMessage.style.display = 'flex';
            errorText.textContent = 'Error loading PDF: ' + error.message;
        });
        
        /**
         * Render a specific page of the PDF
         */
        function renderPage(num) {
            pageRendering = true;
            
            // Update UI page numbers
            pageNumDisplay.textContent = num;
            pageNumMobileDisplay.textContent = num;
            
            // Update progress bar
            updateProgressBar();
            
            // Update reading progress on the server
            saveProgress(num);
            
            // Get the page
            pdfDoc.getPage(num).then(function(page) {
                // Adjust scale based on viewport
                const viewportWidth = document.getElementById('viewerContainer').clientWidth - 20;
                const viewportHeight = document.getElementById('viewerContainer').clientHeight - 20;
                
                const originalViewport = page.getViewport({ scale: 1 });
                
                // Calculate scale to fit page within container
                const widthScale = viewportWidth / originalViewport.width;
                const heightScale = viewportHeight / originalViewport.height;
                const fitScale = Math.min(widthScale, heightScale);
                
                // Apply user scale on top of fit scale
                const viewport = page.getViewport({ scale: fitScale * scale });
                
                // Set canvas dimensions
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                
                // Render PDF page into canvas context
                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                
                const renderTask = page.render(renderContext);
                
                // Wait for rendering to finish
                renderTask.promise.then(function() {
                    pageRendering = false;
                    
                    // Check if there's a pending page
                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                }).catch(function(error) {
                    console.error('Error rendering page:', error);
                    pageRendering = false;
                    errorMessage.style.display = 'flex';
                    errorText.textContent = 'Error rendering page: ' + error.message;
                });
            }).catch(function(error) {
                console.error('Error getting page:', error);
                pageRendering = false;
                errorMessage.style.display = 'flex';
                errorText.textContent = 'Error getting page: ' + error.message;
            });
        }
        
        /**
         * Display previous page
         */
        function showPrevPage() {
            if (pageNum <= 1) return;
            
            pageNum--;
            queueRenderPage(pageNum);
        }
        
        /**
         * Display next page
         */
        function showNextPage() {
            if (pageNum >= pdfDoc.numPages) return;
            
            pageNum++;
            queueRenderPage(pageNum);
        }
        
        /**
         * Queue a page for rendering
         */
        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }
        
        /**
         * Update the progress bar
         */
        function updateProgressBar() {
            if (!pdfDoc) return;
            
            const progress = (pageNum / pdfDoc.numPages) * 100;
            progressIndicator.style.width = progress + '%';
        }
        
        /**
         * Save reading progress to the server
         */
        function saveProgress(page) {
            fetch('/reading-session/update-progress', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    session_id: sessionId,
                    current_page: page,
                    is_completed: page === pdfDoc.numPages
                })
            })
            .then(response => response.json())
            .catch(error => {
                console.error('Error saving reading progress:', error);
            });
        }
        
        /**
         * Handle zoom in
         */
        function zoomIn() {
            if (scale >= 3.0) return;
            scale += 0.1;
            queueRenderPage(pageNum);
        }
        
        /**
         * Handle zoom out
         */
        function zoomOut() {
            if (scale <= 0.5) return;
            scale -= 0.1;
            queueRenderPage(pageNum);
        }
        
        /**
         * Toggle fullscreen mode
         */
        function toggleFullscreen() {
            const container = document.getElementById('viewerContainer');
            
            if (!document.fullscreenElement) {
                container.requestFullscreen().catch(err => {
                    console.error('Error attempting to enable full-screen mode:', err);
                });
            } else {
                document.exitFullscreen();
            }
        }
        
        /**
         * Handle bookmark functionality
         */
        function setupBookmarkSystem() {
            // Set up bookmark modal
            const bookmarkModal = new bootstrap.Modal(document.getElementById('bookmarkModal'));
            const addBookmarkBtn = document.getElementById('addBookmark');
            const bookmarkNameInput = document.getElementById('bookmarkName');
            const bookmarksList = document.getElementById('bookmarksList');
            const noBookmarksMsg = document.getElementById('noBookmarksMsg');
            
            // Show bookmark modal
            bookmarkBtn.addEventListener('click', function() {
                showBookmarks();
                bookmarkModal.show();
            });
            
            // Add a new bookmark
            addBookmarkBtn.addEventListener('click', function() {
                const name = bookmarkNameInput.value.trim() || `Page ${pageNum}`;
                
                const newBookmark = {
                    id: Date.now(),
                    name: name,
                    page: pageNum,
                    createdAt: new Date().toISOString()
                };
                
                bookmarks.push(newBookmark);
                saveBookmarks();
                showBookmarks();
                
                bookmarkNameInput.value = '';
            });
            
            // Show bookmarks in the modal
            function showBookmarks() {
                if (bookmarks.length === 0) {
                    noBookmarksMsg.style.display = 'block';
                    bookmarksList.innerHTML = '';
                    return;
                }
                
                noBookmarksMsg.style.display = 'none';
                
                // Sort bookmarks by page number
                bookmarks.sort((a, b) => a.page - b.page);
                
                let html = '';
                bookmarks.forEach(bookmark => {
                    html += `
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${bookmark.name}</strong>
                                <small class="text-muted d-block">Page ${bookmark.page}</small>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-primary go-to-bookmark" data-page="${bookmark.page}">
                                    Go
                                </button>
                                <button class="btn btn-sm btn-outline-danger delete-bookmark" data-id="${bookmark.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                bookmarksList.innerHTML = html;
                
                // Add event listeners for bookmark actions
                document.querySelectorAll('.go-to-bookmark').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const page = parseInt(this.dataset.page);
                        pageNum = page;
                        queueRenderPage(page);
                        bookmarkModal.hide();
                    });
                });
                
                document.querySelectorAll('.delete-bookmark').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = parseInt(this.dataset.id);
                        bookmarks = bookmarks.filter(b => b.id !== id);
                        saveBookmarks();
                        showBookmarks();
                    });
                });
            }
        }
        
        /**
         * Save bookmarks to localStorage
         */
        function saveBookmarks() {
            localStorage.setItem(`bookmarks_${sessionId}`, JSON.stringify(bookmarks));
        }
        
        /**
         * Load bookmarks from localStorage
         */
        function loadBookmarks() {
            const saved = localStorage.getItem(`bookmarks_${sessionId}`);
            if (saved) {
                try {
                    bookmarks = JSON.parse(saved);
                } catch (e) {
                    console.error('Error parsing bookmarks:', e);
                    bookmarks = [];
                }
            } else {
                bookmarks = [];
            }
        }
        
        // Event listeners for navigation
        prevBtn.addEventListener('click', showPrevPage);
        nextBtn.addEventListener('click', showNextPage);
        prevBtnMobile.addEventListener('click', showPrevPage);
        nextBtnMobile.addEventListener('click', showNextPage);
        
        // Event listeners for zoom
        zoomInBtn.addEventListener('click', zoomIn);
        zoomOutBtn.addEventListener('click', zoomOut);
        fullscreenBtn.addEventListener('click', toggleFullscreen);
        
        // Set up keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowRight') {
                showNextPage();
            } else if (e.key === 'ArrowLeft') {
                showPrevPage();
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            // Re-render current page to adjust to new size
            queueRenderPage(pageNum);
        });
        
        // Set up the bookmark system
        setupBookmarkSystem();
    });
</script>

<style>
/* PDF Reader Styles */
.pdf-reader-container {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
    background-color: #f0f2f5;
    z-index: 1030; /* Above normal content */
}

.pdf-reader-navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 1rem;
    background-color: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
    z-index: 1;
}

.navbar-left {
    display: flex;
    align-items: center;
}

.back-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background: #f8f9fa;
    color: #495057;
    margin-right: 1rem;
    text-decoration: none;
}

.book-title {
    margin: 0;
    font-size: 1rem;
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.book-author {
    color: #6c757d;
    font-size: 0.8rem;
    margin-left: 0.5rem;
}

.navbar-center {
    display: flex;
    align-items: center;
}

.nav-btn {
    background: none;
    border: none;
    color: #495057;
    font-size: 1.25rem;
    cursor: pointer;
    padding: 0.25rem 0.5rem;
}

.page-display {
    margin: 0 0.75rem;
    font-weight: 500;
}

.navbar-right {
    display: flex;
    align-items: center;
}

.session-expired {
    color: #dc3545;
    font-size: 0.875rem;
    margin-right: 1rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.purchase-btn {
    padding: 0.375rem 0.75rem;
    background-color: #28a745;
    color: #fff;
    border-radius: 0.25rem;
    text-decoration: none;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.session-badge {
    padding: 0.25rem 0.5rem;
    background-color: #f0f2f5;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    margin-right: 1rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.session-badge.purchased {
    background-color: #d4edda;
    color: #155724;
}

.controls-group {
    display: flex;
    gap: 0.5rem;
}

.control-btn {
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    background: #f8f9fa;
    color: #495057;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

#viewerContainer {
    flex: 1;
    position: relative;
    overflow: auto;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    background-color: #444;
    padding-top: 20px;
}

#pdfCanvas {
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.15);
    margin-bottom: 20px;
}

#errorMessage {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10;
}

.error-content {
    text-align: center;
    color: #dc3545;
}

#loadingSpinner {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 10;
}

.spinner {
    width: 3rem;
    height: 3rem;
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-left-color: #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.reading-progress-bar {
    height: 4px;
    background-color: #dee2e6;
    width: 100%;
}

#progressIndicator {
    height: 100%;
    background-color: #007bff;
    width: 0;
    transition: width 0.3s ease;
}

.mobile-controls {
    position: fixed;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    background-color: rgba(255, 255, 255, 0.9);
    padding: 0.5rem;
    border-radius: 50px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    z-index: 100;
}

.mobile-nav-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f8f9fa;
    color: #495057;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.mobile-page-display {
    margin: 0 1rem;
    font-weight: 500;
}

@media (max-width: 767.98px) {
    .book-title {
        max-width: 150px;
    }
    
    .book-author {
        display: none;
    }
    
    .control-btn {
        width: 1.8rem;
        height: 1.8rem;
        font-size: 0.9rem;
    }
}
</style>

<?php
include $footerPath;
?> 