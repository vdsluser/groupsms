<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS 그룹 메시지 보내기</title>
    <style>
        * { 
            box-sizing: border-box; 
            margin: 0;
            padding: 0;
        }

        body { 
            font-family: Arial, sans-serif; 
            margin: 0;
            padding: 15px;
            max-width: 1200px;
            margin: 0 auto;
        }

        h2 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        #inputArea { 
            width: 100%; 
            height: 200px; 
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
        }

        #result { 
            margin-top: 20px; 
        }

        .sms-link { 
            display: block; 
            margin: 5px 0; 
            padding: 12px;
            text-decoration: none; 
            color: #333;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: #f8f8f8;
        }

        .sms-link:hover { 
            background-color: #f0f0f0; 
        }

        #totalCount { 
            font-weight: bold; 
            margin-bottom: 15px;
            padding: 10px;
            background: #f0f0f0;
            border-radius: 4px;
        }

        .controls { 
            margin: 15px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        select { 
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: white;
        }

        .button { 
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .button:hover {
            background-color: #45a049;
        }

        #loading {
            display: none;
            margin-left: 10px;
            color: #666;
        }

        /* 모바일 반응형 스타일 */
        @media screen and (max-width: 768px) {
            body {
                padding: 10px;
            }

            h2 {
                font-size: 1.2rem;
            }

            .controls {
                flex-direction: column;
                align-items: stretch;
            }

            .button {
                width: 100%;
                padding: 12px;
                font-size: 1rem;
            }

            select {
                width: 100%;
                padding: 12px;
            }

            .sms-link {
                padding: 15px;
                font-size: 0.9rem;
            }

            #totalCount {
                font-size: 0.9rem;
            }

            #inputArea {
                height: 150px;
            }
        }

        /* 작은 모바일 화면 */
        @media screen and (max-width: 480px) {
            h2 {
                font-size: 1.1rem;
            }

            .button {
                font-size: 0.9rem;
            }

            .sms-link {
                font-size: 0.8rem;
            }
        }

        /* 다크 모드 지원 */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1a1a1a;
                color: #fff;
            }

            #inputArea {
                background-color: #2d2d2d;
                color: #fff;
                border-color: #444;
            }

            .sms-link {
                background-color: #2d2d2d;
                color: #fff;
                border-color: #444;
            }

            .sms-link:hover {
                background-color: #3d3d3d;
            }

            select {
                background-color: #2d2d2d;
                color: #fff;
                border-color: #444;
            }

            #totalCount {
                background-color: #2d2d2d;
            }

            .button {
                background-color: #45a049;
            }

            .button:hover {
                background-color: #3d8b41;
            }
        }
    </style>
</head>
<body>
    <h2>SMS 그룹 메시지 보내기</h2>
    <div class="controls">
        <button class="button" onclick="window.open('https://nodong.org/recall', '_blank')">국민의힘 탄핵 찬반 의원 명단</button>
        <button id="fetchContacts" class="button">반대 의원 연락처 추출</button>
        <button class="button" onclick="window.open('https://github.com/vdsluser/groupsms', '_blank')">깃허브 소스 코드</button>
        <span id="loading">로딩중...</span>
    </div>

    <textarea id="inputArea" placeholder="데이터를 붙여넣거나 '연락처 추출' 버튼을 클릭하세요"></textarea>
    
    <div class="controls">
        <label for="groupSize">그룹당 발송 인원:</label>
        <select id="groupSize">
            <option value="5">5명</option>
            <option value="10">10명</option>
            <option value="15">15명</option>
            <option value="20">20명</option>
            <option value="30" selected>30명</option>
            <option value="50">50명</option>
        </select>
    </div>

    <div id="result">
        <div id="totalCount"></div>
        <div id="smsLinks"></div>
    </div>

    <script>
        function processInput() {
            const text = document.getElementById('inputArea').value;
            const groupSize = parseInt(document.getElementById('groupSize').value);
            const lines = text.split('\n').filter(line => line.trim());
            const phonePattern = /\d{3}-\d{4}-\d{4}/;
            const validLines = [];

            lines.forEach(line => {
                const match = line.match(phonePattern);
                if (match) {
                    validLines.push(match[0]);
                }
            });

            const totalCount = validLines.length;
            document.getElementById('totalCount').textContent = 
                `총 발송 인원: ${totalCount}명 (${Math.ceil(totalCount/groupSize)}개 그룹)`;

            // SMS 링크 생성
            const smsLinksDiv = document.getElementById('smsLinks');
            smsLinksDiv.innerHTML = '';

            for (let i = 0; i < validLines.length; i += groupSize) {
                const phoneGroup = validLines.slice(i, i + groupSize);
                const phones = phoneGroup.join(',');
                const link = document.createElement('a');
                link.href = `sms:${phones}`;
                link.className = 'sms-link';
                link.textContent = `그룹 ${Math.ceil((i+1)/groupSize)}: ${i + 1}~${Math.min(i + groupSize, validLines.length)}번 연락처로 문자 보내기 (${phoneGroup.length}명)`;
                smsLinksDiv.appendChild(link);
            }
        }

        document.getElementById('fetchContacts').addEventListener('click', async function() {
            const loading = document.getElementById('loading');
            const inputArea = document.getElementById('inputArea');
            
            loading.style.display = 'inline';
            
            try {
                const response = await fetch('fetch_contacts.php');
                const data = await response.json();
                
                if (data.error) {
                    alert(data.error);
                    return;
                }
                
                inputArea.value = data.contacts.join('\n');
                processInput(); // 연락처 추출 후 자동으로 처리
            } catch (error) {
                alert('연락처를 가져오는 중 오류가 발생했습니다.');
                console.error(error);
            } finally {
                loading.style.display = 'none';
            }
        });

        document.getElementById('inputArea').addEventListener('input', processInput);
        document.getElementById('groupSize').addEventListener('change', processInput);
    </script>
</body>
</html>