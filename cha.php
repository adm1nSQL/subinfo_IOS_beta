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

        // 显示相应内容
        echo 'Sub_Link:     ' . "<a href={$url}>{$url}</a>" . '<br />';
        echo 'Upload:     ' . formatSizeUnits($upload) . '<br />';
        echo 'Download:      ' . formatSizeUnits($download) . '<br />';
        echo 'Remaining:      ' . formatSizeUnits($total - $download) .' <br />';
        echo 'Total:     ' . formatSizeUnits($total) . '<br />';
        echo "Expire:      " . ($data['e'] ? date("Y-m-d H:i:s", intval($data['e'])) : 'None') . "<br />";
        //echo 'TG_Channel: <a href=https://t.me/fffffx2>@fffffx2 ';
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
