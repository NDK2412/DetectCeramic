document.getElementById("upload-form").addEventListener("submit", async (event) => {
    event.preventDefault();

    const fileInput = document.getElementById("image-input");
    const formData = new FormData();
    formData.append("image", fileInput.files[0]);

    try {
        const response = await fetch("/api/predict", {
            method: "POST",
            body: formData,
        });

        const result = await response.json();
        if (result.error) {
            document.getElementById("result").innerText = `Error: ${result.error}`;
        } else {
            document.getElementById("result").innerText = `Prediction: ${result.class} | Confidence: ${result.confidence}`;
        }
    } catch (error) {
        console.error("Error:", error);
        document.getElementById("result").innerText = "An error occurred while predicting.";
    }
});
