<!-- Document Viewer Modal Component -->
<div id="documentViewerModal" class="modal" style="display: none;">
    <div class="modal-content document-viewer-content">
        <div class="modal-header">
            <h2 class="modal-title" id="documentViewerTitle">Document Viewer</h2>
            <span class="close" onclick="closeDocumentViewer()">&times;</span>
        </div>
        <div class="modal-body">
            <div id="documentViewerContainer">
                <!-- Document will be loaded here -->
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDocumentViewer()">Close</button>
            <a id="downloadLink" href="#" class="btn btn-primary" download>
                <i class="fas fa-download"></i> Download
            </a>
        </div>
    </div>
</div>

<style>
    /* Modal Styles (extend existing modal styles from ViewAcc.blade.php) */
    .document-viewer-content {
        width: 90%;
        max-width: 1000px;
        max-height: 90vh;
    }

    #documentViewerContainer {
        height: 70vh;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }

    #documentViewerContainer iframe {
        width: 100%;
        height: 100%;
        border: none;
    }

    #documentViewerContainer img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .modal-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

<script>
    function openDocumentViewer(url, title, downloadUrl) {
        const modal = document.getElementById('documentViewerModal');
        const container = document.getElementById('documentViewerContainer');
        const titleElement = document.getElementById('documentViewerTitle');
        const downloadLink = document.getElementById('downloadLink');

        // Set title
        titleElement.textContent = title;

        // Set download link
        downloadLink.href = downloadUrl;

        // Determine file type and display accordingly
        const fileExtension = url.split('.').pop().toLowerCase();
        if (fileExtension === 'pdf') {
            container.innerHTML = `<iframe src="${url}" title="${title}"></iframe>`;
        } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
            container.innerHTML = `<img src="${url}" alt="${title}" />`;
        } else {
            container.innerHTML = `<p>Unsupported file type. <a href="${url}" target="_blank">Open in new tab</a></p>`;
        }

        // Show modal
        modal.style.display = 'block';
    }

    function closeDocumentViewer() {
        const modal = document.getElementById('documentViewerModal');
        modal.style.display = 'none';
        document.getElementById('documentViewerContainer').innerHTML = '';
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('documentViewerModal');
        if (event.target === modal) {
            closeDocumentViewer();
        }
    });
</script>