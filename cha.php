<?php
header('Content-Type: text/html; charset=utf-8');
$url = $_GET['sub'];
$headers = array(
    'User-Agent: ClashforWindows/0.18.1'
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header = substr($result, 0, $header_size);

// 截取 upload, download, total, expire 的值
preg_match_all('/upload=([0-9]+); download=([0-9]+); total=([0-9]+); expire=([0-9]+)/', $header, $matches);

// 显示相应内容
echo 'sub_link:   ' . $url. '<br />';
echo 'upload:   ' . formatSizeUnits($matches[1][0]) . '<br />';
echo 'downoad:   ' . formatSizeUnits($matches[2][0]) . '<br />';
echo 'balance:   ' . formatSizeUnits($matches[3][0] - $matches[2][0]) .' <br />';
echo 'total:   ' . formatSizeUnits($matches[3][0]) . '<br />';
if (empty($matches[4][0])) {
    $expire = 'None';
} else {
    $expire = date('Y/m/d H:i:s', $matches[4][0]);
}
echo 'expire: ' . $expire . '<br />';
echo '<a href=https://t.me/fffffx2>@fffffx2';

curl_close($ch);

function formatSizeUnits($bytes)
{
    if ($bytes >= 1099511627776) {
        $bytes = number_format($bytes / 1099511627776, 2) . ' TB';
    } elseif ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } else {
        $bytes = $bytes . 'B';
    }

    return $bytes;
}
?>
