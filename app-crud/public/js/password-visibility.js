// Adds a separate Show/Hide button to each styled password input. Changes the input type in the
// browser without changing its value, field name, authentication, or hashing.

// Find password fields inside the styled wrappers on Login, Register, and Reset Password.
// Each field gets its own button so password and confirmation can be viewed separately.
const passwordInputs = document.querySelectorAll('.auth-input input[type="password"]');

passwordInputs.forEach(function (input) {
    const wrapper = input.parentElement;
    wrapper.classList.add('has-password-toggle');

    // Use a real button for keyboard access. type="button" prevents form submission.
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'password-toggle';
    button.textContent = 'Show';
    button.setAttribute('aria-controls', input.id);
    button.setAttribute('aria-pressed', 'false');

    // Include the field label so assistive tools distinguish the two password inputs.
    const label = document.querySelector('label[for="' + input.id + '"]');
    const fieldName = label ? label.textContent.trim().toLowerCase() : 'password';
    button.setAttribute('aria-label', 'Show ' + fieldName);

    button.addEventListener('click', function () {
        const showPassword = input.type === 'password';
        input.type = showPassword ? 'text' : 'password';
        button.textContent = showPassword ? 'Hide' : 'Show';
        button.setAttribute('aria-pressed', String(showPassword));
        button.setAttribute('aria-label', (showPassword ? 'Hide ' : 'Show ') + fieldName);
    });

    // Append the control without changing the field's name, value, or autocomplete.
    wrapper.appendChild(button);
});
