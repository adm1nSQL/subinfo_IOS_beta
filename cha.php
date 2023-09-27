<?php
header('Content-Type: text/html; charset=utf-8');
$url = rawurldecode($_GET['sub']);
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
$body = substr($result, $header_size);

// Extract airport name from Content-Disposition header
$airport_name = '';
if (preg_match('/filename\*=UTF-8\'\'(.+)/', $header, $matches)) {
    $airport_name = urldecode($matches[1]);
}

// 从响应头中提取subscription_userinfo字段的值
$sub_info = '';
foreach (explode("\r\n", $header) as $header_item) {
    if (stripos($header_item, 'subscription-userinfo:') === 0) {
        $sub_info = trim(substr($header_item, strlen('subscription-userinfo:')));
        break;
    }
}

if (!empty($sub_info)) {
    $data = [];
    parse_str(str_replace([';', ' ', 'expire=', 'upload=', 'download=', 'total='], ['&', '', '&e=', '&u=', '&d=', '&t='], $sub_info), $data);

    if (isset($data['u']) && isset($data['d']) && isset($data['t'])) {
        $upload = $data['u'];
        $download = $data['d'];
        $total = $data['t'];

        // Output in HTML format
        echo '<html>';
        echo '<head><meta charset="UTF-8"><title>Subscription Info</title></head>';
        echo '<body>';
        echo '订阅链接:     <a href="' . $url . '">' . $url . '</a><br />';
        // Include the airport name in the output
        if (!empty($airport_name)) {
            echo '机场名: ' . $airport_name . '<br />';
        }
        echo '已上传:     ' . formatSizeUnits($upload) . '<br />';
        echo '已下载:      ' . formatSizeUnits($download) . '<br />';
        echo '总流量:     ' . formatSizeUnits($total) . '<br />';
        echo '剩余流量:      ' . formatSizeUnits($total - $download) .' <br />';
        echo '到期时间:      ' . ($data['e'] ? date("Y-m-d H:i:s", intval($data['e'])) : '未知') . '<br />';

        //echo 'TG_Channel: <a href=https://t.me/fffffx2>@fffffx2 ';
        echo '</body>';
        echo '</html>';
    } else {
        echo "Failed to get traffic information.";
    }
} else {
    echo "Failed to get subscription_userinfo from headers.";
}

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
