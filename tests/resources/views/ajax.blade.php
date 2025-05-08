<html>
<body>

<a href="#" id="ajax-link" onclick="loadAjax();return false;">Click me</a>
<div id="result">Waiting..</div>

<script>
    async function loadAjax() {
        try {
            const response = await fetch('/api/ping');
            if (!response.ok) {
                throw new Error(`Response status: ${response.status}`);
            }

            const json = await response.json();

            document.getElementById('result').innerText = json.status;
        } catch (error) {
            console.error(error.message);
        }
    }

</script>
</body>
