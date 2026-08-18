```javascript
// ==========================================
// DELETE CONFIRMATION
// ==========================================

function confirmDelete() {

    return confirm(
        "Are you sure you want to delete this complaint?"
    );

}


// ==========================================
// FORM VALIDATION
// ==========================================

function validateComplaintForm() {

    let subject =
        document.getElementById("subject").value.trim();

    let category =
        document.getElementById("category").value;

    let description =
        document.getElementById("description").value.trim();


    if (subject === "") {

        alert("Please enter a complaint subject.");

        return false;

    }


    if (category === "") {

        alert("Please select a category.");

        return false;

    }


    if (description === "") {

        alert("Please enter a description.");

        return false;

    }


    return true;

}
```
