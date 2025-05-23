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
                    <button id="toggleToc" class="control-btn" title="Table of Contents">
                        <i class="bi bi-list-ul"></i>
                    </button>
                    <button id="zoomOut" class="control-btn" title="Zoom Out">
                        <i class="bi bi-zoom-out"></i>
                    </button>
                    <button id="zoomIn" class="control-btn" title="Zoom In">
                        <i class="bi bi-zoom-in"></i>
                    </button>
                    <button id="toggleAntialiasing" class="control-btn" title="Toggle Text Sharpness">
                        <i class="bi bi-type"></i>
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

    <!-- Table of Contents Sidebar -->
    <div id="tocSidebar" class="toc-sidebar">
        <div class="toc-header">
            <h5>Table of Contents</h5>
            <button id="closeToc" class="close-toc">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="toc-content">
            <div id="loadingToc" class="toc-loading">
                <div class="spinner-sm"></div>
                <span>Loading contents...</span>
            </div>
            <div id="tocEmpty" class="toc-empty" style="display: none;">
                <i class="bi bi-info-circle"></i>
                <span>No table of contents available</span>
            </div>
            <ul id="tocList" class="toc-list"></ul>
        </div>
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

        <!-- Zoom Indicator -->
        <div id="zoomIndicator" class="zoom-indicator">
            <span id="zoomPercent">100%</span>
        </div>

        <!-- PDF Container for continuous scroll -->
        <div id="pdfPagesContainer"></div>
    </div>
    
    <!-- Progress Bar -->
    <div class="reading-progress-bar">
        <div id="progressIndicator"></div>
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
        const loadingSpinner = document.getElementById('loadingSpinner');
        const errorMessage = document.getElementById('errorMessage');
        const errorText = document.getElementById('errorText');
        const progressIndicator = document.getElementById('progressIndicator');
        const zoomIndicator = document.getElementById('zoomIndicator');
        const zoomPercent = document.getElementById('zoomPercent');
        
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
        const antialiasingBtn = document.getElementById('toggleAntialiasing');
        
        // State variables
        let pdfDoc = null;
        let pageNum = startPage;
        let pageRendering = false;
        let pageNumPending = null;
        let scale = 1.0;
        let useAntialiasing = true; // Default state of antialiasing
        let tocVisible = false;     // Track TOC visibility state
        let pagesRendered = {};     // Track which pages have been rendered
        let visiblePages = [];      // Currently visible pages
        let scrollTimeout = null;   // For scroll debouncing
        let pagesContainer = null;  // Container for PDF pages
        
        // Initial element states
        errorMessage.style.display = 'none';
        pagesContainer = document.getElementById('pdfPagesContainer');
        
        // Load settings from localStorage
        loadZoomLevel();
        loadAntialiasingSetting();
        
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
            
            // Initialize the PDF viewer with continuous scrolling
            initContinuousScrollViewer(pageNum);
            
            // Load table of contents if available
            loadTableOfContents(pdf);
            
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
         * Initialize the continuous scroll PDF viewer
         */
        function initContinuousScrollViewer(startPageNum) {
            // Clear any existing content
            pagesContainer.innerHTML = '';
            pagesRendered = {};
            
            // Create initial viewport for sizing calculations
            pdfDoc.getPage(1).then(function(page) {
                const viewport = page.getViewport({ scale: 1.0 });
                const pageWidth = viewport.width;
                
                // Create placeholders for all pages
                for (let i = 1; i <= pdfDoc.numPages; i++) {
                    const pageContainer = document.createElement('div');
                    pageContainer.className = 'pdf-page-container';
                    pageContainer.id = `page-container-${i}`;
                    pageContainer.dataset.pageNumber = i;
                    
                    // Size placeholder to match page's eventual dimensions
                    const aspectRatio = viewport.height / viewport.width;
                    const placeholderHeight = pageWidth * scale * aspectRatio;
                    
                    pageContainer.style.height = `${placeholderHeight}px`;
                    pageContainer.style.width = `${pageWidth * scale}px`;
                    pageContainer.style.margin = '0 auto 20px auto';
                    
                    // Add page number indicator
                    const pageNumberIndicator = document.createElement('div');
                    pageNumberIndicator.className = 'page-number-indicator';
                    pageNumberIndicator.textContent = i;
                    pageContainer.appendChild(pageNumberIndicator);
                    
                    pagesContainer.appendChild(pageContainer);
                }
                
                // Set up scroll event listener for lazy loading pages
                const viewerContainer = document.getElementById('viewerContainer');
                viewerContainer.addEventListener('scroll', handleScroll);
                
                // Render the start page and surrounding pages
                const pagesToRenderInitially = [startPageNum];
                if (startPageNum > 1) pagesToRenderInitially.push(startPageNum - 1);
                if (startPageNum < pdfDoc.numPages) pagesToRenderInitially.push(startPageNum + 1);
                
                // Render initial pages
                pagesToRenderInitially.forEach(pageNum => {
                    renderPage(pageNum);
                });
                
                // Scroll to the starting page
                scrollToPage(startPageNum);
                
                // Update the current visible pages after initial render
                setTimeout(updateVisiblePages, 100);
            });
        }
        
        /**
         * Handle scroll events to load pages dynamically
         */
        function handleScroll() {
            // Clear previous timeout to debounce scroll events
            if (scrollTimeout) {
                clearTimeout(scrollTimeout);
            }
            
            // Set a new timeout
            scrollTimeout = setTimeout(function() {
                updateVisiblePages();
                
                // Render pages that are now visible or will soon be visible
                const pagesToRender = [];
                
                // Find visible pages and surrounding pages to preload
                visiblePages.forEach(pageNum => {
                    if (!pagesRendered[pageNum]) {
                        pagesToRender.push(pageNum);
                    }
                    
                    // Preload next and previous pages
                    if (pageNum > 1 && !pagesRendered[pageNum - 1]) {
                        pagesToRender.push(pageNum - 1);
                    }
                    
                    if (pageNum < pdfDoc.numPages && !pagesRendered[pageNum + 1]) {
                        pagesToRender.push(pageNum + 1);
                    }
                });
                
                // Ensure we render pages in the correct order
                pagesToRender.sort().forEach(pageNum => {
                    renderPage(pageNum);
                });
                
                // Update progress based on the midpoint of visible pages
                if (visiblePages.length > 0) {
                    // Get the middle page number
                    const midPageIndex = Math.floor(visiblePages.length / 2);
                    const currentPageNum = visiblePages[midPageIndex];
                    
                    // Update displayed page number
                    pageNum = currentPageNum;
                    pageNumDisplay.textContent = currentPageNum;
                    pageNumMobileDisplay.textContent = currentPageNum;
                    
                    // Update progress bar
                    updateProgressBar();
                    
                    // Update TOC highlighting
                    highlightCurrentTocItem(currentPageNum);
                    
                    // Save progress
                    saveProgress(currentPageNum);
                }
            }, 100); // 100ms debounce
        }
        
        /**
         * Update the array of currently visible pages
         */
        function updateVisiblePages() {
            const viewerContainer = document.getElementById('viewerContainer');
            const containerRect = viewerContainer.getBoundingClientRect();
            visiblePages = [];
            
            // Check each page container to see if it's visible
            for (let i = 1; i <= pdfDoc.numPages; i++) {
                const pageContainer = document.getElementById(`page-container-${i}`);
                if (!pageContainer) continue;
                
                const pageRect = pageContainer.getBoundingClientRect();
                
                // Page is visible if it intersects with the viewer container
                if (
                    pageRect.top < containerRect.bottom &&
                    pageRect.bottom > containerRect.top
                ) {
                    visiblePages.push(i);
                }
            }
        }
        
        /**
         * Render a specific page of the PDF
         */
        function renderPage(num) {
            // Check if the page is already rendered or being rendered
            if (pagesRendered[num] || document.getElementById(`canvas-${num}`)) {
                return;
            }
            
            // Mark as rendering in progress
            pagesRendered[num] = 'rendering';
            
            // Get the page
            pdfDoc.getPage(num).then(function(page) {
                // Get page container
                const pageContainer = document.getElementById(`page-container-${num}`);
                if (!pageContainer) return;
                
                // Get device pixel ratio
                const devicePixelRatio = window.devicePixelRatio || 1;
                
                // Calculate dimensions for this page
                const viewport = page.getViewport({ scale: scale });
                
                // Create canvas for this page
                const canvas = document.createElement('canvas');
                canvas.id = `canvas-${num}`;
                canvas.width = Math.floor(viewport.width * devicePixelRatio);
                canvas.height = Math.floor(viewport.height * devicePixelRatio);
                canvas.style.width = Math.floor(viewport.width) + "px";
                canvas.style.height = Math.floor(viewport.height) + "px";
                
                // Position canvas in page container - no longer absolute positioning
                canvas.style.position = 'relative';
                canvas.style.margin = '0 auto';
                
                // Append canvas to page container
                pageContainer.appendChild(canvas);
                
                // Adjust page container height to match actual page
                pageContainer.style.height = Math.floor(viewport.height) + "px";
                pageContainer.style.width = Math.floor(viewport.width) + "px";
                
                // Get context for rendering
                const ctx = canvas.getContext('2d');
                
                // Reset context and prepare for high-quality rendering
                ctx.setTransform(1, 0, 0, 1, 0, 0);
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                // Enable font smoothing
                ctx.imageSmoothingEnabled = useAntialiasing;
                ctx.imageSmoothingQuality = 'high';
                
                // Scale context to ensure correct rendering
                ctx.scale(devicePixelRatio, devicePixelRatio);
                
                // Render PDF page into canvas context with high quality settings
                const renderContext = {
                    canvasContext: ctx,
                    viewport: viewport,
                    enableWebGL: true,
                    renderInteractiveForms: true,
                    textLayer: true
                };
                
                const renderTask = page.render(renderContext);
                
                // Wait for rendering to finish
                renderTask.promise.then(function() {
                    // Mark as fully rendered
                    pagesRendered[num] = 'rendered';
                }).catch(function(error) {
                    console.error(`Error rendering page ${num}:`, error);
                    pageContainer.innerHTML = `<div class="page-error">Error rendering page ${num}</div>`;
                    pagesRendered[num] = 'error';
                });
            }).catch(function(error) {
                console.error(`Error getting page ${num}:`, error);
                pagesRendered[num] = 'error';
            });
        }
        
        /**
         * Scroll to a specific page
         */
        function scrollToPage(num) {
            const pageContainer = document.getElementById(`page-container-${num}`);
            if (pageContainer) {
                const viewerContainer = document.getElementById('viewerContainer');
                viewerContainer.scrollTo({
                    top: pageContainer.offsetTop - 20,
                    behavior: 'smooth'
                });
                
                // Update page number display
                pageNum = num;
                pageNumDisplay.textContent = num;
                pageNumMobileDisplay.textContent = num;
                
                // Highlight TOC item
                highlightCurrentTocItem(num);
            }
        }
        
        /**
         * Display previous page
         */
        function showPrevPage() {
            if (pageNum <= 1) return;
            
            const newPage = pageNum - 1;
            scrollToPage(newPage);
        }
        
        /**
         * Display next page
         */
        function showNextPage() {
            if (pageNum >= pdfDoc.numPages) return;
            
            const newPage = pageNum + 1;
            scrollToPage(newPage);
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
            saveZoomLevel();
            showZoomIndicator();
            
            // Resize and re-render all pages
            resizeAndRerenderPages();
        }
        
        /**
         * Handle zoom out
         */
        function zoomOut() {
            if (scale <= 0.5) return;
            scale -= 0.1;
            saveZoomLevel();
            showZoomIndicator();
            
            // Resize and re-render all pages
            resizeAndRerenderPages();
        }
        
        /**
         * Show zoom indicator and hide after a delay
         */
        function showZoomIndicator() {
            // Update zoom percentage
            const zoomPercentValue = Math.round(scale * 100);
            showMessage(zoomPercentValue + '%');
        }
        
        /**
         * Load zoom level from localStorage
         */
        function loadZoomLevel() {
            // Try to load book-specific zoom level first
            const savedScale = localStorage.getItem(`zoomLevel_${sessionId}`);
            if (savedScale) {
                const parsedScale = parseFloat(savedScale);
                if (!isNaN(parsedScale) && parsedScale >= 0.5 && parsedScale <= 3.0) {
                    scale = parsedScale;
                    return;
                }
            }
            
            // Fall back to global zoom preference
            const globalScale = localStorage.getItem('globalZoomLevel');
            if (globalScale) {
                const parsedScale = parseFloat(globalScale);
                if (!isNaN(parsedScale) && parsedScale >= 0.5 && parsedScale <= 3.0) {
                    scale = parsedScale;
                }
            }
        }
        
        /**
         * Save zoom level to localStorage
         */
        function saveZoomLevel() {
            // Save both session-specific and global zoom preference
            localStorage.setItem(`zoomLevel_${sessionId}`, scale.toString());
            localStorage.setItem('globalZoomLevel', scale.toString());
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
         * Toggle text antialiasing for clarity
         */
        function toggleAntialiasing() {
            useAntialiasing = !useAntialiasing;
            
            // Update button visual state
            if (useAntialiasing) {
                antialiasingBtn.classList.remove('active');
            } else {
                antialiasingBtn.classList.add('active');
            }
            
            // Save preference
            localStorage.setItem('pdfAntialiasing', useAntialiasing ? 'true' : 'false');
            
            // Show indicator with current state
            const message = useAntialiasing ? 'Text smoothing: ON' : 'Text sharpening: ON';
            showMessage(message);
            
            // Re-render current page with new setting
            scrollToPage(pageNum);
        }
        
        /**
         * Load antialiasing setting from localStorage
         */
        function loadAntialiasingSetting() {
            const savedSetting = localStorage.getItem('pdfAntialiasing');
            if (savedSetting !== null) {
                useAntialiasing = savedSetting === 'true';
                
                // Update button visual state
                if (!useAntialiasing) {
                    antialiasingBtn.classList.add('active');
                }
            }
        }
        
        /**
         * Show a temporary message notification
         */
        function showMessage(message) {
            // Update zoom indicator to show the message
            zoomPercent.textContent = message;
            
            // Show indicator
            zoomIndicator.classList.add('visible');
            
            // Hide after delay
            clearTimeout(window.zoomTimeout);
            window.zoomTimeout = setTimeout(() => {
                zoomIndicator.classList.remove('visible');
            }, 1500);
        }
        
        /**
         * Load and display table of contents if available
         */
        function loadTableOfContents(pdf) {
            const loadingToc = document.getElementById('loadingToc');
            const tocEmpty = document.getElementById('tocEmpty');
            const tocList = document.getElementById('tocList');
            
            // Show loading indicator
            loadingToc.style.display = 'flex';
            tocEmpty.style.display = 'none';
            tocList.innerHTML = '';
            
            // Get the table of contents from the PDF
            pdf.getOutline().then(function(outline) {
                // Hide loading indicator
                loadingToc.style.display = 'none';
                
                if (!outline || outline.length === 0) {
                    // No TOC available
                    tocEmpty.style.display = 'flex';
                    return;
                }
                
                // Store TOC data for later use
                window.tocData = processOutlineItems(outline);
                
                // Render TOC items
                renderTocItems(outline, tocList);
                
                // Highlight current TOC item based on initial page
                highlightCurrentTocItem(pageNum);
            }).catch(function(error) {
                console.error('Error loading table of contents:', error);
                loadingToc.style.display = 'none';
                tocEmpty.style.display = 'flex';
            });
        }
        
        /**
         * Process outline items to extract page destinations
         */
        function processOutlineItems(items, result = []) {
            items.forEach(function(item) {
                if (item.dest) {
                    result.push({
                        title: item.title,
                        dest: item.dest,
                        pageNum: null,  // Will be resolved later
                        element: null   // Will be set when rendered
                    });
                }
                
                if (item.items && item.items.length > 0) {
                    processOutlineItems(item.items, result);
                }
            });
            
            return result;
        }
        
        /**
         * Recursively render TOC items
         */
        function renderTocItems(items, container, level = 0) {
            items.forEach(function(item) {
                const li = document.createElement('li');
                li.className = 'toc-item';
                
                const link = document.createElement('a');
                link.textContent = item.title;
                link.href = '#';
                link.dataset.title = item.title;
                link.style.paddingLeft = (level * 15) + 'px';
                
                // Store reference to this element in tocData
                if (window.tocData) {
                    const tocItem = window.tocData.find(entry => entry.title === item.title);
                    if (tocItem) {
                        tocItem.element = link;
                    }
                }
                
                // Handle click event to navigate to the destination
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    if (item.dest) {
                        navigateToDestination(item.dest);
                        
                        // On mobile, automatically close the TOC after selection
                        if (window.innerWidth < 768) {
                            toggleTableOfContents(false);
                        }
                    }
                });
                
                li.appendChild(link);
                container.appendChild(li);
                
                // Handle nested items
                if (item.items && item.items.length > 0) {
                    const nestedUl = document.createElement('ul');
                    nestedUl.className = 'nested-toc';
                    li.appendChild(nestedUl);
                    renderTocItems(item.items, nestedUl, level + 1);
                }
            });
        }
        
        /**
         * Navigate to a destination in the PDF
         */
        function navigateToDestination(dest) {
            if (typeof dest === 'string') {
                // Named destination
                pdfDoc.getDestination(dest).then(function(destination) {
                    navigateToExplicitDestination(destination);
                }).catch(function(error) {
                    console.error('Error resolving named destination:', error);
                });
            } else if (Array.isArray(dest)) {
                // Explicit destination
                navigateToExplicitDestination(dest);
            } else {
                console.error('Unsupported destination format');
            }
        }
        
        /**
         * Navigate to an explicit destination in the PDF
         */
        function navigateToExplicitDestination(explicitDest) {
            if (!Array.isArray(explicitDest) || explicitDest.length < 1) {
                console.error('Invalid destination format');
                return;
            }
            
            // Get the page reference from the destination array
            const pageRef = explicitDest[0];
            
            // Get the page number from the page reference
            pdfDoc.getPageIndex(pageRef).then(function(pageIndex) {
                // PDF.js page indices are zero-based, but our pageNum is one-based
                const targetPage = pageIndex + 1;
                
                // Navigate to the target page
                scrollToPage(targetPage);
                
                // Update progress bar
                updateProgressBar();
            }).catch(function(error) {
                console.error('Error navigating to destination:', error);
            });
        }
        
        /**
         * Highlight the TOC item that corresponds to the current page
         */
        function highlightCurrentTocItem(currentPage) {
            if (!window.tocData || window.tocData.length === 0) return;
            
            // Clear all current highlights
            document.querySelectorAll('.toc-item a').forEach(link => {
                link.classList.remove('active');
            });
            
            // If page destinations aren't resolved yet, resolve them
            const unresolvedItems = window.tocData.filter(item => item.pageNum === null);
            if (unresolvedItems.length > 0) {
                resolveAllTocPageNumbers().then(() => {
                    setActiveItemBasedOnPage(currentPage);
                }).catch(error => {
                    console.error('Error resolving TOC destinations:', error);
                });
            } else {
                // Page numbers already resolved, find the active item
                setActiveItemBasedOnPage(currentPage);
            }
        }
        
        /**
         * Resolve all TOC item destinations to page numbers
         */
        function resolveAllTocPageNumbers() {
            const promises = window.tocData.map(item => {
                if (item.pageNum !== null) return Promise.resolve();
                
                return new Promise((resolve, reject) => {
                    if (typeof item.dest === 'string') {
                        // Named destination
                        pdfDoc.getDestination(item.dest).then(destination => {
                            if (destination && Array.isArray(destination) && destination.length > 0) {
                                const pageRef = destination[0];
                                return pdfDoc.getPageIndex(pageRef);
                            }
                            return Promise.reject('Invalid destination');
                        }).then(pageIndex => {
                            item.pageNum = pageIndex + 1;  // Convert zero-based to one-based
                            resolve();
                        }).catch(reject);
                    } else if (Array.isArray(item.dest) && item.dest.length > 0) {
                        // Explicit destination
                        const pageRef = item.dest[0];
                        pdfDoc.getPageIndex(pageRef).then(pageIndex => {
                            item.pageNum = pageIndex + 1;  // Convert zero-based to one-based
                            resolve();
                        }).catch(reject);
                    } else {
                        reject('Unsupported destination format');
                    }
                });
            });
            
            return Promise.all(promises);
        }
        
        /**
         * Set the active TOC item based on current page
         */
        function setActiveItemBasedOnPage(currentPage) {
            if (!window.tocData || window.tocData.length === 0) return;
            
            // Find the closest TOC item that's before or at the current page
            let activeItem = null;
            let closestPageDiff = Number.MAX_SAFE_INTEGER;
            
            window.tocData.forEach(item => {
                if (item.pageNum === null || !item.element) return;
                
                if (item.pageNum <= currentPage) {
                    const diff = currentPage - item.pageNum;
                    if (diff < closestPageDiff) {
                        closestPageDiff = diff;
                        activeItem = item;
                    }
                }
            });
            
            // Highlight the active item
            if (activeItem && activeItem.element) {
                activeItem.element.classList.add('active');
                
                // Scroll the active item into view if it's not visible
                activeItem.element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
        
        /**
         * Toggle table of contents visibility
         */
        function toggleTableOfContents(forceState = null) {
            const tocSidebar = document.getElementById('tocSidebar');
            
            // Determine new state
            tocVisible = forceState !== null ? forceState : !tocVisible;
            
            // Update UI
            if (tocVisible) {
                tocSidebar.classList.add('visible');
                document.body.classList.add('toc-is-visible');
                
                // Add click outside listener after a short delay to prevent immediate closing
                setTimeout(() => {
                    document.addEventListener('click', handleOutsideClick);
                }, 10);
            } else {
                tocSidebar.classList.remove('visible');
                document.body.classList.remove('toc-is-visible');
                
                // Remove click outside listener
                document.removeEventListener('click', handleOutsideClick);
            }
        }
        
        /**
         * Handle clicks outside the TOC sidebar to close it
         */
        function handleOutsideClick(event) {
            const tocSidebar = document.getElementById('tocSidebar');
            const tocToggleBtn = document.getElementById('toggleToc');
            
            // Check if click is outside TOC sidebar and not on the toggle button
            if (tocVisible && 
                !tocSidebar.contains(event.target) && 
                event.target !== tocToggleBtn && 
                !tocToggleBtn.contains(event.target)) {
                
                toggleTableOfContents(false);
            }
        }
        
        // Event listeners for navigation
        prevBtn.addEventListener('click', showPrevPage);
        nextBtn.addEventListener('click', showNextPage);
        prevBtnMobile.addEventListener('click', showPrevPage);
        nextBtnMobile.addEventListener('click', showNextPage);
        
        // Event listeners for TOC
        document.getElementById('toggleToc').addEventListener('click', function() {
            toggleTableOfContents();
        });
        
        document.getElementById('closeToc').addEventListener('click', function() {
            toggleTableOfContents(false);
        });
        
        // Event listeners for zoom
        zoomInBtn.addEventListener('click', zoomIn);
        zoomOutBtn.addEventListener('click', zoomOut);
        fullscreenBtn.addEventListener('click', toggleFullscreen);
        
        // Event listener for antialiasing toggle
        antialiasingBtn.addEventListener('click', toggleAntialiasing);
        
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
            scrollToPage(pageNum);
        });

        /**
         * Resize and re-render all pages when zoom changes
         */
        function resizeAndRerenderPages() {
            // Update container width based on new scale
            pdfDoc.getPage(1).then(function(page) {
                const viewport = page.getViewport({ scale: 1.0 });
                const pageWidth = viewport.width;
                
                // Clear all rendered pages
                for (let i = 1; i <= pdfDoc.numPages; i++) {
                    const pageContainer = document.getElementById(`page-container-${i}`);
                    if (pageContainer) {
                        // Adjust container height based on aspect ratio
                        const aspectRatio = viewport.height / viewport.width;
                        const newHeight = pageWidth * scale * aspectRatio;
                        
                        // Set both height and width for proper centering
                        pageContainer.style.height = `${newHeight}px`;
                        pageContainer.style.width = `${pageWidth * scale}px`;
                        
                        // Remove existing canvas
                        const existingCanvas = document.getElementById(`canvas-${i}`);
                        if (existingCanvas) {
                            existingCanvas.remove();
                        }
                    }
                }
                
                // Reset rendered pages tracking
                pagesRendered = {};
                
                // Update visible pages and render them
                updateVisiblePages();
                visiblePages.forEach(pageNum => {
                    renderPage(pageNum);
                });
                
                // Also render pages before and after visible pages
                visiblePages.forEach(pageNum => {
                    if (pageNum > 1) {
                        renderPage(pageNum - 1);
                    }
                    if (pageNum < pdfDoc.numPages) {
                        renderPage(pageNum + 1);
                    }
                });
            });
        }
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
    transition: background-color 0.2s, color 0.2s, transform 0.1s;
}

.back-btn:hover {
    background-color: #e9ecef;
    color: #212529;
    transform: scale(1.05);
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
    transition: color 0.2s, transform 0.1s;
}

.nav-btn:hover {
    color: #212529;
    transform: scale(1.1);
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
    transition: background-color 0.2s, transform 0.1s;
}

.purchase-btn:hover {
    background-color: #218838;
    color: #fff;
    transform: scale(1.02);
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
    transition: background-color 0.2s, color 0.2s, transform 0.1s;
}

.control-btn:hover {
    background-color: #e9ecef;
    color: #212529;
    transform: scale(1.05);
}

.control-btn.active {
    background-color: #0d6efd;
    color: white;
}

.control-btn.active:hover {
    background-color: #0b5ed7;
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
    width: 100%;
}

.pdf-page-container canvas {
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.15);
    image-rendering: -webkit-optimize-contrast;
    image-rendering: crisp-edges;
    transform: translateZ(0);
    margin: 0 auto;
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
    transition: background-color 0.2s, color 0.2s, transform 0.1s;
}

.mobile-nav-btn:hover {
    background-color: #e9ecef;
    color: #212529;
    transform: scale(1.05);
}

.mobile-page-display {
    margin: 0 1rem;
    font-weight: 500;
}

.zoom-indicator {
    position: fixed;
    bottom: 80px;
    left: 50%;
    transform: translateX(-50%);
    background-color: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: bold;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s, visibility 0.3s;
    z-index: 1000;
}

.zoom-indicator.visible {
    opacity: 1;
    visibility: visible;
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

/* Table of Contents Styles */
.toc-sidebar {
    position: fixed;
    top: 0;
    left: -300px;
    width: 300px;
    height: 100%;
    background-color: #fff;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
    z-index: 1040;
    transition: left 0.3s ease;
    display: flex;
    flex-direction: column;
}

.toc-sidebar.visible {
    left: 0;
}

.toc-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
}

.toc-header h5 {
    margin: 0;
}

.close-toc {
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    padding: 0.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: color 0.2s, transform 0.1s;
}

.close-toc:hover {
    color: #343a40;
    transform: scale(1.1);
}

.toc-content {
    flex: 1;
    overflow-y: auto;
    padding: 0.5rem 0.5rem;
}

.toc-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.toc-item {
    margin: 0;
    padding: 0;
}

.toc-item a {
    display: block;
    padding: 0.5rem 1rem;
    color: #212529;
    text-decoration: none;
    font-size: 0.925rem;
    border-left: 3px solid transparent;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.toc-item a:hover {
    background-color: #f8f9fa;
    border-left-color: #007bff;
}

.toc-item a.active {
    background-color: #e9f2ff;
    border-left-color: #007bff;
    font-weight: 500;
    color: #007bff;
}

.nested-toc {
    list-style: none;
    padding: 0;
    margin: 0;
}

.toc-loading,
.toc-empty {
    padding: 2rem 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    text-align: center;
}

.spinner-sm {
    width: 1.5rem;
    height: 1.5rem;
    border: 2px solid rgba(0, 0, 0, 0.1);
    border-left-color: #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 1rem;
}

.toc-empty i {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
}

@media (max-width: 767.98px) {
    .toc-sidebar {
        width: 260px;
    }
    
    body.toc-is-visible::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1035;
    }
}

#pdfPagesContainer {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    min-height: 100%;
    margin: 0 auto;
    max-width: 100%;
}

.pdf-page-container {
    position: relative;
    margin-bottom: 20px;
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.15);
    background-color: white;
    width: 100%;
    display: flex;
    justify-content: center;
}

.page-number-indicator {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background-color: rgba(0, 0, 0, 0.5);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    opacity: 0.7;
    z-index: 5;
}

.page-error {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: 100%;
    color: #dc3545;
    font-size: 0.9rem;
    text-align: center;
    padding: 20px;
}
</style>

<?php
include $footerPath;
?> 