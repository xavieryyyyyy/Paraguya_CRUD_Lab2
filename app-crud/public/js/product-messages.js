// Removes only the product success banner after five seconds. The controller already completed
// the database operation before the list page and this timer appear.

// Find the success banner displayed after adding, updating, or deleting a product.
// This script runs after the HTML is parsed because the layout loads it with defer.
const successMessage = document.querySelector('.success-message');

// Some product pages have no success banner. Check first to avoid an error.
if (successMessage) {
    // setTimeout waits before running this function. 5000 milliseconds = 5 seconds.
    // Only remove the visible banner; the saved database operation is unchanged.
    // Validation errors use different classes and are not removed by this script.
    setTimeout(function () {
        successMessage.remove();
    }, 5000);
}
