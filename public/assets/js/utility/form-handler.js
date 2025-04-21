function handleFormSubmission(formId, actionUrl, refreshPage = false) {
    // Select the form element
    const form = document.getElementById(formId);

    // Check if form exists
    if (!form) {
        console.error(`Form with ID '${formId}' not found`);
        return;
    }

    // Add an event listener for form submission
    form.addEventListener('submit', function(event) {
        event.preventDefault();  // Prevent the default form submission

        // Get form data
        const formData = new FormData(form);

        // Convert form data to a plain object
        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        // Make a POST request to the backend PHP script with custom headers
        axios.post(actionUrl, data, {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            console.log('Response:', response.data);
            
            // Show success toast
            showToast('Success', response.data.message || 'Operation completed successfully', 'success');
            
            // ✅ Hide the modal
            const modalElement = bootstrap.Modal.getInstance(form.closest('.modal'));
            if (modalElement) {
                modalElement.hide();
            }

            // ✅ Reset the form
            form.reset();

            // Optionally refresh the page after a slight delay
            if (refreshPage) {
                setTimeout(() => {
                    location.reload();
                }, 1500); // Delay before refresh (adjustable)
            }

            // Optionally, display the updated session value (if needed)
            if (response.data.success && response.data.data.status) {
                console.log("Updated session status:", response.data.data.status);
                // Update the UI or perform any other actions based on the updated session value
            }

            // Check if response contains a redirect URL
            if (response.data.success && response.data.data && response.data.data.redirect_url) {
                // Redirect to the specified URL after a short delay to allow toast to be seen
                setTimeout(() => {
                    window.location.href = response.data.data.redirect_url;
                }, 1500);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Display error message if available
            const errorMessage = error.response && error.response.data && error.response.data.message 
                ? error.response.data.message 
                : 'An error occurred. Please try again.';
            
            // Show error toast
            showToast('Error', errorMessage, 'danger');
        });
    });
}
