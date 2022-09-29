<div>
    <p>Basic view</p>

    @php
        $collector->addQuery(
            "SELECT a FROM b WHERE c = ? AND d = ? AND e = ?",
            ['$10', '$2y$10_DUMMY_BCRYPT_HASH', '$_$$_$$$_$2_$3'],
            0,
            $db
        );
    @endphp
</div>
