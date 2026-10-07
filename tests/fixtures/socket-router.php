<?php

declare(strict_types=1);

/**
 * Router for the built-in PHP server the socket-backed tests run against.
 *
 * It exists because `MockHandler` hands back an in-memory body, where a single `read()` returns
 * everything asked for. A real socket does not: that difference is exactly what hid a truncation
 * bug in `HttpClient::fetchPublic`, so the guard against it must speak over a real socket.
 */
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$size = (int) ($_GET['size'] ?? 100000);

if ($path === '/drip') {
    // One byte at a time, with a pause between them: a transfer that never stalls long enough to
    // trip a per-read timeout, yet takes far longer than one.
    header('Content-Type: application/statuslist+jwt');
    $bytes = (int) ($_GET['bytes'] ?? 100);
    // The pause is deliberately SHORTER than the client's per-read timeout: every individual
    // read succeeds, so only a wall-clock deadline over the whole transfer can stop this.
    $pauseMicros = (int) ($_GET['pause'] ?? 100_000);
    for ($i = 0; $i < $bytes; $i++) {
        echo 'x';
        flush();
        usleep($pauseMicros);
    }
    return true;
}

if ($path === '/body') {
    header('Content-Type: application/statuslist+jwt');
    if (($_GET['declare'] ?? '1') === '1') {
        header('Content-Length: ' . $size);
    }
    // Written in chunks with flushes, so the client sees several packets rather than one.
    $chunk = str_repeat('x', 8192);
    $written = 0;
    while ($written < $size) {
        $piece = substr($chunk, 0, min(8192, $size - $written));
        echo $piece;
        $written += strlen($piece);
        flush();
    }
    return true;
}

if ($path === '/redirecting/.well-known/jwks.json') {
    // A key set that moves: following it would read a document from somewhere the caller never
    // configured.
    http_response_code(302);
    header('Location: /.well-known/jwks.json');
    return true;
}

if ($path === '/.well-known/jwks.json') {
    // The key set is a public document: it is served only to a request carrying no credential and
    // the SDK's branded User-Agent, so a test reading it through the SDK's own client can tell.
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (isset($_SERVER['HTTP_AUTHORIZATION']) || !str_starts_with($userAgent, 'proof-sdk-php/')) {
        http_response_code(400);
        echo 'credential or library user agent on a public read';
        return true;
    }
    header('Content-Type: application/json');
    echo '{"keys":[]}';
    return true;
}

http_response_code(404);
echo 'not found';
return true;
