<?php
header('Content-Type: application/json');

function fetchContacts() {
    $url = "https://nodong.org/recall";
    $html = file_get_contents($url);
    
    if (!$html) {
        return ['error' => '데이터를 가져올 수 없습니다.'];
    }

    $contacts = [];
    
    // HTML에서 테이블 행을 추출
    preg_match_all('/<tr>.*?<td class="cate">.*?<span[^>]*>반대<\/span>.*?<td class="title">\s*<a[^>]*>([^<]+)<\/a>.*?<td class="m_no">([^<]+)<\/td>.*?<td class="m_no">.*?<td class="m_no">.*?<td class="m_no">(\d{3}-\d{4}-\d{4})/s', $html, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $name = trim($match[1]);
        $phone = trim($match[3]);
        $contacts[] = "$name : $phone";
    }
    
    return ['contacts' => $contacts];
}

echo json_encode(fetchContacts());
?> 