<?php

echo "Fone Uploader\n";

// by fone or sum

// credit extractUrls -> https://gist.github.com/matthiasott/0ee80bcce3ef65c4d7eeabb739c954ba (Ts diddyblud)

function extractUrls( $string ) {
  
	preg_match_all("/(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:\/[^\"\'\s]*)?/uix", $string, $post_links);
  
	$post_links = array_unique( array_map( 'html_entity_decode', $post_links[0] ) );
  
	return array_values( $post_links );
}

function downloadAsset($ID, $Version) {
    $url = "https://assetdelivery.roblox.com/v1/asset/?id=$ID&version=$Version";

    $GoonMobile = file_get_contents($url);

    $IDx = rand(10000000,99999999);

    $RealFile = file_put_contents("filestuff/".$IDx, $GoonMobile);

    return $IDx;
}

$mainURL = "https://example.org/asset/"; // your website and access link

// WARNING: Place this script in your asset folder or it will NOT work correctly

$assetURL = ""; // i.e. (https://example.org/asset/$assetURL) [Just use as blank] This is for special cases

if (isset($_POST)) {
    $file = $_FILES["rbxmx"];

    if ($file["error"] === UPLOAD_ERR_OK) {
        $tmpName = $file["tmp_name"];
        $name = $file["name"];
        $size = $file["size"];
        $pi = pathinfo($name);
        $ext = $pi["extension"];
        $version = (int) $_POST["version"] ?? 1;

        if ($ext !== "rbxmx") {
            header("Location: /errors/403.php");
            exit;
        }

        if ($size > 5000000) {
            header("Location: /errors/403.php");
            exit;
        }

        $ID = uniqid(); // Generate a ID but you could also just use SQL like a little bitch who cares about organization FUCK YOU

        $FuckassNumber = rand(10000,99999);

        move_uploaded_file($tmpName, $assetURL.$name."-".$FuckassNumber);

        $filestuff = file_get_contents($mainURL. $assetURL . $name . "-" . $FuckassNumber);

        $urls = extractUrls($filestuff);

        for ($i = 0; $i < count($urls); $i++) {
            if ($i > 2) {
                $urls[$i - 3] = $urls[$i];
            }
        }

        foreach ($urls as $url) {
            if (!str_contains($url, "xsd")) {
                $ID = (int) filter_var($url, FILTER_SANITIZE_NUMBER_INT);
                $test = downloadAsset($ID, 1);
                $urlx = $mainURL.$assetURL.$test."</url></Content>";
                
                $filestuff = str_replace($url, $urlx, $filestuff);
            }
        }

        unlink($assetURL.$name."-".$FuckassNumber);

        file_put_contents($assetURL.$name."-".$FuckassNumber, $filestuff);

        header("Location: $mainURL$assetURL$name-$FuckassNumber");

        exit;
    } else {
        header("Location: /errors/403.php");
        exit;
    }
}

?>
