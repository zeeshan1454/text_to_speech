<?php
// text to speech api url : http://103.113.27.5:8723/txtosp/intts_lngv_txtosp.php?text=एक ट्वीट मे उन्होने यह भी कहा था कि उन्हे सनी के साथ काम करने मे कोई परेशानी नही है&lang=hi_female_v1
// Show ivr Files url : http://103.113.27.5:8723/txtosp/ivr_test_file/
// एक ट्वीट मे उन्होने यह भी कहा था कि उन्हे सनी के साथ काम करने मे कोई परेशानी नही है
// Optional parameter 
//  hi_male_v1 : Hindi (Male 1)
//  hi_female_v1 : Hindi (Female)
//  hi_male_v2 : Hindi (Male 2)
// Yes, sure. The system is retrieving the details.We found that claim has the status as InProgress.
// en_female_v1 : English (Female 1)
// en_female_v4 : English (Female 2)
// en_female_v6 : English (Female 3)
// en_female_v7 : English (Female 4)

  
    if(isset($_REQUEST['text']) && !empty($_REQUEST['text'])){
        $text = $_REQUEST['text'];
    } else {
        echo 'Text Required';
        exit();
    }
    if(isset($_REQUEST['lang']) && !empty($_REQUEST['lang'])){
        $lang = $_REQUEST['lang'];
    } else {
        $lang = 'hi_female_v1';
    }
    $currentDateTime = date("Y-m-d H:i:s");

    $apiUrl = 'https://ivrapi.indiantts.in/tts';
    $params = [
        'type' => 'indiantts',
        'text' => $text,
        'api_key' => '101200b0-2710-11ef-b58f-bd77d76bd7b6', 
        'user_id' => '190495',
        'action' => 'play',
        'numeric' => 'hcurrency',
        'lang' => $lang,
        'samplerate' => '8000',
        'ver' => '3'
    ];
    
    $queryString = http_build_query($params);
    
    $finalUrl = $apiUrl . '?' . $queryString;
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $finalUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    $response = curl_exec($ch);
    
    $date = date("Y-m-d_H-i-s");
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    } else {
        if (!file_exists('ivr_test_file')) {
            mkdir('ivr_test_file', 0777, true);
        }
        $file_name = $date . '.wav';
        $filePath = 'ivr_test_file/' . $date . '.wav';
        file_put_contents($filePath, $response);
        echo 'Audio file has been saved as ' . $filePath;
    }
curl_close($ch);

?>