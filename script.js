const form = document.getElementById('assessment-form');
const output = document.getElementById('output');

form.addEventListener('submit', function(event) {
    event.preventDefault();

    const photoInput = document.getElementById('photo-upload');
    const annotationInput = document.getElementById('annotation');

    // this is just to simulate a server response
    const assessmentData = {
        photo: photoInput.files[0].name,
        annotation: annotationInput.value
    };


    const response = `
        <p>Assessment submitted successfully:</p>
        <p><strong>Photo:</strong> ${assessmentData.photo}</p>
        <p><strong>Annotation:</strong> ${assessmentData.annotation}</p>
    `;

    output.innerHTML = response;

    const sameAddressCheckbox = document.getElementById('same-address');
    const affectedAddressContainer = document.getElementById('affected-address-container');

    sameAddressCheckbox.addEventListener('change', function() {
        if (this.checked) {
            affectedAddressContainer.style.display = 'none';
        } else {
            affectedAddressContainer.style.display = 'block';
        }
    });
});
