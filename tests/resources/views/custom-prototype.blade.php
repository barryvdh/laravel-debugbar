<html>
<body>

<script>
    Array.prototype.customRemove = function(item) {
        const i = this.indexOf(item)
        if (i > -1) {
            this.splice(i, 1)
        }
        return this
    }
</script>
</body>
