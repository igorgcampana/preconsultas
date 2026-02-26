<?php
/**
 * PROXY GEMINI API
 * Protege a chave da API e faz a requisição segura via cURL
 */

// Headers CORS para permitir chamada do frontend
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Responde imediatamente a requisições de preflight (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ----------------------------------------------------
// SUA CHAVE DA API GEMINI AQUI:
// ----------------------------------------------------
define('GEMINI_API_KEY', 'AIzaSyBlAAMdkHuDPnLXcq_xqoruB6DOUEpCDTY');

// Garante que é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'texto' => 'Método inválido. Use POST.']);
    exit();
}

// Intercepta e decodifica o payload JSON
$rawData = file_get_contents("php://input");
$body = json_decode($rawData, true);

if (!$body || empty($body['promptSistema']) || empty($body['promptUsuario']) || empty($body['modelo'])) {
    echo json_encode(['ok' => false, 'texto' => 'Payload inválido. Está faltando promptSistema, promptUsuario ou modelo.']);
    exit();
}

$modelo = $body['modelo'];
$promptSistema = $body['promptSistema'];
// promptUsuario vem como uma string contendo um array JSON das imagens base64
$promptUsuarioRaw = $body['promptUsuario'];

// Monta o array de partes para a requisição
$parts = [];

// 1. Adiciona o texto (promptSistema + prompt de instrução)
$parts[] = [
    'text' => $promptSistema . "\n\nAqui estão os exames anexados:"
];

// 2. Adiciona as imagens (inlineData)
$examesArray = json_decode($promptUsuarioRaw, true);
if (is_array($examesArray)) {
    foreach ($examesArray as $exame) {
        if (isset($exame['inlineData'])) {
            $parts[] = [
                'inlineData' => [
                    'mimeType' => $exame['inlineData']['mimeType'],
                    'data' => $exame['inlineData']['data']
                ]
            ];
        }
    }
}
else {
    // Fallback: se não for um array de objetos inlineData, envia como texto
    $parts[] = ['text' => $promptUsuarioRaw];
}

// Monta o payload final que a API Gemini exige
$geminiPayload = [
    'contents' => [
        [
            'parts' => $parts
        ]
    ]
];

// Monta a URL da API
$url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelo}:generateContent?key=" . GEMINI_API_KEY;

// Inicializa o cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($geminiPayload));

// Falha no SSL às vezes ocorre em hospedagens locais/compartilhadas. Em produção (Hostgator) recomendo tirar essa linha:
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

// Executa a requisição
$response = curl_exec($ch);
$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Trata erros do cURL
if ($response === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'texto' => 'Erro de conexão cURL: ' . $curlError]);
    exit();
}

// Processa a resposta do Gemini
$responseData = json_decode($response, true);

// Verifica status HTTP (ex: 429 = Quota Exceeded)
if ($httpStatus !== 200) {
    $errorMsg = isset($responseData['error']['message']) ? $responseData['error']['message'] : 'Erro desconhecido da API Gemini';
    // Repassa o status HTTP para o frontend poder identificar erro 429 adequadamente
    http_response_code($httpStatus);
    echo json_encode(['ok' => false, 'texto' => $errorMsg]);
    exit();
}

// Extrai o texto da resposta de sucesso
if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
    $textoExtraido = $responseData['candidates'][0]['content']['parts'][0]['text'];
    echo json_encode(['ok' => true, 'texto' => $textoExtraido]);
}
else {
    echo json_encode(['ok' => false, 'texto' => 'Falha ao extrair texto da resposta da IA. Formato inesperado.']);
}