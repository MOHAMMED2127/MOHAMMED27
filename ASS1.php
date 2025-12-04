<?php
$method = "AES-256-CBC";
$result = $error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // $text   = $_POST['text']       ?? '';
  //  $pass   = $_POST['passphrase'] ?? '';
   // $action = $_POST['action']     ?? '';

    // text
if (isset($_POST['text']) && $_POST['text'] !== null) {
    $text = $_POST['text'];
} else {
    $text = '';
}

// passphrase
if (isset($_POST['passphrase']) && $_POST['passphrase'] !== null) {
    $pass = $_POST['passphrase'];
} else {
    $pass = '';
}

// action
if (isset($_POST['action']) && $_POST['action'] !== null) {
    $action = $_POST['action'];
} else {
    $action = '';
}

    if ($pass === '') {
        $error = "Please enter a secret phrase.";
    } else {
        $key   = hash('sha256', $pass, true);
        $ivlen = openssl_cipher_iv_length($method);

        if ($action === 'encrypt') {
            $iv   = openssl_random_pseudo_bytes($ivlen);
            $raw  = openssl_encrypt($text, $method, $key, OPENSSL_RAW_DATA, $iv);
            if ($raw === false) {
                $error = "The encryption failed.";
            } else {
                $result = base64_encode($iv . $raw);
            }
        } elseif ($action === 'decrypt') {
            $bin = base64_decode($text, true);
            if ($bin === false || strlen($bin) <= $ivlen) {
                $error = "Invalid encrypted text.";
            } else {
                $iv   = substr($bin, 0, $ivlen);
                $raw  = substr($bin, $ivlen);
                $plain = openssl_decrypt($raw, $method, $key, OPENSSL_RAW_DATA, $iv);
                if ($plain === false) {
                    $error = "Decryption failed.";
                } else {
                    $result = $plain;
                }
            }
        } else {
            $error = "An unknown process.";
        }
    }
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>PHP AES</title>
</head>
<body>
<h2>PHP AES Encryption Assignment 1</h2>

<?php if ($error): ?>
    <div style="color:red"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post">
    <p>
        <label>Text:</label><br>
        <textarea name="text" rows="6" cols="60"><?= isset($_POST['text']) ? htmlspecialchars($_POST['text']) : '' ?></textarea>
    </p>
    <p>
        <label>The secret phrase:</label><br>
        <input type="" name="passphrase"
               value="<?= isset($_POST['passphrase']) ? htmlspecialchars($_POST['passphrase']) : '' ?>">
    </p>
    <p>
        <button type="submit" name="action" value="encrypt">Encrypt</button>
        <button type="submit" name="action" value="decrypt">Decrypt</button>
    </p>
</form>

<?php if ($result !== ''): ?>
    <h3>Result</h3>
    <pre><?= htmlspecialchars($result) ?></pre>
<?php endif; ?>
</body>
</html>
